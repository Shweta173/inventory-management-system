@extends('layouts.auth')

@section('title', 'Register')

@section('form')

<h2 class="auth-card-title">Create account</h2>
<p class="auth-card-sub">Set up your admin account to get started.</p>

{{-- Server-side error summary --}}
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

<form method="POST" action="{{ route('register.post') }}" novalidate id="registerForm">
    @csrf

    {{-- Full Name --}}
    <div class="mb-3">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name') }}"
               placeholder="e.g. John Smith"
               autocomplete="name"
               required />
        @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @else
            <div class="form-text" style="font-size:.75rem;color:#9ca3af;margin-top:.3rem;">
                Letters and spaces only — no numbers or special characters.
            </div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email') }}"
               placeholder="e.g. admin@example.com"
               autocomplete="email"
               required />
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
        <div class="pw-wrap">
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   placeholder="Minimum 8 characters"
                   autocomplete="new-password"
                   required />
            <button type="button" class="pw-toggle" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @else
            <div class="form-text" style="font-size:.75rem;color:#9ca3af;margin-top:.3rem;">
                At least 8 characters.
            </div>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
        <div class="pw-wrap">
            <input type="password"
                   class="form-control"
                   id="password_confirmation"
                   name="password_confirmation"
                   placeholder="Re-enter your password"
                   autocomplete="new-password"
                   required />
            <button type="button" class="pw-toggle" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-auth" id="registerBtn">
        Create Account
    </button>
</form>

<div class="auth-link-row">
    Already have an account? <a href="{{ route('login') }}">Sign in here</a>
</div>

<script>
// Client-side name validation: block numbers and special characters in real time
document.getElementById('name').addEventListener('input', function () {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

document.getElementById('registerForm').addEventListener('submit', function () {
    var btn = document.getElementById('registerBtn');
    btn.disabled = true;
    btn.textContent = 'Creating account…';
});
</script>

@endsection
