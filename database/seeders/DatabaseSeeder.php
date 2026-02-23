<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Ahmad Fauzi',      [1, 2],    ['Jumat', 'Sabtu'],           150000, 'bisa'],
            ['Sarah Putri',      [2, 3],    ['Sabtu'],                    200000, 'bisa'],
            ['Budi Santoso',     [1],       ['Jumat'],                    100000, 'bisa'],
            ['Kevin Ramadhan',   [3, 4],    ['Sabtu', 'Minggu'],          250000, 'mungkin'],
            ['Citra Amalia',     [2],       ['Jumat', 'Sabtu'],           175000, 'bisa'],
            ['Dian Purnama',     [1, 2, 3], ['Senin', 'Jumat'],           120000, 'bisa'],
            ['Eka Saputra',      [4],       ['Sabtu'],                    300000, 'bisa'],
            ['Fajar Hidayat',    [2, 3],    ['Rabu', 'Jumat'],            160000, 'mungkin'],
            ['Gita Nuraini',     [1],       ['Jumat', 'Sabtu', 'Minggu'], 180000, 'bisa'],
            ['Hendra Wijaya',    [3],       ['Sabtu'],                    220000, 'bisa'],
            ['Indah Permata',    [2, 4],    ['Jumat'],                    140000, 'bisa'],
            ['Joko Susanto',     [1, 3],    ['Sabtu', 'Minggu'],          200000, 'tidak'],
            ['Kartika Dewi',     [2],       ['Jumat', 'Sabtu'],           190000, 'bisa'],
            ['Luthfi Hakim',     [4],       ['Sabtu'],                    280000, 'mungkin'],
            ['Maya Anggraini',   [1, 2],    ['Jumat'],                    130000, 'bisa'],
            ['Nanda Pratama',    [3, 4],    ['Jumat', 'Sabtu'],           250000, 'bisa'],
            ['Oscar Mahendra',   [2],       ['Sabtu'],                    170000, 'bisa'],
            ['Putri Rahayu',     [1],       ['Jumat', 'Sabtu'],           155000, 'bisa'],
            ['Qori Aisyah',      [3],       ['Jumat'],                    200000, 'tidak'],
            ['Reza Firmansyah',  [2, 3],    ['Sabtu', 'Minggu'],          240000, 'bisa'],
            ['Siti Nurhaliza',   [1, 2],    ['Jumat'],                    125000, 'bisa'],
            ['Taufik Ismail',    [4],       ['Sabtu'],                    300000, 'mungkin'],
            ['Umi Kulsum',       [2],       ['Jumat', 'Sabtu'],           160000, 'bisa'],
            ['Vina Oktavia',     [3, 4],    ['Jumat'],                    210000, 'bisa'],
            ['Wawan Setiawan',   [1, 2, 3], ['Sabtu'],                    180000, 'bisa'],
        ];

        foreach ($data as [$nama, $weeks, $days, $budget, $status]) {
            Registration::create([
                'nama_lengkap' => $nama,
                'weeks'        => $weeks,
                'days'         => $days,
                'budget'       => $budget,
                'status'       => $status,
            ]);
        }
    }
}
