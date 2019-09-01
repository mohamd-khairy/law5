<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class CertificateCopyRequest extends Model
{
    
    protected $table="certificateCopyRequest";

    protected $dates = ['issuedDate'];
    protected $fillable = [
        "applicantId", "certificateId", "count", "isChamberMember", "isSubscriptionFeesPaid", "isIDAFeesPaid", "isFEIFeesPaid", "isIssued", "isDeleted", "issueDate"
    ];


    // public static $rules = [
    //     "certificateTypeId" => "required|integer",
    //     "requestId" => "required|integer",
    //     "certificateNumber" => "nullable|integer",
    //     "isWinnedTender" => "boolean",
    //     "isDeleted" => "boolean",
    //     "issuedDate" => "nullable",
    //     "managerApproveDate" => "nullable",
    // ];

    protected $casts = [
        'isChamberMember' => 'boolean',
        'isSubscriptionFeesPaid' => 'boolean',
        'isIDAFeesPaid' => 'boolean',
        'isFEIFeesPaid' => 'boolean',
        'isIssued' => 'boolean',
        'isDeleted' => 'boolean',

    ];

    public $timestamps = true;


    public function certificate(){

        return $this->hasMany("App\Model\Certificate", "id");

    }

    public function applicant(){

        return $this->hasMany("App\Model\Applicant", "id");

    }

}
