<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::query()->updateOrCreate(
            ['username' => env('ADMIN_DEFAULT_USERNAME', 'admin')],
            [
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'admin12345')),
            ]
        );
    }
}
