<?php

namespace Tests\Feature;

use App\Models\EventSetting;
use App\Models\Lokasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_vote_once_per_session(): void
    {
        EventSetting::query()->create([
            'nama_event' => 'Bukber 2026',
            'is_voting_open' => true,
            'deadline_voting' => now()->addDays(1),
            'is_registration_open' => true,
        ]);

        $lokasi = Lokasi::factory()->create();

        $first = $this->postJson('/api/v1/voting', [
            'lokasi_id' => $lokasi->id,
            'voter_name' => 'Ahmad',
            'session_token' => 'session-1',
        ]);

        $first->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.lokasi_id', $lokasi->id);

        $second = $this->postJson('/api/v1/voting', [
            'lokasi_id' => $lokasi->id,
            'voter_name' => 'Ahmad',
            'session_token' => 'session-1',
        ]);

        $second->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_voting_index_returns_overview(): void
    {
        EventSetting::query()->create([
            'nama_event' => 'Bukber 2026',
            'is_voting_open' => true,
            'deadline_voting' => now()->addDays(1),
            'is_registration_open' => true,
        ]);
        Lokasi::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/voting');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'is_voting_open',
                    'deadline',
                    'lokasi',
                    'total_voters',
                ],
            ]);
    }
}
