<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $admin = AdminUser::query()->where('username', $request->string('username'))->first();

        if (! $admin || ! Hash::check($request->string('password'), $admin->password)) {
            return $this->error('Username atau password tidak valid.', null, 401);
        }

        $admin->tokens()->delete();
        $admin->forceFill(['last_login_at' => now()])->save();

        $token = $admin->createToken('admin-api')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'admin' => [
                'id' => $admin->id,
                'username' => $admin->username,
            ],
        ], 'Login admin berhasil.');
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->success(null, 'Logout berhasil.');
    }
}
