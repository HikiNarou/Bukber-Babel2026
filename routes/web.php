<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckAuth;

Route::get('/', [RegistrationController::class, 'create']);
Route::post('/register-bukber', [RegistrationController::class, 'store']);
Route::get('/success', [RegistrationController::class, 'success']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(CheckAuth::class);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
