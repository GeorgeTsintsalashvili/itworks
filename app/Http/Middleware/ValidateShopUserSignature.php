<?php

namespace App\Http\Middleware;

use Closure;

class ValidateShopUserSignature
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
        if (!$request -> hasValidSignature())
        {
            return redirect() -> route('shop.invalidSignature');
        }

        return $next($request);
    }
}
