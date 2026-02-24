<?php

namespace Database\Factories;

use App\Models\Lokasi;
use App\Models\Peserta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lokasi>
 */
class LokasiFactory extends Factory
{
    protected $model = Lokasi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peserta_id' => Peserta::factory(),
            'nama_tempat' => $this->faker->company().' '.$this->faker->randomElement(['Resto', 'Cafe', 'Warung']),
            'alamat' => $this->faker->address(),
            'latitude' => $this->faker->latitude(-7, -6),
            'longitude' => $this->faker->longitude(106, 107),
            'google_place_id' => $this->faker->optional()->regexify('[A-Za-z0-9]{20}'),
        ];
    }
}
