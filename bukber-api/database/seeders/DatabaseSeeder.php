<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            EventSettingSeeder::class,
        ]);

        if (app()->environment(['local', 'development', 'testing'])) {
            $this->call(DummyPesertaSeeder::class);
        }
    }
}
