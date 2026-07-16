<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tier_id' => 'nullable|integer|exists:price_tiers,id',
            'custom_days' => 'nullable|integer|min:1|max:365',
            'email' => 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'custom_days.min' => 'Custom days must be at least 1.',
            'custom_days.max' => 'Custom days cannot exceed 365.',
            'email.required' => 'An email address is required to receive your license code.',
        ];
    }
}
