<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper {

    private const RESPONSE_STATUS_KEY    = 'status';
    private const RESPONSE_STATUS_FAILED = 'failed';
    private const RESPONSE_STATUS_OK     = 'ok';
    private const RESPONSE_ERROR_KEY     = 'error';
    private const RESPONSE_DATA_KEY      = 'data';
    private const RESPONSE_CODE_KEY      = 'code';

    /**
     * Return response with status - failed, and error message.
     *
     * @param $errorKey
     * @param null $message
     * @return JsonResponse
     */
    public static function sendErrorResponse($errorKey, $message = null) {
        $message = is_null($message) ? ErrorHelper::getErrorMessage($errorKey) : $message;
        $code    = ErrorHelper::getErrorCode($errorKey);

        $errorResponse =  [
            self::RESPONSE_CODE_KEY   => $code,
            self::RESPONSE_STATUS_KEY => self::RESPONSE_STATUS_FAILED,
            self::RESPONSE_ERROR_KEY  => $message
        ];

        return response()->json($errorResponse, $code);
    }

    /**
     * Return response with "status" - ok,and "data" in json format
     * @param $data
     * @param int $code
     * @return JsonResponse
     */
    public static function sendSuccessResponse($data, $code = JsonResponse::HTTP_OK) {
        $successResponse = [
            self::RESPONSE_CODE_KEY   => $code,
            self::RESPONSE_STATUS_KEY => self::RESPONSE_STATUS_OK,
            self::RESPONSE_DATA_KEY   => $data
        ];

        return response()->json($successResponse, $code);
    }

}
