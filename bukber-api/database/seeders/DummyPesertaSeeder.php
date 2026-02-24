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
            $hari = collect(PesertaHari::HARI_LIST)
                ->shuffle()
                ->take(fake()->numberBetween(2, 4));

            foreach ($hari as $day) {
                $item->hari()->create(['hari' => $day]);
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
