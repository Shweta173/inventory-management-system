<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:255', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Full name is required.',
            'name.min'               => 'Name must be at least 2 characters.',
            'name.max'               => 'Name must not exceed 100 characters.',
            'name.regex'             => 'Name may only contain letters and spaces — no numbers or special characters.',
            'email.required'         => 'Email address is required.',
            'email.email'            => 'Please enter a valid email address (e.g. user@example.com).',
            'email.unique'           => 'An account with this email already exists.',
            'password.required'      => 'Password is required.',
            'password.min'           => 'Password must be at least 8 characters.',
            'password.confirmed'     => 'Password confirmation does not match.',
        ];
    }
}
