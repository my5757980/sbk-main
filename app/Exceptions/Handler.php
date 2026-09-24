<?php

namespace App\Exceptions;

use App\Http\Controllers\Api\Controller as ResponseController;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

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
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            if (!$request->expectsJson()) {
                $loginRoute = $request->is('admin') || $request->is('admin/*')
                    ? route('admin.auth.login')
                    : route('login');

                return redirect()->guest($loginRoute);
            }

            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return parent::render($request, $exception);
    }
}
