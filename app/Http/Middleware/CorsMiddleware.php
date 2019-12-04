<?php
/*
 * Author:xx_lufei
 * Time:2016年11月3日13:15:51
 * Note:Access Control Headers.
 */

namespace App\Http\Middleware;


use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware
{
    private $headers;
    private $allow_origin;

    public function handle(Request $request, \Closure $next)
    {
        $this->headers = [
            "Access-Control-Allow-Methods" => "GET, POST, PATCH, PUT, OPTIONS",
            'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers'),
        ];
        if ($request->isMethod('options')) {
            return $this->setCorsHeaders(new Response('OK', 200));
        }
        return  $this->setCorsHeaders($next($request));
    }

    /**
     * @param $response
     * @return mixed
     */
    public function setCorsHeaders($response)
    {

        foreach ($this->headers as $key => $value) {
            $response->header($key, $value);
        }
        $response->header('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
