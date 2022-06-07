<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Helpers\Paginator;

class PaginatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this -> app -> bind('paginator', function(){

          return new Paginator();
      });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
