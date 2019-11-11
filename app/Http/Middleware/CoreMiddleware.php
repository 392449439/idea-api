<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CoreMiddleware
{

    public function handle(Request $request, \Closure $next)
    {

        // $request
        $heads = $request->header('Authorization');
        $heads  = explode(";", $heads);
        $config = [];
        collect($heads)->each(function ($el) use (&$config) {
            $item = explode("=", $el);
            $config[$item[0]] = $item[1];
        });


        $request->config = $config;

        if (!$request->config['app_id']) {
            return response()->json([
                "code" => -9000,
                "msg" => '没有提供app_id',
                "data" => null
            ], 401);
        }

        $App = DB::table('app');
        $appInfo = $App->where('app_id', $request->config['app_id'])->first();
        if (!$appInfo) {
            return response()->json([
                "code" => -9001,
                "msg" => '无效的app_id',
                "data" => null
            ], 401);
        }
        $request->appInfo = $appInfo;
        return $next($request);
    }
}
