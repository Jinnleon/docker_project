<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ErrorHelper {

    const AUTH_ERROR     = 'auth_error';
    const SCAN_DIR_ERROR = 'scan_dir_error';
    const SEARCH_ERROR   = 'search_error';

    private const ERROR_MESSAGE = 'error_message';
    private const ERROR_CODE    = 'error_code';

    public static function getErrorCode($errorKey) {
        return self::ERROR_DICTIONARY[$errorKey][self::ERROR_CODE];
    }

    public static function getErrorMessage($errorKey) {
        return self::ERROR_DICTIONARY[$errorKey][self::ERROR_MESSAGE];
    }

    private const ERROR_DICTIONARY = array(
        self::AUTH_ERROR => [self::ERROR_MESSAGE => 'Invalid email or password.', self::ERROR_CODE => JsonResponse::HTTP_UNAUTHORIZED],
        self::SCAN_DIR_ERROR => [self::ERROR_MESSAGE => 'In the scanning process error has occurred.', self::ERROR_CODE => JsonResponse::HTTP_FORBIDDEN],
        self::SEARCH_ERROR => [self::ERROR_MESSAGE => 'In the search files process error has occurred.', self::ERROR_CODE => JsonResponse::HTTP_FORBIDDEN],
    );

}
