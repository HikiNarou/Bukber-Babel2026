<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BroadcastService
{
    public function broadcast(string $message): array
    {
        // MVP: log broadcast payload; in production this can dispatch queue jobs for email/WA gateways.
        Log::info('Bukber broadcast', [
            'message' => $message,
            'sent_at' => now()->toIso8601String(),
        ]);

        return [
            'message' => $message,
            'status' => 'queued',
        ];
    }
}
