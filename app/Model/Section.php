<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    
    protected $fillable = ["nameEn", "nameAr", "chamberId", "isDeleted"];

    protected $dates = ["deleted_at"];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
        "chamberId" => "required|integer",
       
    ];

    public $timestamps = true;

    public function requests(){

        return $this->hasMany("App\Model\RequestModel", "sectionId");

    }
 
    public function chamber(){
        return $this->belongsTo("App\Model\Chamber" ,"chamberId");
        }
}
 