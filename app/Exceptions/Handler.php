<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->renderable(function (Throwable $e, $request) {
                if ($request->is('api/*')) {
                    $error = '';
                    if ($e instanceof TokenInvalidException) {
                        $error = 'Invalid token';
                    } else if ($e instanceof TokenExpiredException) {
                        $error = 'Token has expired';
                    } else if($e instanceof JWTException) {
                        $error = 'Token not parsed';
                    }else {
                        $error = 'Unauthorized';
                    }
                    return response()->json(['error' => $error], 401);
                }
            });
            
        });
    }
}
