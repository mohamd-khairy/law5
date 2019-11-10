<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationMail;
use App\Model\Role;
use App\Model\User;
use App\Model\Applicant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendResetPasswordLink;
use App\Mail\TwoFactorAuth;
use App\Model\TrustedDevices;
use App\Model\UserSetting;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{

    const MODEL = User::class;

    use RESTActions;

    public function register(Request $request)
    {
        DB::beginTransaction();

        $this->validate($request, User::$rules);
        $data = $request->all();
        $data['email'] = strtolower($data['email']);
        $data['password'] = Hash::make($data['password']);
        $role = Role::where('key', 'Applicant')->first()->toArray();
        $data['roleId'] = $role['id'];
        $data['emailConfirmationCode'] = $this->generate_code(6);
        $user = User::firstOrCreate($data);
        $userSettings = UserSetting::firstOrCreate(['userId' => $user->id]);
        app('App\Http\Controllers\LogController')->Logging("users", $user, "register");

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

        if(!$user->isEmailVerified){ // false

            Mail::to($user->email)->send(new ConfirmationMail($user)); 

            if(count(Mail::failures()) > 0){

                DB::rollback();

                return response()->json(__('auth.mailNotSend'), 400);
            }
        }

        DB::commit();

        unset($user['token']);
        unset($user['emailConfirmationCode']);

        return $this->respond(Response::HTTP_CREATED, $user);
    }

    public function login(Request $request)
    {
        DB::statement('SET GLOBAL event_scheduler = ON');

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'trustToken' => 'nullable|string'
        ]);

        $email = strtolower($request->get('email'));
        $password = $request->get('password');
        $trustToken  = $request->get('trustToken') ?? null;

        $user = User::where('email', $email)->first();

        if (empty($user)) {
            return response()->json(__('auth.loginMessage'), 422);
        }

        if ($user->isActive == false) {
            return response()->json(__('auth.isActive'), 422);
        }

        if ($user->isEmailVerified == false) {
            return response()->json(__('auth.isEmailVerified'), 422);
        }

        if ($user && Hash::check($password, $user->password)) {

            /** for twoFactorAuth */
            $userSetting = UserSetting::where('userId', $user->id)->first() ?? UserSetting::firstOrCreate(['userId' => $user->id]);;
            if ($userSetting->isTwofactorAUthenticationEnabled == true) {

                $step2required = $this->TwoFactorAuth($user, $trustToken);
            } else {
                $user->checkStep2Token = false;
                $user->save();

                $step2required = false;
            }

            $user = User::find($user->id);

            $user->token = User::generateApiToken();

            $step2token = User::generateApiToken();

            User::where('email', $request->input('email'))->update(['token' => "$user->token"]);

            if ($userSetting->isTwofactorAUthenticationEnabled == true && $user->checkStep2Token == true) {
                User::where('email', $request->input('email'))->update(['step2token' => "$step2token"]);
            }

            $user['isActive']        = ($user['isActive']) ? true : false;
            $user['isEmailVerified'] = ($user['isEmailVerified']) ? true : false;
            unset($user['checkStep2Token']);
            unset($user['step2token']);
            unset($user['step2code']);
            unset($user['codeCreationDate']);
            $user['step2required'] = $step2required;
            $user->roleKey = Role::where('id', $user['roleId'])->first()['key'];
            if ($user->roleKey == "Applicant") {
                $user->profileId = (!empty($data = Applicant::where("userId", $user->id)->first())) ? $data->id : null;
            } else {
                $user->profileId = (!empty($data = Employee::where("userId", $user->id)->first())) ? $data->id : null;
            }
            app('App\Http\Controllers\LogController')->Logging("users", $user, "Login");
            return response()->json($user, Response::HTTP_OK);
        }
        return response()->json(__('auth.loginMessage'), 422);
    }

    public function TwoFactorAuth($user, $trustToken)
    {
        $trustedDevice = TrustedDevices::where(['userId' => $user->id, 'trustToken' => "$trustToken"])->first();

        if (!empty($trustedDevice)) {

            $user = User::find($user->id);
            $user->checkStep2Token = false;
            $user->save();

            $step2required = false;
        } else {

            /** save data to table user  */
            $user = User::find($user->id);
            $user->step2code = rand(1000, 9999);
            $user->codeCreationDate = Carbon::now();
            $user->checkStep2Token = true;
            $user->save();

            /** send mail to user with the generated code  */
            Mail::to($user->email)->send(new TwoFactorAuth($user));

            if (Mail::failures()) {
                return response()->json(__('auth.mailNotSend'), 422);
            }

            $step2required = true;
        }

        return $step2required;
    }

    public function Step2Authentication(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|numeric',
            'getTrustToken' => 'required|boolean'
        ]);

        $user = userData();

        if ($request->code == $user->step2code) {

            $now = Carbon::now();
            $diff_in_minutes =  $now->diffInMinutes($user->codeCreationDate) / 60;
            if ($diff_in_minutes >= 1) {
                User::where('id', $user->id)->update([
                    'step2code' => null,
                    'codeCreationDate' => null
                ]);

                return response()->json(__('auth.codeExpired'), 422);
            }

            $step2token = User::generateApiToken();
            $trustToken =  User::generateApiToken();

            // set step2token
            User::where('id', $user->id)->update([
                'step2token' => "$step2token",
                'step2code' => null,
                'codeCreationDate' => null
            ]);

            if ($request->getTrustToken) {
                // set trustToken
                TrustedDevices::updateOrCreate([
                    'userId' => $user->id
                ], [
                    'trustToken' => "$trustToken"
                ]);
            } else {
                TrustedDevices::updateOrCreate([
                    'userId' => $user->id
                ], [
                    'trustToken' => null
                ]);
            }

            return response()->json([
                'token' => $step2token,
                'trustToken' => ($request->getTrustToken) ? $trustToken : null
            ], 200);
        }

        return response()->json(__('auth.codeNotCorrect'), 422);
    }

    public function resendStep2AuthenticationCode(Request $request)
    {
        $user = userData();

        /** save data to table user  */
        $user = User::find($user->id);
        $user->step2code = rand(1000, 9999);
        $user->codeCreationDate = Carbon::now();
        $user->save();

        /** send mail to user with the generated code  */
        Mail::to($user->email)->send(new TwoFactorAuth($user));

        if (Mail::failures()) {
            return response()->json(__('auth.mailNotSend'), 422);
        }

        return response()->json(__('auth.codeSentSuccessfully'), 200);
    }

    public function ResetPassword(Request $request)
    {
        $rules = [
            'email'   => 'required|email',
        ];
        $this->validate($request, $rules);
        $email = strtolower($request->email);
        $user = User::where("email", $email)->first();
        if (!empty($user)) {
            $user = User::find($user->id);
            if (!empty($user->resetPasswordCode)) {
                switch (substr($user->resetPasswordCode, -3, 1)) {
                    case '1':
                        $code = $this->generate_code(15) . "2" . rand(10, 20);
                        break;
                    case '2':
                        $code = $this->generate_code(15) . "3" . rand(10, 20);
                        break;
                    case '3':
                        $now  = Carbon::now();
                        $end  = $user->resetPasswordCodeCreationdate;
                        $hour = $now->diffInHours($end);
                        if ($hour >= 6) {
                            $user->resetPasswordCode = null;
                            $user->resetPasswordCodeCreationdate = null;
                            $user->save();
                            $code = $this->generate_code(15) . "1" . rand(10, 20);
                        } else {
                            return response()->json(__('auth.ResetPasswordAfterSix'), 400);
                        }
                        break;
                    default:
                        return response()->json(__('auth.wrong'), 400);
                        break;
                }
            } else {
                $code = $this->generate_code(15) . "1" . rand(10, 20);
            }
            $user->resetPasswordCode = $code;
            $user->resetPasswordCodeCreationdate = Carbon::now()->toDateTimeString();
            if ($user->save()) {
                app('App\Http\Controllers\LogController')->Logging("users", $user, "reset", "resetPasswordCode");
                $this->mail($user->name, $user->email, $user->token, $user->resetPasswordCode, $user->resetPasswordCodeCreationdate);
            }
        } else {
            return response()->json(__('auth.NoUser'), 400);
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
        if (empty($user)) {
            return response()->json(__('auth.ForgetPasswordPage'), 400);
        }
        $now = Carbon::now()->addHours(4);
        $days = $now->diffInDays($user->resetPasswordCodeCreationdate);
        if ($days < 1) { //
            $user = User::find($user->id);
            $user->password = Hash::make($request->newPassword);
            $user->resetPasswordCode = null;
            $user->resetPasswordCodeCreationdate = null;
            $user->save();
            app('App\Http\Controllers\LogController')->Logging("users", $user, "reset", "password");
            return response()->json(__('auth.updatePasswordSuccess'), 200);
        } else {
            $user = User::find($user->id);
            $user->resetPasswordCode = null;
            $user->resetPasswordCodeCreationdate = null;
            $user->save();
            return response()->json(__('auth.ExpiredCode'), 400);
        }
    }

    public function ConfirmEmail(Request $request)
    {
        /** validation */
        $rules = [
            'emailConfirmationCode'   => 'required|min:6',
        ];
        $this->validate($request, $rules);

        /** check code */
        $user = User::where("emailConfirmationCode", $request->emailConfirmationCode)->first();
        if (empty($user)) {
            return response()->json(__('auth.codeNotCorrect'), 400);
        }

        /** set user data */
        $user->isEmailVerified = 1;
        $user->emailConfirmationCode = null;
        $user->save();

        return response()->json(__('auth.emailVerified'), 200);
 
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

    public function mail($name, $email, $token, $code, $time)
    {
        Mail::to($email)->send(new SendResetPasswordLink($name, $email, $token, $code, $time));

        if (Mail::failures()) {
            return response()->json(__('auth.mailNotSend'), 422);
        }
    }
}
