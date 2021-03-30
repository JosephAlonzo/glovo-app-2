<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'carrier/rate',
        'carrier/*',
        'workingdays.json',
        'workingtime.json',
        'serviceavailability.json',
        'productavailability.json',
        'orders/tracking/live',
        'orders/tracking/currier',
        'webhook/*',
        'webhooks/*',
        '/*',
    ];
}
