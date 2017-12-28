<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use ReflectionException;
use FatalThrowableError;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ( ($exception instanceof \InvalidArgumentException || $exception instanceof \Illuminate\Database\QueryException  || $exception instanceof \BadMethodCallException || $exception instanceof \ErrorException || $exception instanceof ReflectionException || $exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) && $request->wantsJson())
        {
            return response()->json([
                'status' => 0,
                'login' => 0,
                'message' => 'Resource not found.',
                'error' => $exception->getMessage()
            ], 404);
        }
        return parent::render($request, $exception);
    }
}
