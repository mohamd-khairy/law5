<?php
 namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\AssessmentMethod;
use App\Model\Sector;
use App\Model\Section;

class Chamber extends Model {

    use SoftDeletes;

    protected $fillable = ["nameEn", "nameAr", "assessmentMethod" , "isDeleted"];

    protected $dates = ["deleted_at"];

    public static $rules = [
        "nameEn" => "required",
        "nameAr" => "required",
    ];

    public $timestamps = false;

    public function assessment_method()
    {
       return $this->hasOne(AssessmentMethod::class);
    }

    public function sectors()
    {
        return $this->hasMany(Sector::class);
    }

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function assessment(){

        return $this->hasMany("App\Model\Assessment", "chamberId");
    }

    public function employee(){

        return $this->hasMany("App\Model\Employee", "chamberId");
    }
 
}
