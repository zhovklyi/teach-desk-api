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
            $payload = APIResponseData::error('The given data was invalid.', [
                'errors' => $e->errors(),
            ]);

            return response()->json($payload->toArray(), 422);
        });

        $this->renderable(function (AuthenticationException $e) {
            $payload = APIResponseData::error('Unauthenticated.');

            return response()->json($payload->toArray(), 401);
        });

        $this->renderable(function (ModelNotFoundException $e) {
            $payload = APIResponseData::error('Resource not found.');

            return response()->json($payload->toArray(), 404);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            $payload = APIResponseData::error('Route not found.');

            return response()->json($payload->toArray(), 404);
        });

        // Fallback for any other exception
        $this->renderable(function (Throwable $e) {
            // If an HttpException, use its status, otherwise 500
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            // Avoid leaking internal messages in production for 500s
            $message = $status >= 500 ? 'Server error.' : ($e->getMessage() ?: 'Error');

            $payload = APIResponseData::error($message);

            return response()->json($payload->toArray(), $status);
        });
    }
}
