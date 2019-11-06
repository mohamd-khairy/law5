<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RepresentativeType extends Model
{
    protected $fillable = ["nameEn", "nameAr", "key" ,"needAttachment"];

    protected $dates = [];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
        "key"    => "required",
        "needAttachment"    => "boolean",
    ];

    public $timestamps = false;

}
