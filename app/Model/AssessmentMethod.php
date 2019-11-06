<?php
 namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AssessmentMethod extends Model {

    public $translatable = ["nameEn", "nameAr"];

    protected $fillable = ["nameEn", "nameAr"];

    protected $dates = [];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
    ];

    public $timestamps = false;

    // Relationships

}
