<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePORequest extends FormRequest
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
        $purchaseOrderId = $this->route('purchase_order')?->id;
        
        return [
            'po_number' => [
                'required',
                'string',
                'max:255',
                "unique:purchase_orders,po_number,{$purchaseOrderId}"
            ],
            'po_date' => 'required|date',
            'po_image' => 'nullable|image|max:2048',
            'institution_name' => 'required|string|max:255',
            'institution_email' => 'nullable|email|max:255',
            'institution_phone' => 'nullable|string|max:20',
            'institution_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.batch_no' => 'nullable|string|max:255',
            'items.*.mfg_date' => 'nullable|date',
            'items.*.exp_date' => 'nullable|date|after_or_equal:items.*.mfg_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required.',
            'items.*.name.required' => 'Item name is required.',
            'items.*.price.required' => 'Item price is required.',
            'items.*.quantity.required' => 'Item quantity is required.',
            'items.*.exp_date.after_or_equal' => 'Expiry date must be after or equal to manufacturing date.',
        ];
    }
}
