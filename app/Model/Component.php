<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        "assessmentId",
        "componentName",
        "unit",
        "quantity",
        "unitPrice",
        "supplier",
        "rate",
        "CIF",
        "isPackaging",
        "isImported"
    ];

    protected $dates = [];

    public $timestamps = false;

    public function assessment()
    {
        return $this->belongsTo('App\Model\Assessment');
    }
    
    
}
