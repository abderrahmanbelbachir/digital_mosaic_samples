<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestHost = parse_url($request->headers->get('origin'),  PHP_URL_HOST);
        $authorization = $request->header('Authorization');
        $domains = ['placetta.dz'];


        return $next($request);
    }
}
