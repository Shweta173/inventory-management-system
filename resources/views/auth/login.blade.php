@extends('layouts.auth')

@section('title', 'Login')

@section('form')

<h2 class="auth-card-title">Welcome back</h2>
<p class="auth-card-sub">Sign in to your admin account.</p>

{{-- Server-side error alert --}}
@if ($errors->any())
    <div class="auth-alert">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.post') }}" novalidate id="loginForm">
    @csrf

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email') }}"
               placeholder="admin@example.com"
               autocomplete="email"
               required />
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
        <div class="pw-wrap">
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   placeholder="Enter your password"
                   autocomplete="current-password"
                   required />
            <button type="button" class="pw-toggle" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-auth" id="loginBtn">
        Sign In
    </button>
</form>

<div class="auth-link-row">
    Don't have an account? <a href="{{ route('register') }}">Register here</a>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function () {
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = 'Signing in…';
});
</script>

@endsection
