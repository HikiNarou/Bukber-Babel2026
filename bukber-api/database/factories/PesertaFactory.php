<?php

namespace Database\Factories;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peserta>
 */
class PesertaFactory extends Factory
{
    protected $model = Peserta::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'nama_lengkap' => $this->faker->name(),
            'minggu' => $this->faker->numberBetween(1, 4),
            'budget_per_orang' => $this->faker->numberBetween(50000, 300000),
            'catatan' => $this->faker->optional()->sentence(),
            'device_fingerprint' => hash('sha256', $this->faker->uuid()),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
