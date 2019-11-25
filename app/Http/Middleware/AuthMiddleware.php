<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthMiddleware
{

    public function handle(Request $request, \Closure $next)
    {

        $jwt =  $request->jwt;
        if ($jwt == 'panel') {
            return $next($request);
        } else {
            if ($jwt && $jwt != 'undefined') {
                return $next($request);
            } else {
                //401  
                return new Response('未登录', 401);
            }
        }
    }
}
