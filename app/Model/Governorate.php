<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ["nameEn", "nameAr"];

    protected $dates = [];

    public static $rules = [
        "nameEn"            => "required",
        "nameAr"            => "required",
    ];
}
