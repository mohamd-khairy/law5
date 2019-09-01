<?php namespace App\Http\Controllers;

use App\Model\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{

    const MODEL = "App\Model\Setting";

    use RESTActions;

    public function setting(Request $request)
    {
        $this->validate($request, Setting::$rules_setting);
        $data_setting = Setting::first();
        $old=$data_setting->getOriginal();
        $data_setting->update($request->all());
        app('App\Http\Controllers\LogController')->Logging_update("settings" , $data_setting,$old);

        $reponse = [
            "automaticAssignDelay" => $data_setting->automaticAssignDelay,
            "automaticIDAApproveDelay" => $data_setting->automaticIDAApproveDelay,
            "law5CertificatePercentage" => $data_setting->law5CertificatePercentage,
            "exportFundPercentage" => $data_setting->exportFundPercentage,
            "executiveManagerName" => $data_setting->executiveManagerName
        ];
        return $this->respond(Response::HTTP_OK, $reponse);
    }

    public function setting_mail(Request $request)
    {
        $this->validate($request, Setting::$rules_email);
        $data_setting = Setting::first();
        $data_setting->mailServer = $request->mailServer;
        $data_setting->mailServerPort = $request->mailServerPort;
        $data_setting->mailEnableSSL = $request->mailEnableSSL;
        $data_setting->fromEmail = $request->fromEmail;
        $data_setting->fromEmailPassword = Hash::make($request->fromEmailPassword);
        $old=$data_setting->getOriginal();
        $data_setting->save();
        app('App\Http\Controllers\LogController')->Logging_update("settings" , $data_setting , $old);

        $reponse = [
            "mailServer" => $data_setting->mailServer,
            "mailServerPort" => $data_setting->mailServerPort,
            "mailEnableSSL" => $data_setting->mailEnableSSL,
            "fromEmail" => $data_setting->fromEmail,
            "fromEmailPassword" => $data_setting->fromEmailPassword
        ];
        return $this->respond(Response::HTTP_OK, $reponse);
    }

    public function all_settings()
    {
        $data_setting = Setting::first();
        $reponse = [
            "automaticAssignDelay" => $data_setting->automaticAssignDelay,
            "automaticIDAApproveDelay" => $data_setting->automaticIDAApproveDelay,
            "law5CertificatePercentage" => $data_setting->law5CertificatePercentage,
            "exportFundPercentage" => $data_setting->exportFundPercentage,
            "executiveManagerName" => $data_setting->executiveManagerName

        ];
        return response()->json($reponse, 200);
    }

    public function mail_settings()
    {
        $data_setting = Setting::first();
        $reponse = [
            "mailServer" => $data_setting->mailServer,
            "mailServerPort" => $data_setting->mailServerPort,
            "mailEnableSSL" => $data_setting->mailEnableSSL,
            "fromEmail" => $data_setting->fromEmail,
            "fromEmailPassword" => $data_setting->fromEmailPassword
        ];
        return response()->json($reponse, 200);
    }

    public function SendMailSettings()
    {
        $setting = Setting::first();
        config("mail.driver", "smtp");
        config("mail.host", $setting->mailServer);
        config("mail.port", $setting->mailServerPort);
        config("mail.from.address", $setting->fromEmail);
        config("mail.from.name", $setting->fromEmail);
        config("mail.encryption", ($setting->mailEnableSSL) ? "tls" : "");
        config("mail.username", $setting->fromEmail);
        config("mail.password", $setting->fromEmailPassword);
    }
    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
