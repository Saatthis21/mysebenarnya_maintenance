<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Auth::id() only works for the default 'web' guard.
        // Users in this app authenticate via named guards (public/mcmc/agency),
        // so we must check each guard to get the real user ID.
        $user = Auth::guard('public')->user()
             ?? Auth::guard('mcmc')->user()
             ?? Auth::guard('agency')->user();

        $userId = $user?->id;

        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'contact_number'   => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'string'],
            'password'         => ['nullable', 'confirmed', Password::defaults()],
            'profile_picture'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Full name is required.',
            'email.required'          => 'Email address is required.',
            'email.email'             => 'Please enter a valid email address.',
            'email.unique'            => 'This email address is already taken.',
            'contact_number.required' => 'Contact number is required.',
            'password.confirmed'      => 'New passwords do not match.',
        ];
    }
}
