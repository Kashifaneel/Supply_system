<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplyRequest extends FormRequest
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
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'supply_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|exists:p_o_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item must be supplied.',
            'items.*.po_item_id.required' => 'PO Item is required.',
            'items.*.quantity.required' => 'Supply quantity is required.',
            'items.*.quantity.min' => 'Supply quantity must be at least 1.',
        ];
    }
}
