<?php

namespace App\Http\Services;

use Carbon\Carbon;

use App\Model\Certificate;
use App\Model\CertificateType;

use App\Model\Setting;
use App\Model\Applicant;


class CertificateService 
{

     /**
     *
     * Calculate the expiary date of a certificate based on its type.
     *
     * @param    type  $certificate type, and based on it the date calculation will differ.
     * @return      Date String
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
        $expiryDate = self::calcExpiryDate($cert);
        $carbonExpiryDate = Carbon::parse($expiryDate);
        
        // check expire date and current date
        if (Carbon::now()->gt($carbonExpiryDate)) //gt ~ greater than
            return true;
        else 
            return false;
        
    }

    public static function IsExpiryDateInBetween($expiryDate, $fromDate, $toDate)
    {
        $expiryDate = Carbon::parse($expiryDate);
        $fromDate = Carbon::parse($fromDate);
        $toDate = Carbon::parse($toDate);

        return $expiryDate->between($fromDate, $toDate);
    }

    public static function generateCertificatePDF(Certificate $certificate, $certificateNumber)
    {
        $applicant = Applicant::with("government", "city","sector")->where("id", $certificate->requests->applicantId)->first();
        $setting = Setting::first();
        $assessment = app('App\Http\Controllers\AssessmentController')->GetAssessment($certificate->requests->assessmentId);
        $assessment = json_decode($assessment->content(), true);

        //make Pdf
        $pdf = app()->make('dompdf.wrapper');
        $pdf->setPaper('A4', 'landscape');

        if ($certificate->certificateTypeId == 1) {
            $manufactor=$assessment['manufactoringByOthers'];
            $pdf->loadView("export", compact('certificate', 'assessment', 'applicant', 'setting' ,"manufactor", 'certificateNumber'));
        } elseif ($certificate->certificateTypeId == 2) {
            $pdf->loadView("law5", compact('certificate', 'assessment', 'applicant', 'setting', 'certificateNumber'));
        }
        return response()->make($pdf->stream(), 200, ['content-type' =>  'application/pdf']);
    }
}