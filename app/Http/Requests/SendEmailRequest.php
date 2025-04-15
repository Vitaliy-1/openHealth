<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Rules\PhoneNumber;

class SendEmailRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' => ['required', new PhoneNumber()],
            '_token' => 'required|csrf_token',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => 'ім\'я']),
            'name.string' => trans('validation.string', ['attribute' => 'ім\'я']),
            'phone.required' => trans('validation.required', ['attribute' => 'телефон']),
            '_token.required' => trans('validation.required', ['attribute' => 'токен CSRF']),
            '_token.csrf_token' => trans('validation.custom._token.csrf_token', ['attribute' => 'токен CSRF']),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->addExtension('csrf_token', function ($attribute, $value, $parameters, $validator) {
            return csrf_token() === $value;
        });
    }
}
