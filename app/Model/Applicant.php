<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{

    // 
    protected $fillable = [
        "facilityName",
        "legalEntityIdentifier",
        "managerName",
        "sectorId",
        "userId",
        "isInsideIndusterialArea",
        "governorateId",
        "cityId",
        "indusrialAreaName",
        "blockNumber",
        "areaNumber",
        "areaOrDistrict",
        "buildingNumber",
        "telephone",
        "fax",
        "taxCard",
        "authorityAcceptanceNumber",
        "commercialRecord",
        "licenseNumber",
        "factoryArea",
        "investmentCostAtConstructionTime",
        "currentInvestmentCosts",
        "extentOfEnvironmentalConditionsImplementationAndTrashDisposal",
        "extentOfDangerousMaterialsUsage",
        "experienceTypeId",
        "annualInsuranceValue",
        "certificatesAndExtentOfSpecificationsMatching",
        "insuredWorkersCount",
        "entryNumberInIndusterialRecord",
        "yearOfFirstCertificate",
        "expirationDate",
        "attachmentIds"
    ];

    // protected $dates = [];

    public static $rules = [
        "facilityName" => "required",
        "legalEntityIdentifier" => "required",
        "managerName" => "required",
        "sectorId" => "required",
        "isInsideIndusterialArea" => "required",
        "governorateId" => "required",
        "cityId" => "required",
        "indusrialAreaName" => "nullable|required_if:isInsideIndusterialArea,".true,
        "blockNumber" => "nullable|required_if:isInsideIndusterialArea,".true,
        "areaNumber" => "nullable|required_if:isInsideIndusterialArea,".true,
        "areaOrDistrict" => "nullable|required_if:isInsideIndusterialArea,".false,
        "buildingNumber" => "nullable|required_if:isInsideIndusterialArea,".false,
        "telephone" => "required",
        "fax" => "nullable",
        "taxCard" => "required",
        "authorityAcceptanceNumber" => "required",
        "commercialRecord" => "required",
        //"licenseNumber" => "required", //not anymore
        "factoryArea" => "required|max:13",
        "investmentCostAtConstructionTime" => "required|max:13",
        "currentInvestmentCosts" => "required|max:13",
        "extentOfEnvironmentalConditionsImplementationAndTrashDisposal" => "required|max:13",
        "extentOfDangerousMaterialsUsage" => "required|max:13",
        "experienceTypeId" => "nullable",
        "annualInsuranceValue" => "required|max:13",
        "certificatesAndExtentOfSpecificationsMatching" => "required",
        "insuredWorkersCount" => "required",
        "entryNumberInIndusterialRecord" => "required",
        "yearOfFirstCertificate" => "required",
        "expirationDate" => "required",
        "attachmentIds.*" => "required",

    ];


    public $timestamps = true;

    public function requests(){

        return $this->hasMany("App\Model\RequestModel", "applicantId");

    }

    public function sector(){
        return $this->belongsTo("App\Model\Sector" ,"sectorId");
    }

    public function government(){
        return $this->belongsTo("App\Model\Governorate" ,"governorateId");
    }

    public function city(){
        return $this->belongsTo("App\Model\City" ,"cityId");
    }

    public function certificateCopyRequests(){

        return $this->belongsTo("App\Model\CertificateCopyRequest");
    }
}
