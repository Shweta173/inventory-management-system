<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(Request $request): array
    {
        $credentials = [
            'email'    => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return ['success' => true];
        }

        return [
            'success' => false,
            'field'   => 'email',
            'msg'     => 'These credentials do not match our records.',
        ];
    }

    public function register(Request $request): array
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => trim($request->input('name')),
                'email'    => strtolower(trim($request->input('email'))),
                'password' => $request->input('password'),
            ]);

            Auth::login($user);
            DB::commit();

            return ['success' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('AuthService::register - ' . $e->getMessage());
            return [
                'success' => false,
                'field'   => 'email',
                'msg'     => 'Registration failed. Please try again.',
            ];
        }
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
