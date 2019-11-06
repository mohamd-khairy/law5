<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrustedDevices extends Model
{
    protected $fillable = ['userId' , 'trustToken'];
}
