<?php

namespace App\Http\Requests\Admin\admins;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
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
            'username'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required',
            'password'      => 'required|string|min:8',
            'first_name'    => 'required|string|max:255',
            'medium_name'   => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'country_id'    => 'required|integer',
            'state_id'      => 'required|integer',
            'city_id'       => 'required|integer',
            'role_id'       => 'required|integer',
            'image'         => 'required|mimes:png,jpg', 
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
