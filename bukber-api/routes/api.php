<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\TanggalController as AdminTanggalController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\RegistrasiController;
use App\Http\Controllers\Api\TanggalController;
use App\Http\Controllers\Api\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);

Route::prefix('v1')->group(function (): void {
    Route::post('/registrasi', [RegistrasiController::class, 'store'])
        ->middleware('throttle:registrasi');
    Route::get('/registrasi', [RegistrasiController::class, 'index']);
    Route::get('/registrasi/{uuid}', [RegistrasiController::class, 'show']);
    Route::put('/registrasi/{uuid}', [RegistrasiController::class, 'update']);

    Route::prefix('dashboard')->group(function (): void {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/chart/hari', [DashboardController::class, 'chartHari']);
        Route::get('/chart/minggu', [DashboardController::class, 'chartMinggu']);
        Route::get('/chart/budget', [DashboardController::class, 'chartBudget']);
        Route::get('/responden', [DashboardController::class, 'responden']);
    });

    Route::get('/lokasi', [LokasiController::class, 'index']);
    Route::get('/lokasi/search', [LokasiController::class, 'search']);
    Route::post('/lokasi', [LokasiController::class, 'store']);

    Route::get('/voting', [VotingController::class, 'index']);
    Route::post('/voting', [VotingController::class, 'store'])
        ->middleware('throttle:voting');
    Route::get('/voting/hasil', [VotingController::class, 'hasil']);

    Route::get('/tanggal', [TanggalController::class, 'show']);

    Route::prefix('admin')->group(function (): void {
        Route::post('/login', [AdminAuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/peserta', [AdminPesertaController::class, 'index']);
            Route::delete('/peserta/{id}', [AdminPesertaController::class, 'destroy']);
            Route::get('/settings', [AdminSettingsController::class, 'show']);
            Route::put('/settings', [AdminSettingsController::class, 'update']);
            Route::post('/tanggal', [AdminTanggalController::class, 'store']);
            Route::put('/tanggal', [AdminTanggalController::class, 'update']);
            Route::post('/broadcast', [AdminSettingsController::class, 'broadcast']);
        });
    });
});
