<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Model\User;
use App\Model\UserSetting;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth; 

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
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
                return $next($request);
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
                    return $next($request);
                }
            }

            return response()->json(['message' => 'Unauthorized.'], 401);
        }
       
        return response()->json(['message' => 'Unauthorized.'], 401);
    }
}
