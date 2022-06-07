<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class EnsureShopUserEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $shopUser = \Auth::guard('shopUser') -> user();

        if (!$shopUser || ($shopUser instanceof MustVerifyEmail && !$shopUser -> hasVerifiedEmail()))
        {
          return $request -> expectsJson() ? abort(response() -> json(['emailNotVerified' => true], 403)) : Redirect::route('shop.verification.notice');
        }

        return $next($request);
    }
}
