<?php

namespace App\Exceptions;

use App\Traits\Response\HasApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasApiResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->reportable(function (OAuthServerException $e) {
            if($e->getCode() == 9)
                return false;
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            if ($e instanceof ModelNotFoundException) {
                $modelClass =class_basename($e->getModel());
                return $this->send(
                    message: __('messages.exceptions.not-found', ['model' => Str::of($modelClass)->headline()]),
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }
        }
        return parent::render($request, $e);
    }
}
