<?php

namespace ShowHeroes\Passport\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class CorsRequest
 * @package ShowHeroes\Passport\Http\Middleware
 */
class CorsRequest
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
    }
}
