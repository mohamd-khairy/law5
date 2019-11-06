<?php
 namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CertificateType extends Model {

    protected $table="certificate_types";

    protected $fillable = ["name","nameAr"];

    protected $dates = [];

    public static $rules = [
        "name" => "required",
        "nameAr" => "required",
    ];

    public $timestamps = false;

    // Relationships

}
