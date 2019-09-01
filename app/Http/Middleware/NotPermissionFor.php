<?php

namespace App\Http\Middleware;
use App\Model\User;
use Closure;

class NotPermissionFor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null)
    {
        if(empty($request->header("Authorization"))){
            return response('Unauthorized.', 401);
        }
        $header = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header)) {
            if ($header == User::where('token', $header)->first()['token']) {
                $user = User::with('roles')->where('token', $header)->first();
            }else{
                return  response('Unauthorized.', 401);  
            }
        } else {
          return  response('Unauthorized.', 401);
        }
        $permissions = explode('&', $permission);
        return (!empty($user->roles) && (in_array($user->roles->key, $permissions))) ?
        response('Unauthorized.', 401)  : $next($request);
    }
}
