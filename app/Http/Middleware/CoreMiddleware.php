<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CoreMiddleware
{

    public function handle(Request $request, \Closure $next)
    {

        if (!$request->filled('app_id')) {
            return response()->json([
                "code" => -9000,
                "msg" => '没有提供app_id',
                "data" => null
            ], 401);
        }

        $App = DB::table('app');
        $appInfo = $App->where('app_id', $request->input("app_id"))->first();
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
