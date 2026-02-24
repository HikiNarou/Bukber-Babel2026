<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PesertaResource;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);
        $query = Peserta::query()->with(['hari', 'lokasi'])->latest();

        if ($search = trim($request->string('q')->toString())) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        return $this->paginated(
            $paginator,
            PesertaResource::collection($paginator->getCollection()),
            'Daftar peserta admin berhasil diambil.'
        );
    }

    public function destroy(int $id)
    {
        $peserta = Peserta::query()->find($id);

        if (! $peserta) {
            return $this->error('Peserta tidak ditemukan.', null, 404);
        }

        $peserta->delete();

        foreach ([
            'dashboard_stats',
            'chart_hari',
            'chart_minggu',
            'chart_budget',
            'responden_latest',
            'list_lokasi',
            'voting_overview',
            'tanggal_final',
        ] as $key) {
            Cache::forget($key);
        }

        return $this->success(null, 'Peserta berhasil dihapus.');
    }
}
