<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthMiddleware
{

    public function handle(Request $request, \Closure $next)
    {


        $jwt =  $request->config['jwt'];

        if ($jwt == 'panel') {
            return $next($request);
        } else {
            if ($jwt && $jwt != 'undefined') {
                // 传了
                $request->jwt = json_decode(decrypt($jwt));
                return $next($request);
            } else {
                //401  
                return new Response('未登录', 401);
            }
        }
    }
}
