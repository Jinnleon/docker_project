<?php

namespace App\Exceptions;

use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            return ResponseHelper::sendErrorResponse(ErrorHelper::AUTH_ERROR);
        }

        if ($exception instanceof RegisterException) {
            $errorMessage = $exception->getMessage();
            return ResponseHelper::sendErrorResponse(ErrorHelper::AUTH_ERROR, $errorMessage);
        }

        if ($exception instanceof ScanDirException) {
            $errorMessage = $exception->getMessage();
            return ResponseHelper::sendErrorResponse(ErrorHelper::SCAN_DIR_ERROR, $errorMessage);
        }

        if ($exception instanceof SearchFilesException) {
            $errorMessage = $exception->getMessage();
            return ResponseHelper::sendErrorResponse(ErrorHelper::SEARCH_ERROR, $errorMessage);
        }

        return parent::render($request, $exception);
    }
}
