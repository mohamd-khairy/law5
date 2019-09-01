<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    

    protected $fillable = [
        "applicantId",
        "productName",
        "chamberId",
        "manufactoringByOthers",
        "manufactoringCompanyName",
        "manufactoringCompanyIndustrialRegistry",
        "isTotals",
        "annualProductionCapacity",
        "powerResources",
        "localSpareParts",
        "importedSpareParts",
        "researchAndDevelopment",
        "wages",
        "annualDepreciation",
        "administrativeExpenses",
        "marketingExpenses",
        "otherExpenses",
        "localComponentsTotals",
        "localPackagingComponentsTotals",
        "importedComponentsTotals",
        "importedPackagingComponentsTotals",
        "isDeleted",
        "assessmetDate"
    ];

    protected $dates = [];

    public $timestamps = false;

    public function components(){

        return $this->hasMany("App\Model\Component","assessmentId");

    }

    public function requests(){

        return $this->hasMany("App\Model\RequestModel", "assessmentId");

    }

    public function chamber(){
        return $this->belongsTo("App\Model\Chamber" ,"chamberId");
    }


}
