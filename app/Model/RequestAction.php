<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestAction extends Model
{

    protected $table="request_actions";

    protected $fillable = [
        "actionId", "requestId", "byUserId", "toUserId","comment","isAuto"
    ];

    protected $dates = [];

    public $timestamps = true;

    public function actions()
    {
        return $this->belongsTo("App\Model\Action", "actionId");
    }
    public function requests()
    {
        return $this->belongsTo("App\Model\RequestModel", "requestId");
    }
    public function user_by()
    {
        return $this->belongsTo("App\Model\User", "byUserId");
    }
    public function user_to()
    {
        return $this->belongsTo("App\Model\Employee", "toUserId");
    }
}
