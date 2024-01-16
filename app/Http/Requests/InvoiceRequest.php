<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_item' => ['required', 'string'],
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'number' => 'required|string',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'discount' => 'required|numeric',
            'reference' => 'nullable|string',
            'terms_and_conditions' => 'required|string',
        ];
    }
}
