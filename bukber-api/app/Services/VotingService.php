<?php

namespace App\Services;

use App\Models\EventSetting;
use App\Models\Lokasi;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class VotingService
{
    public function getOverview(): array
    {
        return Cache::remember('voting_overview', 30, function (): array {
            $settings = EventSetting::singleton();
            $deadline = $settings->deadline_voting?->toIso8601String();
            $isOpenByDeadline = $settings->deadline_voting === null || now()->lte($settings->deadline_voting);
            $isVotingOpen = $settings->is_voting_open && $isOpenByDeadline;

            $lokasi = Lokasi::query()
                ->withCount('votes')
                ->orderByDesc('votes_count')
                ->orderBy('nama_tempat')
                ->get();

            $totalVoters = (int) $lokasi->sum('votes_count');

            return [
                'is_voting_open' => $isVotingOpen,
                'deadline' => $deadline,
                'lokasi' => $lokasi->map(static function (Lokasi $item) use ($totalVoters): array {
                    $votes = (int) $item->votes_count;

                    return [
                        'id' => $item->id,
                        'nama_tempat' => $item->nama_tempat,
                        'alamat' => $item->alamat,
                        'latitude' => $item->latitude,
                        'longitude' => $item->longitude,
                        'total_votes' => $votes,
                        'percentage' => $totalVoters > 0 ? round(($votes / $totalVoters) * 100, 1) : 0.0,
                    ];
                })->all(),
                'total_voters' => $totalVoters,
            ];
        });
    }

    public function submitVote(array $payload, string $sessionToken, ?string $ipAddress): Vote
    {
        $this->ensureVotingOpen();

        if (Vote::query()->where('session_token', $sessionToken)->exists()) {
            throw ValidationException::withMessages([
                'vote' => ['Session ini sudah pernah melakukan voting.'],
            ]);
        }

        if ($ipAddress && Vote::query()->where('voter_ip', $ipAddress)->where('voter_name', $payload['voter_name'])->exists()) {
            throw ValidationException::withMessages([
                'vote' => ['Nama yang sama dari IP ini sudah pernah melakukan voting.'],
            ]);
        }

        $lokasi = Lokasi::query()->find($payload['lokasi_id']);

        if (! $lokasi) {
            throw ValidationException::withMessages([
                'lokasi_id' => ['Lokasi tidak ditemukan.'],
            ]);
        }

        $vote = Vote::query()->create([
            'lokasi_id' => $lokasi->id,
            'voter_name' => trim($payload['voter_name']),
            'voter_ip' => $ipAddress,
            'session_token' => $sessionToken,
        ]);

        foreach (['voting_overview', 'tanggal_final'] as $key) {
            Cache::forget($key);
        }

        return $vote->load('lokasi');
    }

    private function ensureVotingOpen(): void
    {
        $settings = EventSetting::singleton();

        if (! $settings->is_voting_open) {
            throw ValidationException::withMessages([
                'voting' => ['Voting belum dibuka oleh admin.'],
            ]);
        }

        if ($settings->deadline_voting && now()->greaterThan($settings->deadline_voting)) {
            throw ValidationException::withMessages([
                'voting' => ['Voting sudah ditutup karena melewati deadline.'],
            ]);
        }
    }
}
