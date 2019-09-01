<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table="log";

    protected $fillable = ["RecordId", "createdByUserId","TableName", "Column","Action", "oldValue","newValue","createdAt"];

    protected $dates = [];

    public static $rules = [
        "RecordId"            => "required|integer",
        "createdByUserId"     => "required|integer",
        "TableName"           => "required",
        "Column"              => "nullable",
        "Action"              => "required",
        "oldValue"            => "nullable",
        "newValue"            => "nullable",
        "createdAt"           => "required",
    ];

    public $timestamps = false;
 
    public function user_by()
    {
        return $this->belongsTo("App\Model\User", "createdByUserId");
    }
}
