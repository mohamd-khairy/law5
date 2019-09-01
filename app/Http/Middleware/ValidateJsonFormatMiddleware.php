<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class ValidateJsonFormatMiddleware
 * @package App\Http\Middleware
 */
class ValidateJsonFormatMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next){
        if ($request->isJson()){
            $validJson = json_decode($request->getContent(), true);
            //$json = $request->json()->all(); // this will give empty array in both cases (1. invalid JSON format, 2. empty JSON object)
            if (is_null($validJson)){
                return response()->json(['json.invalid' => ['Input JSON object has invalid format.']], 422);
            }
        }

        return $next($request);
    }
}
