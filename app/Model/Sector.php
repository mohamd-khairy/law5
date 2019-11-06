<?php 
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model {

    use SoftDeletes;

    protected $fillable = ["nameEn", "nameAr", "isDeleted"];

    protected $dates = ["deleted_at"];
 
    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
    ];

    public $timestamps = false;

    // Relationships

    public function requests(){

        return $this->hasMany("App\Model\RequestModel", "sectorId");

    }
}
