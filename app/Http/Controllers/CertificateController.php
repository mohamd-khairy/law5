<?php namespace App\Http\Controllers;
use App\Model\RequestModel;
use App\Model\Role;
use App\Model\Certificate;
use App\Model\CertificateType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Model\RequestStatus;
use App\Model\RequestAction;
use App\Model\TenderWinner;
use App\Model\User;
use App\Model\Action;
use Carbon\Carbon;
use App\Model\Applicant;
use App\Model\Assessment;
use App\Model\Attachment;
use App\Model\Setting;
use App\Http\Services\CertificateService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    const MODEL = "App\Model\Certificate";
    use RESTActions;

    public function IssueCertificates(Request $request)
    {
        $rules = [
            'requestId'  => 'integer|required'
        ];
        $this->validate($request, $rules);
        $resquestId = $request->requestId;
        $req = RequestModel::find($resquestId);
        if (empty($req)) {
            return response()->json("this request not found", 400);
        }
        $req->statusId = RequestStatus::where("key", "Issued")->first()->id;
        $old=$req->getOriginal();
        if ($req->save()) {
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
            $cert = Certificate::where("requestId", $resquestId)->get();
            $lastCert = Certificate::orderBy("issueDate", "desc")->latest()->first();
            if (empty($lastCert)) {
                $lastCert = 0;
            } else {
                $lastCert = $lastCert->certificateNumber;
            }
            $response = array();
            foreach ($cert as $c) {
                $certificate = Certificate::find($c->id);
                $certificate->issueDate = Carbon::now()->toDateTimeString();
                $certificate->certificateNumber = $lastCert + 1;
                $old=$certificate->getOriginal();
                $certificate->save();
                app('App\Http\Controllers\LogController')->Logging_update("certificates",$certificate,$old);
                array_push(
                    $response,
                    [
                        "certificateTypeId" => $certificate->certificateTypeId,
                        "certificateNumber" => $certificate->certificateNumber,
                        "certificateURL"    =>  "",//url("/api/pdf?cert_id=" . $c->id)
                        "certificateId"     =>  $c->id
                    ]
                );
            }
            $action = new RequestAction();
            $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
            if (!empty($header2)) {
                $user = User::where('token', $header2)->first();
            } else {
                return response("unathorize", 401);
            }
            $action->requestId = $resquestId;
            $action->actionId = Action::where("key", "IssueCertificates")->first()->id;
            $action->byUserId = $user->id;
            $action->toUserId = null;//$req->employeeId;
            $action->comment = null;//$request->comment;
            $action->isAuto = 0;
            $action->save();
            return response()->json($response, 200);
        }
    }

    public function SetCertificateAsWinner(Request $request)
    {
        $rules = [
            'requestId'               => 'integer|required',
            'TenderDate'              => 'required',
            'TenderDescription'       => 'required',
            'TenderValue'             => 'required',
        ];
        $this->validate($request, $rules);
        $cert = Certificate::where("requestId", $request->requestId)->where("certificateTypeId", 2)->first();
        //return $cert;
        if (!empty($cert)) {
            $cert = Certificate::find($cert->id);
            $cert->isWinnedTender = 1;
            $old=$cert->getOriginal();
            $cert->save();
            app('App\Http\Controllers\LogController')->Logging_update("certificates",$cert,$old);
            $tender = new TenderWinner();
            $tender->certificateId = $cert->id;
            $tender->TenderDescription = $request->TenderDescription;
            $tender->TenderValue = $request->TenderValue;
            $tender->TenderDate = $request->TenderDate;
            if($tender->save()){
                app('App\Http\Controllers\LogController')->Logging_create("tender_winners",$tender);
                $action = new RequestAction();
                $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
                if (!empty($header2)) {
                    $user = User::where('token', $header2)->first();
                } else {
                    return response("unathorize", 401);
                }
                $action->requestId = $request->requestId;
                $action->actionId = Action::where("key", "Save")->first()->id;
                $action->byUserId = $user->id;
                $action->toUserId = null;//$req->employeeId;
                $action->comment = null;//$request->comment;
                $action->isAuto = 0;
                $action->save();
            }
            
            return response()->json("done ", 200);
        } else {
            return response()->json("there is no cert with type law5 ", 400);
        }
    }

    public function listOfCertificate(Request $request, $req_id)
    {
        $cert = Certificate::where("requestId", $req_id)->get();
        if (empty($cert)) {
            return response()->json("there is no certificate for this request yet...", 400);
        }
        $response = array();
        foreach ($cert as $c) {
            $expDate = CertificateService::calcExpiryDate($c);
            $certType = CertificateType::findOrFail($c->certificateTypeId); // this will return 404 if failed
            array_push(
                $response,
                [
                    "certificateTypeId" => $c->certificateTypeId,
                    "certificateNumber" => $c->certificateNumber,
                    "certificateURL"    => " " ,//url("/api/pdf?cert_id=" . $c->id) //
                    "certificateId" =>  $c->id,
                    "expiryDate"=> $expDate,
                    "typeNameAr" => $certType->nameAr,
                    "typeNameEn" => $certType->name,
                ]
            );
        }
        return response()->json($response, 200);
    }
    
    public function pdf(Request $request)
    {
        $this->validate($request, ['cert_id' => 'required|integer']);
        $cert_id = $request->cert_id;
        //make Pdf
        $pdf = app()->make('dompdf.wrapper');
        $pdf->setPaper('A4', 'landscape');
        $certificate = Certificate::with("requests")->where("id", $cert_id)->first();
        $applicant = Applicant::with("government", "city","sector")->where("id", $certificate->requests->applicantId)->first();
        $setting = Setting::first();
        $assessment = app('App\Http\Controllers\AssessmentController')->GetAssessment($certificate->requests->assessmentId);
        $assessment = json_decode($assessment->content(), true);
        if ($certificate->certificateTypeId == 2) {
            $manufactor=$assessment['manufactoringByOthers'];
            $pdf->loadView("export", compact('certificate', 'assessment', 'applicant', 'setting' ,"manufactor"));
        } elseif ($certificate->certificateTypeId == 1) {
            $pdf->loadView("law5", compact('certificate', 'assessment', 'applicant', 'setting'));
        }
        return response()->make($pdf->stream(), 200, ['content-type' =>  'application/pdf']);
    }

    public function renewCertificate(Request $request)
    {
        $this->validate($request, ['cert_id' => 'required|integer']);
        $cert_id = $request->cert_id;
        $token = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($token)) {
            $user = User::where('token', $token)->first();
        } else {
            return response("unathorize", 401);
        }
        $dataOfCertificate = Certificate::with("requests")->where("id", $cert_id)->first();
        if (empty($dataOfCertificate)) {
            return response()->json("There is no certificate with this id .", 404);
        } 
        $applicant = Applicant::where("id", $dataOfCertificate->requests->applicantId)->first();
        if (empty($applicant)) {
            return response()->json("There is no applicant with this id .", 404);
        } 
        if($applicant->userId != $user->id){
            return response()->json("This certificate not belongs to this user .", 400);
        }
        
        return $this->CloneRequestAndAssessment($dataOfCertificate->requests->id , $cert_id);  
    }
   
    public function CloneRequestAndAssessment($request_id , $cert_id)
    {
        $reqq = RequestModel::find($request_id);
        //clone assessment
        $ass = Assessment::find($reqq->assessmentId);
        $ass  = $ass->replicate();
        $ass->save();
        //
        //clone request
        $req = $reqq->replicate();
        $req->assessmentId = $ass->id;
        $req->isRenewal = true;
        $req->originalRequestId = $request_id;
        $req->statusId = RequestStatus::where("key", "Draft")->first()->id;
        $req->save();
        //
        //for logging create request
        app('App\Http\Controllers\LogController')->Logging_create("requests" , $req);
        //for logging create assessment
        app('App\Http\Controllers\LogController')->Logging_create("assessment" , $ass);

        $this->CloneCertificate($cert_id , $req->id);  
        $this->CloneAttachments($request_id , $req->id);  

        return response()->json($req->id, 200);
    }

    public function CloneCertificate($cert_id , $new_request_id)
    {
        $cert = Certificate::find($cert_id);
        $cert  = $cert->replicate();
        $cert->requestId = $new_request_id;
        $cert->certificateNumber = null;
        $cert->issueDate = null;
        $cert->managerApproveDate = null;
        $cert->isWinnedTender = 0;
        $cert->isDeleted = 0;
        $cert->save();
        app('App\Http\Controllers\LogController')->Logging_create("certificate" , $cert);
    }
    
    public function CloneAttachments($request_id , $new_request_id)
    {
        $attachments = Attachment::where("requestId", $request_id)->get();
        if (!empty($attachments)) {
            foreach ($attachments as $attach) {
                $ext = explode('.', $attach->relativePath)[1];
                $fileName = time().Str::random(30).'.'.$ext;
                $att = $attach->replicate();
                $att->requestId = $new_request_id;
                $att->relativePath = $fileName;
                $att->save();
                if (file_exists($attach->relativePath)) {
                    File::copy(base_path("public/storage/upload/".$attach->relativePath) 
                    ,base_path("public/storage/upload/".$fileName));
                }
            }
        }
    }
}