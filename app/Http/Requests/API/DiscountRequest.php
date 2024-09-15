<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{
    public function authorize()
    {
        // Update this if you need to add authorization logic
        return true;
    }

    public function rules()
    {
        return [
            'discount_code' => 'nullable|string',
            'price' => 'required|numeric',
            'customer_id' => 'required|exists:customers,id',
            'services' => 'required|json', 
        ];
    }

}
