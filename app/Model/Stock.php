<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['stockName', 'stockPrice', 'stockYear'];

}
