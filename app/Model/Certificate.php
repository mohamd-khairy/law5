<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table="certificates";

    protected $fillable = [
        "certificateTypeId", "requestId", "certificateNumber", "isWinnedTender","isDeleted" ,"issuedDate","managerApproveDate"
    ];


    public static $rules = [
        "certificateTypeId" => "required|integer",
        "requestId" => "required|integer",
        "certificateNumber" => "nullable|integer",
        "isWinnedTender" => "boolean",
        "isDeleted" => "boolean",
        "issuedDate" => "nullable",
        "managerApproveDate" => "nullable",
    ];

    public $timestamps = true;

    public function requests(){

        return $this->belongsTo("App\Model\RequestModel", "requestId");

    }

    public function certificateCopyRequests(){

        return $this->belongsTo("App\Model\CertificateCopyRequest");
    }
}
