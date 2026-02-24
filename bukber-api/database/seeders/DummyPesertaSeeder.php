<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use App\Models\Peserta;
use App\Models\PesertaHari;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyPesertaSeeder extends Seeder
{
    public function run(): void
    {
        $peserta = Peserta::factory()->count(25)->create();

        foreach ($peserta as $item) {
            $mingguList = collect(PesertaHari::MINGGU_LIST)
                ->shuffle()
                ->take(fake()->numberBetween(1, 3))
                ->sort()
                ->values();

            $item->update([
                'minggu' => (int) ($mingguList->first() ?? 1),
            ]);

            foreach ($mingguList as $minggu) {
                $hari = collect(PesertaHari::HARI_LIST)
                    ->shuffle()
                    ->take(fake()->numberBetween(1, 3));

                foreach ($hari as $day) {
                    $item->hari()->create([
                        'minggu' => $minggu,
                        'hari' => $day,
                    ]);
                }
            }

            Lokasi::factory()->create([
                'peserta_id' => $item->id,
            ]);
        }

        $lokasiIds = Lokasi::query()->pluck('id')->all();

        if ($lokasiIds === []) {
            return;
        }

        for ($i = 0; $i < 40; $i++) {
            Vote::query()->create([
                'lokasi_id' => fake()->randomElement($lokasiIds),
                'voter_name' => fake()->name(),
                'voter_ip' => fake()->ipv4(),
                'session_token' => hash('sha256', Str::uuid()->toString()),
            ]);
        }
    }
}
