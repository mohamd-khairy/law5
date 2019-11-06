<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Model\UserSetting;

class UserSettingsController extends Controller
{

   
    public function get(Request $request)
    {
        $user = userData();

        $model = UserSetting::where('userId', $user->id)->first();
        if (is_null($model)) {
            $model = UserSetting::create(['userId' => $user->id]);
            //   return $this->respond(Response::HTTP_NOT_FOUND);
        }
        $model['isTwofactorAUthenticationEnabled'] = ($model['isTwofactorAUthenticationEnabled'])? true : false;
        return $this->respond(Response::HTTP_OK, $model);
    }


    public function put(Request $request)
    {
        $user = userData();

        $this->validate($request, UserSetting::$rules);
        
        $model = UserSetting::where('userId', $user->id)->first();
        if (is_null($model)) {
            $model = UserSetting::create(['userId' => $user->id]);
            // return $this->respond(Response::HTTP_NOT_FOUND);
        }
        $old=$model->getOriginal();
        $model->update($request->all());
        app('App\Http\Controllers\LogController')->Logging_update(strtolower(substr('UserSetting', 10)."s"),$model,$old);

        $model['isTwofactorAUthenticationEnabled'] = ($model['isTwofactorAUthenticationEnabled'])? true : false;
        return $this->respond(Response::HTTP_OK, $model);
    }

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
