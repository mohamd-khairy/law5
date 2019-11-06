<?php

namespace App\Http\Middleware;

use App\Model\User;
use App\Model\UserSetting;
use Closure;

class PermissionFor
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

            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        //token or step2token
        $header = trim(explode(' ', $request->header("Authorization"))[1]);

        $user = User::with('roles')->where('step2token', $header)->first();

        if(!empty($user)){ // step2token

            $userSetting = UserSetting::where('userId' , $user->id)->first()?? UserSetting::firstOrCreate(['userId' => $user->id]);

            if(!empty($userSetting) && $user->checkStep2Token == true && $userSetting->isTwofactorAUthenticationEnabled == true)
            {
                session_start();
                $_SESSION['user'] = $user;

                $permissions = explode('&', $permission);
                return (!empty($user->roles) && (in_array($user->roles->key, $permissions))) ?
                    $next($request)  : response('Unauthorized.', 401);
            }

            return response()->json(['message' => 'Unauthorized.'], 401);

        }else{ // token

            $user = User::with('roles')->where('token', $header)->first();
            
            if(!empty($user)){ 
            
                $userSetting = UserSetting::where('userId' , $user->id)->first()?? UserSetting::firstOrCreate(['userId' => $user->id]);

                if((!empty($userSetting) && $userSetting->isTwofactorAUthenticationEnabled == false) || $user->checkStep2Token == false )
                {
                    session_start();
                    $_SESSION['user'] = $user;

                    $permissions = explode('&', $permission);
                    return (!empty($user->roles) && (in_array($user->roles->key, $permissions))) ?
                        $next($request)  : response('Unauthorized.', 401);
                }
            }

            return response()->json(['message' => 'Unauthorized.'], 401);
        }
       
        return response()->json(['message' => 'Unauthorized.'], 401);
      
    }
}
