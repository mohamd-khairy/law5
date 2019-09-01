<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ["nameEn", "nameAr", "governorateId"];

    protected $dates = [];

    public static $rules = [
        "nameEn"            => "required",
        "nameAr"            => "required",
        "governorateId"     => "required",  
    ];

    public $timestamps = false;

    // Relationships
}
