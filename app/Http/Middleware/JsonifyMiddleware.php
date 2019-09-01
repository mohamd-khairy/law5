<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class JsonifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var $response Response */
        $response = $next($request);
        $response->setContent($response->getContent());

        return $response;
    }
}
