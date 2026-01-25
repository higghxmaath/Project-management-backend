<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    
   protected $middlewareAliases = [

    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

    // JWT
    'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,

];

    protected $middlewareGroups = [

        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Throttle API requests
            'throttle:api',

            // Resolve route-model bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     */
    protected $middlewareAliases = [

        // Authentication
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        // Authorization
        'can' => \Illuminate\Auth\Middleware\Authorize::class,

        // Guest redirect
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Rate limiting
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // JWT auth middleware (comes from tymon/jwt-auth)
        'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
    ];
}
