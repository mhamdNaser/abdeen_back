<?php

namespace App\Http\Requests\Admin\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'                  => 'nullable|string|max:255',
            'client_id'             => 'required|string|max:255',
            'client_secret'         => 'required|string|max:255',
            'currency'              => 'nullable|string|max:255',
            'locale'                => 'nullable|string|max:255',
        ];
    }

    /**
     * Customize the error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mode.required'                  => 'The mode field is required.',
            'mode.in'                        => 'The mode must be either sandbox or live.',
            'sandbox_client_id.required_if' => 'The sandbox client ID field is required when mode is sandbox.',
            'sandbox_client_secret.required_if' => 'The sandbox client secret field is required when mode is sandbox.',
            'live_client_id.required_if'    => 'The live client ID field is required when mode is live.',
            'live_client_secret.required_if' => 'The live client secret field is required when mode is live.',
            'live_app_id.required_if'       => 'The live app ID field is required when mode is live.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'errors'    => $validator->errors(),
        ], 422));
    }
}
