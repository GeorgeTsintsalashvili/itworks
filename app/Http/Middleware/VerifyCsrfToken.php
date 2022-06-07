<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
     protected $except = [
         '/configurator/*',
         '/shoppingCart/*',
         '/contact/*',
         '/products/*',
         '/accessories/*',
         '/computers/*',
         '/processors/*',
         '/motherboards/*',
         '/memoryModules/*',
         '/monitors/*',
         '/videoCards/*',
         '/hardDiskDrives/*',
         '/solidStateDrives/*',
         '/computerCases/*',
         '/powerSupplies/*',
         '/processorCoolers/*',
         '/caseCoolers/*',
         '/opticalDiscDrives/*',
         '/networkDevices/*',
         '/peripherals/*',
         '/uninterruptiblePowerSupplies/*',
         '/notebookChargers/*',
         '/laptops/*',
         '/controlPanel/*',
         '/password/email',
         '/payment/callback/*'
     ];
}
