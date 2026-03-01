<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
    ];
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'error_type' => 'unauthenticated',
                    'message' => __('Unauthenticated.')
                ], 401);
            }
        });
    }
}
