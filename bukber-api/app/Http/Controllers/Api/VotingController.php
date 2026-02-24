<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Resources\VotingResource;
use App\Services\VotingService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VotingController extends Controller
{
    public function __construct(private readonly VotingService $votingService) {}

    public function index()
    {
        return $this->success($this->votingService->getOverview(), 'Data voting berhasil diambil.');
    }

    public function store(StoreVoteRequest $request)
    {
        $sessionToken = $request->string('session_token')->toString();

        if ($sessionToken === '') {
            $sessionToken = $request->cookie('bukber_vote_token')
                ?: hash('sha256', $request->ip().$request->userAgent().Str::lower($request->string('voter_name')));
        }

        try {
            $vote = $this->votingService->submitVote(
                $request->validated(),
                $sessionToken,
                $request->ip()
            );
        } catch (ValidationException $exception) {
            return $this->error('Validasi gagal', $exception->errors(), 422);
        }

        return $this->success(VotingResource::make($vote), 'Vote berhasil disimpan.', 201)
            ->cookie(
                cookie('bukber_vote_token', $sessionToken, 60 * 24 * 30, sameSite: 'lax')
            );
    }

    public function hasil()
    {
        $overview = $this->votingService->getOverview();
        $winner = collect($overview['lokasi'])->sortByDesc('total_votes')->first();
        $overview['winner'] = $winner;

        return $this->success($overview, 'Hasil voting berhasil diambil.');
    }
}
