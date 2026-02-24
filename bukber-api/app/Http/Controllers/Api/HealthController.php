<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HealthController extends Controller
{
    public function __invoke()
    {
        return $this->success([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ], 'Service is healthy.');
    }
}
