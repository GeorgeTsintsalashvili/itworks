<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class
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
     * @throws \Throwable
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
        if ($exception instanceof TokenMismatchException)
        {
          if ($request -> expectsJson())
          {
              return response() -> json(['tokenMismatch' => true], 419);
          }
        }

        if ($exception instanceof \Swift_TransportException)
        {
          if ($request -> expectsJson())
          {
              return response() -> json(['mailServerFault' => true], 419);
          }
        }

        if ($exception instanceof ThrottleRequestsException)
        {
          if ($request -> expectsJson())
          {
              return response() -> json(['tooManyRequests' => true], 419);
          }
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
      if ($request -> expectsJson())
      {
        return response() -> json(['unauthenticatedError' => true], 401);
      }

      $guard = \Arr::get($exception -> guards(), 0);

      switch ($guard)
      {
        case 'shopUser': $login = '/#authorization-modal'; break;

        default: $login = '/login'; break;
      }

      return redirect() -> guest($login);
    }
}
