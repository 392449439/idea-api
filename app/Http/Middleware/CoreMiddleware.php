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

        $domain_id = '';
        $domainInfo = null;

        if (isset($config['domain_id'])) {
            /**拿到domain */
            $domain_id = $config['domain_id'];
        } else if (isset($config['app_id'])) {
            $domain_id = DB::table('domain_app')->where('app_id',  $config['app_id'])->value('domain_id');
        }

        if ($domain_id) {
            $domainInfo = DB::table('domain')->where('domain_id',  $config['domain_id'])->first();
        }

        $request->domain_id = $domain_id;
        $request->domainInfo = $domainInfo;

        try {
            $request->jwt = json_decode(decrypt($request->config['jwt']));
        } catch (\Throwable $th) {
            $request->jwt = null;
        }

        return $next($request);
    }
}
