<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OldCertificate extends Model
{
    protected $table="old_certificates";

    protected $primaryKey = 'id'; 

    public $incrementing = false;

    protected $dates = ['startDate', 'endDate'];
    
    protected $fillable = ["certificateTypeId", "companyName", "companyActivity", "productName", "copy", "companyAddress", "companyCity", "companyRegNo", "localPercentage", "manufacturingByOthers", "manufacturingCompanyName", "manufacturingCompanyIndustrialRegistry", "issueYear", "startDate", "endDate"
    ];

    public function scopeSearchCompanyName($query, string $scope = 'where', $companyName)
    {
        return $query->$scope('companyName', 'LIKE', '%'.$companyName.'%');
    }

    public function scopeSearchChamber($query, string $scope = 'where', $chamber)
    {
        //companyActivity is the chamber   
        return $query->$scope('companyActivity', 'LIKE', '%'.$chamber.'%'); 
    }

    public function scopeSearchCertificateNumber($query, string $scope = 'where', $certificateNumber)
    {
        //id is the certificate number
        return $query->$scope('id', 'LIKE', '%'.$certificateNumber.'%');
    }

    public static $rules = [
        // 'id' => 'string|required|unique:id',
        // 'certificateTypeId' => 'integer|nullable',
        // 'companyName' => 'string|',
        // 'companyActivity' => 'string|',
        // 'productName' => 'string|',
        // 'copy' => 'integer|',
        // 'companyAddress' => 'string|',
        // 'companyCity' => 'string|',
        // 'companyRegNo' => 'string|',
        // 'localPercentage' => 'numeric|',
        // 'manufacturingByOthers' => 'boolean|nullable',
        // 'manufacturingCompanyName' => 'string|',
        // 'manufacturingCompanyIndustrialRegistry' => 'string|',
        // 'issueYear' => 'integer|',
        // 'startDate' => 'date|',
        // 'endDate' => 'date|',
    ];

    protected $casts = [
        'manufacturingByOthers' => 'boolean',
    ];


    public $timestamps = true;

}
