<?php

namespace App\Http\Request;

use App\Exceptions\RegisterException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'email'    => 'required|email|unique:users',
            'name'     => 'required|string|max:32|min:3',
            'password' => 'required'
        ];
    }

    public function messages() {
        return [
            'email.required'    => 'Email is required.',
            'email.email'       => 'Email format is invalid.',
            'name.required'     => 'Name is required.',
            'name.string'       => 'Name should be a string.',
            'name.max'          => 'The name should be shorter than 32 characters.',
            'name.min'          => 'The name should be longer than 2 characters.',
            'password.required' => 'Password is required.'
        ];
    }

    /**
     * @param Validator $validator
     * @throws RegisterException
     */
    protected function failedValidation(Validator $validator) {
        $errorMessage = $validator->errors()->first();
        throw new RegisterException($errorMessage);
    }

}
