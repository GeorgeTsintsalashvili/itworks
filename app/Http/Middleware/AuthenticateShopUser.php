<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class AuthenticateShopUser
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */

   public function handle($request, Closure $next)
   {
     if (\Auth::guard('shopUser') -> check())
     {
       return $next($request);
     }

     return redirect('/#authorization-modal');
   }
}
