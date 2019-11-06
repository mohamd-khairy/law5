<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = ['isTwofactorAUthenticationEnabled' , 'userId'];

    public static $rules = [
        "isTwofactorAUthenticationEnabled" => "required|boolean"
    ];

    public function userSetting()
    {
        return $this->hasOne('App\Model\User');
    }
}
