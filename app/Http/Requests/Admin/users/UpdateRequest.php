<?php

namespace App\Http\Requests\Admin\users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'username'      => 'nullable|string|max:255|unique:users,username,' . $id,
            'email'         => 'nullable|email|max:255|unique:users,email,' . $id,
            'phone'         => 'nullable|integer|unique:users,phone,' . $id,
            'name'          => 'nullable|string|max:255',
            'country_id'    => 'nullable',
            'state_id'      => 'nullable',
            'city_id'       => 'nullable',
            'address_1'     => 'nullable',
            'address_2'     => 'nullable',
            'address_3'     => 'nullable',
            'image'         => 'nullable',
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
