<?php

namespace App\Exceptions;

use App\Data\Common\APIResponseData;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e) {
            return APIResponseData::error(
                message: 'The given data was invalid.',
                code: 422,
                data: ['errors' => $e->errors()],
            );
        });

        $this->renderable(function (AuthenticationException $e) {
            return APIResponseData::error(message: 'Unauthenticated.', code: 401);
        });

        $this->renderable(function (ModelNotFoundException $e) {
            return APIResponseData::error(message: 'Resource not found.', code: 404);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return APIResponseData::error(message: 'Route not found.', code: 404);
        });

        // Fallback for any other exception
        $this->renderable(function (Throwable $e) {
            // If an HttpException, use its status, otherwise 500
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            // Avoid leaking internal messages in production for 500s
            $message = $status >= 500 ? 'Server error.' : 'An error occured.';

            return APIResponseData::error(message: $message, code: $status);
        });
    }
}
