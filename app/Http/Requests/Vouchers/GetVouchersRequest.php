<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'serie' => 'nullable|string|max:20',
            'numero' => 'nullable|string|max:20',
            'tipo_comprobante' => 'nullable|string|max:3',
            'moneda' => 'nullable|string|in:PEN,USD', 
            'page' => 'nullable|integer|min:1',
            'paginate' => 'nullable|integer|min:1|max:100',
        ];
    }
}
