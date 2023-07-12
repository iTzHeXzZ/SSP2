<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    public function redirectTo($request)
    {
        if ($request->is('welcome') && $request->user()) {
            return route('home');
        }
    }
    
    #protected function redirectTo(Request $request): ?string
    #{
     #   return $request->expectsJson() ? null : route('login');
    #}
}
