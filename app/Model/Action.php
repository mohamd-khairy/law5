<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ["nameEn", "nameAr", "key"];

    protected $dates = [];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
        "key"    => "required",
    ];

    public $timestamps = false;

    // Relationships
}
