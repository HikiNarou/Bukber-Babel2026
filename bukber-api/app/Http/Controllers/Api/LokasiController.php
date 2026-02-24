<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLokasiRequest;
use App\Http\Resources\LokasiResource;
use App\Models\Lokasi;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);
        $paginator = Lokasi::query()
            ->withCount('votes')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            $paginator,
            LokasiResource::collection($paginator->getCollection()),
            'Daftar lokasi berhasil diambil.'
        );
    }

    public function search(Request $request)
    {
        $keyword = trim($request->string('q')->toString());

        if (mb_strlen($keyword) < 2) {
            return $this->error('Kata kunci minimal 2 karakter.', null, 422);
        }

        $localMatches = Lokasi::query()
            ->select(['id', 'nama_tempat', 'alamat', 'latitude', 'longitude'])
            ->where('nama_tempat', 'like', "%{$keyword}%")
            ->orWhere('alamat', 'like', "%{$keyword}%")
            ->limit(8)
            ->get()
            ->map(static fn (Lokasi $lokasi): array => [
                'id' => $lokasi->id,
                'nama_tempat' => $lokasi->nama_tempat,
                'alamat' => $lokasi->alamat,
                'latitude' => $lokasi->latitude,
                'longitude' => $lokasi->longitude,
                'source' => 'database',
            ]);

        $remoteCacheKey = 'lokasi_search:'.md5($keyword);
        $remoteMatches = Cache::remember($remoteCacheKey, 600, function () use ($keyword): array {
            try {
                $response = Http::timeout(8)
                    ->retry(2, 150)
                    ->withHeaders(['User-Agent' => 'BukberMagangBSB/1.0'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $keyword,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'limit' => 5,
                    ])
                    ->throw()
                    ->json();
            } catch (RequestException) {
                return [];
            }

            return collect($response)->map(static function (array $item): array {
                return [
                    'id' => null,
                    'nama_tempat' => $item['display_name'] ?? 'Lokasi',
                    'alamat' => $item['display_name'] ?? null,
                    'latitude' => isset($item['lat']) ? (float) $item['lat'] : null,
                    'longitude' => isset($item['lon']) ? (float) $item['lon'] : null,
                    'source' => 'nominatim',
                ];
            })->all();
        });

        return $this->success(
            collect($localMatches)->merge($remoteMatches)->take(10)->values(),
            'Hasil pencarian lokasi berhasil diambil.'
        );
    }

    public function store(StoreLokasiRequest $request)
    {
        $lokasi = Lokasi::query()->create($request->validated());

        Cache::forget('list_lokasi');

        return $this->success(
            LokasiResource::make($lokasi),
            'Lokasi berhasil ditambahkan.',
            201
        );
    }
}
