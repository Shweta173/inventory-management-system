<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request);

        if ($result['success']) {
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors([$result['field'] => $result['msg']])
            ->withInput($request->only('email'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request);

        if ($result['success']) {
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([$result['field'] => $result['msg']])
            ->withInput($request->only('name', 'email'));
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return redirect()->route('login');
    }
}
