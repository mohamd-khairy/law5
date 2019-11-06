<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;

class authTokenOnly
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
        if(empty($request->header("Authorization"))){
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $header = trim(explode(' ', $request->header("Authorization"))[1]);
        $user = User::where('token', $header)->first();
    
        if (!empty($header) && !empty($user) && ($header == $user['token'])) {
            
            session_start();
            $_SESSION['user'] = $user;

            return $next($request);
        } else {
           return response()->json(['message' => 'Unauthorized.'], 401);
        }
    }
}
