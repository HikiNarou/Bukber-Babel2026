<?php

namespace Tests\Unit;

use App\Models\EventSetting;
use App\Models\Lokasi;
use App\Services\VotingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VotingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_vote_creates_vote_record(): void
    {
        EventSetting::query()->create([
            'nama_event' => 'Bukber',
            'is_registration_open' => true,
            'is_voting_open' => true,
            'deadline_voting' => now()->addDay(),
        ]);
        $lokasi = Lokasi::factory()->create();

        $vote = app(VotingService::class)->submitVote([
            'lokasi_id' => $lokasi->id,
            'voter_name' => 'Rizky',
        ], 'session-xyz', '127.0.0.1');

        $this->assertSame('Rizky', $vote->voter_name);
        $this->assertDatabaseHas('votes', [
            'session_token' => 'session-xyz',
            'lokasi_id' => $lokasi->id,
        ]);
    }

    public function test_submit_vote_throws_for_duplicate_session(): void
    {
        $this->expectException(ValidationException::class);

        EventSetting::query()->create([
            'nama_event' => 'Bukber',
            'is_registration_open' => true,
            'is_voting_open' => true,
            'deadline_voting' => now()->addDay(),
        ]);
        $lokasi = Lokasi::factory()->create();

        $service = app(VotingService::class);
        $service->submitVote([
            'lokasi_id' => $lokasi->id,
            'voter_name' => 'Rizky',
        ], 'dup-session', '127.0.0.1');

        $service->submitVote([
            'lokasi_id' => $lokasi->id,
            'voter_name' => 'Rizky',
        ], 'dup-session', '127.0.0.1');
    }
}
