<?php

namespace Database\Seeders;

use App\Models\EventSetting;
use Illuminate\Database\Seeder;

class EventSettingSeeder extends Seeder
{
    public function run(): void
    {
        EventSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'nama_event' => 'Bukber Magang BSB 2026',
                'deadline_registrasi' => now()->addDays(10),
                'deadline_voting' => now()->addDays(14),
                'is_registration_open' => true,
                'is_voting_open' => true,
            ]
        );
    }
}
