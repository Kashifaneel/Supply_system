<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supply_id' => 'required|exists:supplies,id',
            'cheque_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'cheque_image' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supply_id.required' => 'Supply is required.',
            'cheque_no.required' => 'Cheque number is required.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'cheque_image.image' => 'Cheque image must be a valid image file.',
            'cheque_image.max' => 'Cheque image must not exceed 2MB.',
        ];
    }
}
