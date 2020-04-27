<?php

namespace App\Http\Request;

use App\Exceptions\ScanDirException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ScanDirRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'dir'   => 'required|string',
            'depth' => 'integer'
        ];
    }

    public function messages() {
        return [
            'dir.required'  => 'Directory path not found.',
            'dir.string'    => 'Directory path should be a string.',
            'depth.integer' => 'Invalid depth format'
        ];
    }

    /**
     * @param Validator $validator
     * @throws ScanDirException
     */
    protected function failedValidation(Validator $validator) {
        $errorMessage = $validator->errors()->first();
        throw new ScanDirException($errorMessage);
    }

}
