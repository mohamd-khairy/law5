<?php namespace App\Http\Controllers;

use App\Model\Role;
use App\Model\User;
use App\Model\Applicant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Model\Employee;
use App\Model\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendResetPasswordLink;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{

    const MODEL = User::class;

    use RESTActions;

    public function register(Request $request)
    {

        $this->validate($request, User::$rules);
        $data = $request->all();
        $data['email'] = strtolower($data['email']);
        $data['password'] = Hash::make($data['password']);
        $data['token'] = User::generateApiToken();
        $role = Role::where('key', 'Applicant')->first()->toArray();
        $data['roleId'] = $role['id'];
        $user = User::create($data);
        app('App\Http\Controllers\LogController')->Logging("users" , $user ,"register");

        $sectorId = NULL;
        if (isset($data['sectorId'])) {
            $sectorId = $data['sectorId'];
        }
        if ($role['key'] == 'Applicant') {
            $applicant = new Applicant();
            $applicant->facilityName = $user->name;
            $applicant->sectorId = $sectorId;
            $applicant->userId = $user->id;
            $applicant->save();
            $user->profileId = $applicant->id;
        }
        $user->roleKey = Role::where('id',  $user->roleId)->first()['key'];
        return $this->respond(Response::HTTP_CREATED, $user);
    }

    public function login(Request $request)
    {
        DB::statement('SET GLOBAL event_scheduler = ON');
        $email = strtolower($request->get('email'));
        $password = $request->get('password');
        $user = User::where('email', $email)->first();
        if(empty($user)){
            return response()->json(__('auth.loginMessage'),422);
        }
        if($user->isActive == false){
            return response()->json(__('auth.isActive'),422);
        }
        if ($user && Hash::check($password, $user->password)) {
            $user->token = User::generateApiToken();
            $user->roleKey = Role::where('id', $user['roleId'])->first()['key'];
            if ($user->roleKey == "Applicant") {
                $user->profileId = (!empty($data = Applicant::where("userId", $user->id)->first())) ? $data->id : null;
            } else {
                $user->profileId = (!empty($data = Employee::where("userId", $user->id)->first())) ? $data->id : null;
            }

            User::where('email', $request->input('email'))->update(['token' => "$user->token"]);
            app('App\Http\Controllers\LogController')->Logging("users" , $user , "Login");
            return response()->json($user, Response::HTTP_OK);
        }
            return response()->json(__('auth.loginMessage'),422);
    }

    public function ResetPassword(Request $request)
    {
        $rules = [
            'email'   => 'required|email',
        ];
        $this->validate($request, $rules);
        $email=strtolower($request->email);
        $user = User::where("email", $email)->first();
        if (!empty($user)) {
            $user = User::find($user->id);
            if(!empty($user->resetPasswordCode)){
                switch (substr($user->resetPasswordCode,-3, 1)) {
                    case '1':
                    $code= $this->generate_code(15)."2".rand(10,20);
                        break;
                    case '2':
                    $code= $this->generate_code(15)."3".rand(10,20);
                        break;
                    case '3':
                     $now  = Carbon::now();
                     $end  = $user->resetPasswordCodeCreationdate;
                     $hour = $now->diffInHours($end);
                    if ($hour >= 6) { 
                        $user->resetPasswordCode=null;
                        $user->resetPasswordCodeCreationdate=null;
                        $user->save();
                        $code= $this->generate_code(15)."1".rand(10,20);
                    }else{
                        return response()->json(__('auth.ResetPasswordAfterSix'),400);
                    }
                        break;
                    default:
                        return response()->json(__('auth.wrong'),400);
                    break;
                }
            }else{
                $code= $this->generate_code(15)."1".rand(10,20);
            }
            $user->resetPasswordCode=$code;
            $user->resetPasswordCodeCreationdate = Carbon::now()->toDateTimeString();
            if ($user->save()) {
                app('App\Http\Controllers\LogController')->Logging("users" , $user , "reset" ,"resetPasswordCode");
                $this->mail( $user->name, $user->email, $user->token,$user->resetPasswordCode ,$user->resetPasswordCodeCreationdate);
            }
        } else {
            return response()->json(__('auth.NoUser'),400);
        }
    }

    public function SendResetPasswordCode(Request $request)
    {
        $rules = [
            'code'   => 'required|min:8|max:100',
            'newPassword'   => 'required|min:8|max:100',
        ];
        $this->validate($request, $rules);
        $user = User::where("resetPasswordCode", $request->code)->first();
        if (empty($user)){
            return response()->json(__('auth.ForgetPasswordPage'),400);
        }
        $now = Carbon::now()->addHours(4);
        $days = $now->diffInDays($user->resetPasswordCodeCreationdate);
        if($days < 1) {//
            $user = User::find($user->id);
            $user->password = Hash::make($request->newPassword);
            $user->resetPasswordCode = null;
            $user->resetPasswordCodeCreationdate = null;
            $user->save();
            app('App\Http\Controllers\LogController')->Logging("users" , $user , "reset" ,"password");
            return response()->json(__('auth.updatePasswordSuccess'), 200);
        } else {
            $user = User::find($user->id);
            $user->resetPasswordCode = null;
            $user->resetPasswordCodeCreationdate = null;
            $user->save();
            return response()->json(__('auth.ExpiredCode'),400);
        }
    }

    protected function generate_code($length = 8)
    {
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
            '0123456789';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++)
            $str .= $chars[random_int(0, $max)];

        return $str;
    }

    public function mail($name, $email, $token, $code ,$time)
    {
        $setting = Setting::first();
        app('App\Http\Controllers\SettingsController')->SendMailSettings();
        Mail::to($email)->send(new SendResetPasswordLink($name,$email, $token, $code , $time));
    }
}
