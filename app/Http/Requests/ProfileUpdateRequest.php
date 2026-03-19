<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio'      => ['nullable', 'string', 'max:500'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:100'],
            'website'  => ['nullable', 'url', 'max:255'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
