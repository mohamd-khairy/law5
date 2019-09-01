<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class RequestModel extends Model
{
    use SoftDeletes;

    protected $table = "requests";

    protected $fillable = [
        "statusId", "applicantId", "assessmentId", "employeeId", "sectorId", "sectionId","openedByEmployeeId",
        "representativeName", "representativeNationalId", "representativeTypeId", "representativeDelegationNumber",
        "representativeDelegationIssuedBy", "representativeTelephone", "representativeFax", "representativeMobile","chamberMemberNumber",
        "representativeEmail", "representativeMailingAddress","industrialRegistry",
        "isOriginalsReceived", "isChamberMember", "isSubscriptionFeesPaid", "isDeleted"
    ];


    protected $dates = [];

    public static $rules = [
        "statusId" => "nullable|integer",
        "applicantId" => "nullable|integer",
        "assessmentId" => "nullable|integer",
        "employeeId" => "nullable|integer",
        "sectorId" => "nullable|integer",
        "sectionId" => "nullable|integer",

        "representativeName" => "required|max:255",
        "representativeNationalId" => "required|max:255",
        "representativeTypeId" => "required|integer",

        "representativeDelegationNumber" => "required|max:255",
        "representativeDelegationIssuedBy" => "required|max:255",

        "chamberMemberNumber" => "required|numeric",

        "industrialRegistry" => "required|max:255",
        "representativeMailingAddress" => "required|max:255",
        "representativeFax" => "nullable|numeric",
        "representativeTelephone" => "required|numeric",
        "representativeMobile" => "required|numeric",
        "representativeEmail" => "required|email|max:255",
        "isOriginalsReceived" => "boolean",
        "isChamberMember" => "boolean",
        "isIDAFeesPaid" => "boolean",
        "isFEIFeesPaid" => "boolean",
        "isSubscriptionFeesPaid" => "boolean",
        "isDeleted" => "boolean",

    ];
    
    public static $rules_representativeTypeId = [
        "statusId" => "nullable|integer",
        "applicantId" => "nullable|integer",
        "assessmentId" => "nullable|integer",
        "employeeId" => "nullable|integer",
        "sectorId" => "nullable|integer",
        "sectionId" => "nullable|integer",

        "representativeName" => "required|max:255",
        "representativeNationalId" => "required|max:255",
        "representativeTypeId" => "required|integer",

        "representativeDelegationNumber" => "nullable|max:255",
        "representativeDelegationIssuedBy" => "nullable|max:255",

        "chamberMemberNumber" => "required|numeric",

        "industrialRegistry" => "required|max:255",
        "representativeMailingAddress" => "required|max:255",
        "representativeFax" => "nullable|numeric",
        "representativeTelephone" => "required|numeric",
        "representativeMobile" => "required|numeric",
        "representativeEmail" => "required|email|max:255",
        "isOriginalsReceived" => "boolean",
        "isChamberMember" => "boolean",
        "isIDAFeesPaid" => "boolean",
        "isFEIFeesPaid" => "boolean",
        "isSubscriptionFeesPaid" => "boolean",
        "isDeleted" => "boolean",

    ];

    public static $rules_update = [
        "statusId" => "nullable|integer",
        "applicantId" => "nullable|integer",
        "assessmentId" => "nullable|integer",
        "employeeId" => "nullable|integer",
        "sectorId" => "nullable|integer",
        "sectionId" => "nullable|integer",

        "representativeName" => "required|max:255",
        "representativeNationalId" => "required|max:255",
        "representativeTypeId" => "required|integer",
        "representativeDelegationNumber" => "required|max:255",
        "representativeDelegationIssuedBy" => "required|max:255",

        "chamberMemberNumber" => "required|numeric",

        "representativeMailingAddress" => "required|max:255",
        "representativeFax" => "nullable|numeric",
        "representativeTelephone" => "required|numeric",
        "representativeMobile" => "required|numeric",
        "representativeEmail" => "required|email|max:255",
        "industrialRegistry" => "required|max:255",

        "isOriginalsReceived" => "boolean",
        "isChamberMember" => "boolean",
        "isIDAFeesPaid" => "boolean",
        "isFEIFeesPaid" => "boolean",
        "isSubscriptionFeesPaid" => "boolean",
        "isDeleted" => "boolean",

    ];

    public $timestamps = true;


    public function sectors()
    {
        return $this->belongsTo("App\Model\Sector", "sectorId");
    }
    public function status()
    {
        return $this->belongsTo("App\Model\RequestStatus", "statusId");
    }
    public function users_applicant()
    {
        return $this->belongsTo("App\Model\Applicant", "applicantId");
    }
    public function users_employee()
    {
        return $this->belongsTo("App\Model\Employee", "employeeId");
    }
    public function assessments()
    {
        return $this->belongsTo("App\Model\Assessment", "assessmentId");
    }
  
    public function sections()
    {
        return $this->belongsTo("App\Model\Section", "sectionId");
    }
    public function employees()
    {
        return $this->belongsTo("App\Model\Employee", "employeeId");
    }
    public function certificate()
    {
        return $this->hasMany("App\Model\Certificate","requestId");
    }
    
}
