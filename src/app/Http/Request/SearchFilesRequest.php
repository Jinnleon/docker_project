<?php
namespace App\Http\Request;

use App\Exceptions\ScanDirException;
use App\Exceptions\SearchFilesException;
use App\GlobalValues;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SearchFilesRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            GlobalValues::FILE_NAME => 'string',
            GlobalValues::DEPTH     => 'integer',
            GlobalValues::TYPE      => 'string'
        ];
    }

    public function messages() {
        return [
            'file_name.string'   => 'File name should be a string.',
            'depth.integer'      => 'Invalid depth format.',
            'type.string'        => 'Type should be a string.'
        ];
    }

    /**
     * @param Validator $validator
     * @throws SearchFilesException
     */
    protected function failedValidation(Validator $validator) {
        $errorMessage = $validator->errors()->first();
        throw new SearchFilesException($errorMessage);
    }

}
