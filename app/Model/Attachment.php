<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;


    protected $fillable = ["requestId", "applicantId", "isRepresentativeProof", "relativePath", "isDeleted"];


    public static $rules = [
        "requestId" => "nullable|integer",
        "applicantId" => "nullable|integer",
        "isRepresentativeProof" => "nullable|boolean",
        "relativePath" => "required|file",
        "isDeleted" => "boolean",
    ];

    protected $dates = [];

    public $timestamps = true;
}
