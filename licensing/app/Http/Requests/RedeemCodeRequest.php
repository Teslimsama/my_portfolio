<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|size:19',
            'domain' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'code.size' => 'The license code must be exactly 19 characters (format: XXXX-XXXX-XXXX-XXXX).',
            'domain.required' => 'The requesting domain is required.',
        ];
    }
}
