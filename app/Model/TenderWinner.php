<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TenderWinner extends Model
{
    protected $fillable = ["certificateId", "TenderDescription", "TenderDate","TenderValue"];

    protected $dates = [];

    public static $rules = [
        "certificateId"            => "required",
        "TenderDescription"            => "required",
        "TenderValue"            => "required",
        "TenderDate"            => "required",
    ];

    public $timestamps = true;

    // Relationships
}
