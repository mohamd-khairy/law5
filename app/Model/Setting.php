<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    


    protected $fillable = [
        "automaticAssignDelay", "automaticIDAApproveDelay", "law5CertificatePercentage", "exportFundPercentage","executiveManagerName",
        "mailServer", "mailServerPort", "mailEnableSSL", "fromEmail", "fromEmailPassword"
    ];

    protected $dates = [];


    public static $rules_setting = [
        "automaticAssignDelay" => "required|integer",
        "automaticIDAApproveDelay" => "required|integer",
        "law5CertificatePercentage" => "required|integer|min:0|max:100",
        "exportFundPercentage" => "required|integer|min:0|max:100",
        "executiveManagerName" => "required|string",
    ];

    public static $rules_email = [

        "mailServer" => "required|string",

        "mailServerPort" => "required|integer",

        "mailEnableSSL" => "required|boolean",

        "fromEmail" => "required|string",

        "fromEmailPassword" => "required|string",
    ];

    public $timestamps = false;

    protected $hidden = [
        'fromEmailPassword',
    ];
}
