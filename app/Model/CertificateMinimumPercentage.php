<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CertificateMinimumPercentage extends Model
{
    
    protected $table="certificateMinimumPercentage";

    protected $fillable = [
        "certificateTypeId", "fromDate", "minimumPercentage", "isDeleted", "deleted_at"
    ];

    protected $casts = [
        "isDeleted" => 'boolean',
    ];
    
    public $timestamps = true;


    public function certificateType(){

        return $this->hasMany("App\Model\CertificateType", "id");

    }
}
