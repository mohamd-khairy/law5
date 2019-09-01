<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = ["nameEn", "nameAr", "isDeleted"];

   // protected $dates = [];

    public static $rules = [
        "nameEn" => "required|max:255",
        "nameAr" => "required|max:255",
       
    ]; 

    public $timestamps = false;
}
