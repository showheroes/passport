<?php

namespace ShowHeroes\Passport\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use EvgenyL\RestAPICore\Http\Exceptions\APIJSONHandlerTrait;
use ShowHeroes\SspMapping\Exceptions\SentryReportableException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{

    use APIJSONHandlerTrait;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(
            static function (Throwable $e) {
                //
            }
        );
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleJSONResponse($request, $exception);
        }
        return parent::render($request, $exception);
    }

    protected function prepareException(Throwable $e): HttpException|NotFoundHttpException|Throwable|AccessDeniedHttpException
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException('Not found.', $e);
        } else {
            $e = parent::prepareException($e);
        }
        return $e;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()
                ->json(
                    [
                        'error' => 'Unauthenticated.'
                    ],
                    401
                );
        }

        return redirect()->guest('login');
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e):void
    {
        if ($this->shouldReport($e)) {
            if (!$e instanceof SentryReportableException
                || $e->shouldBeReportedToSentry()
            ) {
                app('sentry')->captureException($e);
            }
        }

        parent::report($e);
    }
}
