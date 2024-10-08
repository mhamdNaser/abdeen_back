<?php

namespace App\Http\Requests\Admin\product_details;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            'sku'              => 'required|string|max:255',
            'en_name'          => 'required|string|max:255',
            'ar_name'          => 'required|string|max:255',
            'en_description'   => 'required|string',
            'ar_description'   => 'required|string',
            'cost_Price'       => 'nullable|numeric',
            'public_price'     => 'nullable|numeric',
            'quantity'         => 'nullable|integer',
            'category_id'      => 'nullable|integer',
            'brand_id'         => 'nullable|integer',
            'image'            => 'required',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ], 422));
    }
}
