<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CoreMiddleware
{

    public function handle(Request $request, \Closure $next)
    {

        if ($request->getMethod() == "GET") {
            return $next($request);
        }

        $heads = $request->header('Authorization');
        $heads  = explode(";", $heads);
        $config = [];
        collect($heads)->each(function ($el) use (&$config) {
            $item = explode("=", $el);
            $config[$item[0]] = $item[1];
        });
        $request->config = $config;

        try {
            $request->jwt = json_decode(decrypt($request->config['jwt']));
        } catch (\Throwable $th) {
            $request->jwt = null;
        }

        return $next($request);
    }
}
