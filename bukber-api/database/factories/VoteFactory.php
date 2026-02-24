<?php

namespace Database\Factories;

use App\Models\Lokasi;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    protected $model = Vote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lokasi_id' => Lokasi::factory(),
            'voter_name' => $this->faker->name(),
            'voter_ip' => $this->faker->ipv4(),
            'session_token' => hash('sha256', Str::uuid()->toString()),
        ];
    }
}
