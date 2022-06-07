<?php

namespace App\Http\Middleware;

use Closure;

class ShoppignCartActions
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
        if (\Auth::guard('shopUser') -> check())
        {
            return $next($request);
        }

        return abort(response() -> json(['cartActionNotAllowed' => true], 200));
    }
}
