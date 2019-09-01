<?php

namespace App\Http\Services;

use Carbon\Carbon;

use App\Model\Certificate;
use App\Model\CertificateType;

class CertificateService 
{

     /**
     *
     * Calculate the expiary date of a certificate based on its type.
     *
     * @param    type  $certificate type, based on it the date calculation will differ.
     * @return      Date
     *
     */
    public static function calcExpiryDate(Certificate $cert)
    {
        $type = CertificateType::findOrFail($cert->certificateTypeId);       

        if ($type->name == "Law5")
        {
            $issueDate = Carbon::parse($cert->issueDate);
            $expireDate = $issueDate->endOfYear();
        }
        else if ($type->name == "Export Fund")
        {
            $issueDate = Carbon::parse($cert->issueDate);

            // add 1 Year and 1 Day to the issue date.
            $expireDate = $issueDate->addYear()->addDay()->startOfDay();
        }

        return $expireDate->toDateTimeString();
    }


    public static function isExpired(Certificate $cert)
    {
        // check expire date and current date
        if (Carbon::parse(self::calcExpiryDate($cert))->gt(Carbon::now())) 
            return false;
        else 
            return true;
        
    }
}
