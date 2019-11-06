<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestStatus extends Model
{
    protected $table="requeststatus";

    protected $fillable = ["nameEn", "nameAr", "key"];

    protected $dates = [];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
        "key"    => "required",
    ];

    public $timestamps = false;

    // Relationships

    public function requests(){

        return $this->hasMany("App\Model\RequestModel", "statusId");

    }
    
}

