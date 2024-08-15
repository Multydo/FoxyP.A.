<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        
        "/signin_user",
         "/verifying",
         "/login_user",
         "/getUserInfo",
         "/testing",
         "/save_settings",
         "/build_tables",
         "/autoLogin"
    ];
}