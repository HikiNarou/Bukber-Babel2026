<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\EventSetting;
use App\Services\BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function __construct(private readonly BroadcastService $broadcastService) {}

    public function show()
    {
        return $this->success(EventSetting::singleton(), 'Pengaturan event berhasil diambil.');
    }

    public function update(UpdateSettingsRequest $request)
    {
        $settings = EventSetting::singleton();
        $settings->fill($request->validated());
        $settings->save();

        Cache::forget('voting_overview');

        return $this->success($settings, 'Pengaturan event berhasil diperbarui.');
    }

    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $payload = $this->broadcastService->broadcast($validated['message']);

        return $this->success($payload, 'Broadcast berhasil diproses.');
    }
}
