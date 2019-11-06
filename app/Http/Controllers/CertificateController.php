<?php namespace App\Http\Controllers;
use App\Model\RequestModel;
use App\Model\Certificate;
use App\Model\CertificateType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Model\RequestStatus;
use App\Model\RequestAction;
use App\Model\TenderWinner;
use App\Model\Action;
use Carbon\Carbon;
use App\Model\Applicant;
use App\Model\Assessment;
use App\Model\Component;
use App\Model\Attachment;
use App\Model\CertificateCopyRequest;
use App\Http\Services\CertificateService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class CertificateController extends Controller
{
    const MODEL = "App\Model\Certificate";
    use RESTActions;

    public function IssueCertificates(Request $request)
    {
        /** validation */
        $this->validate($request,[
            'requestId'  => 'integer|required'
        ]);

        /** user data */
        $user = userData();

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
            $actionId = Action::where('key', 'Confirm')->first()->id;
            foreach ($cert as $c) {
        
                $certificate = Certificate::findOrFail($c->id);
                $requestId = $certificate->requests->id;
                $requestAction = RequestAction::where('requestId', $requestId)
                ->where('actionId', $actionId)->first();
        
                if(empty($requestAction)) {
                    
                    return response()->json("Request is not yet confirmed", 304);
                }
                $certificate->issueDate = $requestAction->created_at;
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
        /** validation */
        $this->validate($request,[
            'requestId'               => 'integer|required',
            'TenderDate'              => 'required',
            'TenderDescription'       => 'required',
            'TenderValue'             => 'required',
        ]);
        
        /** user data */
        $user = userData();
        
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
        $cert = Certificate::findOrFail($cert_id);

        if ($cert->requests->status->key == 'Issued') {
            //generate pdf
            return CertificateService::generateCertificatePDF($cert, $cert->certificateNumber);
        } else {
            return response()->json("Certificate is not issued yet.", 400);
       }
    }

    public function renewCertificate(Request $request)
    {
        /** validation */
        $this->validate($request, ['cert_id' => 'required|integer']);
       
        /** user data */
        $user = userData();

        $cert_id = $request->cert_id;
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
        $assOld = Assessment::find($reqq->assessmentId);
        $comp = Component::where('assessmentId' , $reqq->assessmentId)->get();
        $ass  = $assOld->replicate();
        $ass->save();

        //clone components
        foreach($comp as $com){
            $newCom=$com->replicate();
            $newCom->assessmentId = $ass->id;
            $newCom->save();
        }
        
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
                $ext = explode('.', $attach->relativePath);
                $fileName = time().Str::random(30).'.'. end($ext);
                $att = $attach->replicate();
                $att->requestId = $new_request_id;
                $att->relativePath = $fileName;
                $att->save();
                $path = Storage::disk('local')->path('upload/'.$attach->relativePath);
                $NewPath = Storage::disk('local')->path('upload/'.$fileName);
                if (file_exists($path)) {
                    File::copy($path , $NewPath);
                }
            }
        }
    }

    public function expired_and_notRenewed(Request $request)
    {
        $this->validate($request, [
            'toDate' => 'nullable|date|before:tomorrow',
            'fromDate' => 'nullable|date|before:tomorrow',
            'certificateNumber' => 'nullable|numeric',
            'pageSize'               => 'nullable|numeric|min:1',
            'pageIndex'              => 'nullable|numeric',
            'sortColumn'             => 'nullable|string',
            'sortDirection'          => 'nullable|string',    
            ]);

        $fromDate = $request->filled('fromDate')? carbon::parse($request->fromDate) : "0000-00-00";
        $toDate = $request->filled('toDate')? carbon::parse($request->toDate) : Carbon::now();
        $certificateNumber = $request->certificateNumber ?? null;
        $pageSize = $request->get("pageSize");
        $pageIndex = (!empty($request->get("pageIndex"))) ? $request->get("pageIndex") : 0;
        $sortColumn = (!empty($request->get("sortColumn"))) ? $request->get("sortColumn") : "created_at";
        $sortDirection = (!empty($request->get("sortDirection"))) ? $request->get("sortDirection") : "desc";

        $data=Certificate::with('requests')->whereHas("requests", function ($q){
            $q->where('isRenewal', false);
        });

        if (!empty($certificateNumber)) {
            $data = $data->where("certificateNumber" , $certificateNumber);
            
            if(count($data->get()) == 0)
                return response()->json("Not Found Certificates With this number!",404);
        }  

        $data = $data->orderBy('id' , $sortDirection);

        /**
         *  for caculate the count of certificate expired
         */
        $certificates = [];
        foreach ($data->get() as $cert) {
            $expired = CertificateService::isExpired($cert);
            $expiryDate = CertificateService::calcExpiryDate($cert);
            if ($expired && CertificateService::IsExpiryDateInBetween($expiryDate, $fromDate, $toDate)) {
                $certificates[] = $cert; 
            }
        }

        /** paggination */
        $count = count($certificates);
        $perPage = $pageSize;   
        $page = $pageIndex+1;
        if ($page > count($certificates) || $page < 1) { $page = 1; }
        if(empty($perPage)){
            $offset = 0;
            $perPage = null;
        }else{
            $offset = ($page * $perPage) - $perPage;
        } 
        $certificates = array_slice($certificates,$offset,$perPage);
        /** end Paggination */


        $response = array();
        foreach ($certificates as $cert) {
            $applicant = Applicant::where("id", $cert->requests->applicantId)->first();
            $assessment = Assessment::where('applicantId' , $applicant->userId)->first();
            $certType = CertificateType::find($cert->certificateTypeId);
            $expiryDate = CertificateService::calcExpiryDate($cert);
            array_push(
                $response, [
                    "id" => $cert->id,
                    "productName" => $assessment->productName,
                    "companyName"    => $applicant->facilityName,
                    "certificateNumber" =>  $cert->certificateNumber,
                    "certificateTypeNameAr" => $certType->nameAr,
                    "issueDate" => $cert->issueDate,
                    "expiredAt" => $expiryDate,
                ]
            );
        }

        if($sortColumn != 'id'){
            $response = collect($response);
            if(strtolower($sortDirection) == "asc"){

                $response=$response->sortBy($sortColumn)->values();
            }else{
                $response=$response->sortByDesc($sortColumn)->values();
            }
        }

        if (!empty($response)) {
            return response()->json([
                    "listCount" => $count,
                    "data" => $response
            ] ,200);
       }
    }

    public function get_certificates(Request $request)
    {
        /** validation */
        $this->validate($request,[
            'fromDate'               => 'nullable|date',
            'toDate'                 => 'nullable|date',
            'isExpired'              => 'nullable',
            'productName'            => 'nullable|string',
            'companyName'            => 'nullable|string',
            'certificateNumber'      => 'nullable|numeric',
            'pageIndex'              => 'numeric|min:0',
            'pageSize'               => 'numeric|min:1',
            'sortColumn'             => 'nullable|string',
            'sortDirection'          => 'nullable|string|in:asc,desc',   
        ]);

        /** user data */
        $user = userData();

        $fromDate = $request->fromDate ?? "0000-00-00";
        $toDate = $request->toDate ?? Carbon::now();
        $productName = $request->productName ?? null;
        $companyName = $request->companyName ?? null;
        $certificateNumber = $request->certificateNumber ?? null;
        $isExpired = !empty($request->isExpired) ? $request->isExpired : null;
        $pageSize = $request->pageSize;
        $pageIndex = $request->pageIndex ?? 0;
        $sortColumn = (!empty($request->get("sortColumn"))) ? $request->get("sortColumn") : "id";
        $sortDirection = (!empty($request->get("sortDirection"))) ? $request->get("sortDirection") : "desc";

        $data = Certificate::with("requests")->whereBetween('created_at', [$fromDate, $toDate]);

        if (!empty($productName)) {

            $assessment = Assessment::where("productName" , $productName)->first();

            if(empty($assessment))
                return response()->json(["Not Found Certificates With this product name!"],404);
            
            $data = $data->whereHas("requests", function ($q) use ($assessment) {
                $q->where('assessmentId', $assessment->id);
            });
        }

        if (!empty($companyName)) {
            $applicant = Applicant::where("facilityName" , $companyName)->first();
            
            if(empty($applicant))
                return response()->json(["Not Found Certificates With this company name!"],404);
            
            $data = $data->whereHas("requests", function ($q) use ($applicant) {
                $q->where('applicantId', $applicant->id);
            });
        }

        if (!empty($certificateNumber)) {
            $data = $data->where("certificateNumber" , $certificateNumber);
            
            if(count($data->get()) == 0)
                return response()->json("Not Found Certificates With this number!",404);
        }
      
        $expiredCertificate = [];
        $notExpiredCertificate = [];
        $allData = $data->get();
        if(!empty($isExpired) && $isExpired !== null){
            foreach ($allData as $value) {
                $expired = CertificateService::isExpired($value);
                if($expired == 1){
                    array_push($expiredCertificate, $value);  
                }elseif($expired == 0){
                    array_push($notExpiredCertificate, $value);  
                }
            }                
        }
        // $count = ($isExpired)? count($expiredCertificate) : count($notExpiredCertificate);
        if(!empty($isExpired) && ($isExpired == "true" || $isExpired == 1) ){
            $count =  count($expiredCertificate);
        }elseif(!empty($isExpired) && ($isExpired == "false" || $isExpired == 0)){
            $count = count($notExpiredCertificate);
        }else{
            $count = count($allData);
        }
        // $data=$data->paginate($pageSize, ['*'], 'page', $pageIndex + 1);
        $data = $data->get();

        $response = array();
        foreach ($data as $item) {
            $certType = CertificateType::find($item->certificateTypeId);

            $assessment = Assessment::find($item->requests->assessmentId);
            if(empty($assessment))
                return response()->json(["Not Found assessment for this certificate!"],404);

            $applicant = Applicant::find($item->requests->applicantId);
            if(empty($applicant))
                return response()->json(["Not Found company for this certificate!"],404);

            $expiredCert = CertificateService::isExpired($item);
            
            if(!empty($isExpired) && ($isExpired == "true" || $isExpired == 1) ){
                if($expiredCert){
                    array_push(
                        $response,
                        [
                            "id" => $item->id,
                            "productName" => $assessment->productName,
                            "companyName"    => $applicant->facilityName,
                            "certificateNumber" =>  $item->certificateNumber,
                            "certificateTypeNameAr" => $certType->nameAr,
                            "certificateTypeNameEn" => $certType->name,
                            "issueDate" => $item->issueDate,
                            "certificateUrl" => "/pdf?cert_id=".$item->id,
                            "isExpired" => $expiredCert,
                        ]
                    );  
                }
            }elseif(!empty($isExpired) && ($isExpired == "false" || $isExpired == 0)){
                if(!$expiredCert){

                    array_push(
                        $response,
                        [
                            "id" => $item->id,
                            "productName" => $assessment->productName,
                            "companyName"    => $applicant->facilityName,
                            "certificateNumber" =>  $item->certificateNumber,
                            "certificateTypeNameAr" => $certType->nameAr,
                            "certificateTypeNameEn" => $certType->name,
                            "issueDate" => $item->issueDate,
                            "certificateUrl" => "/pdf?cert_id=".$item->id,
                            "isExpired" => $expiredCert,
                        ]
                    );  
                }
            }else{
                array_push(
                    $response,
                    [
                        "id" => $item->id,
                        "productName" => $assessment->productName,
                        "companyName"    => $applicant->facilityName,
                        "certificateNumber" =>  $item->certificateNumber,
                        "certificateTypeNameAr" => $certType->nameAr,
                        "certificateTypeNameEn" => $certType->name,
                        "issueDate" => $item->issueDate,
                        "certificateUrl" => "/pdf?cert_id=".$item->id,
                        "isExpired" => $expiredCert,
                    ]
                );  
            }
        }

        
        if(strtolower($sortDirection) == "asc"){
            $response = collect($response);

            $response = $response->sortBy($sortColumn)->values();
        }else{
            $response = collect($response);

            $response = $response->sortByDesc($sortColumn)->values();
        }
        
        $response = $response->toArray();
        /** paggination */
        // $count = count($certificates);
        $perPage = $pageSize;   
        $page = $pageIndex+1;
        if ($page > count($response) || $page < 1) { $page = 1; }
        if(empty($perPage)){
            $offset = 0;
            $perPage = null;
        }else{
            $offset = ($page * $perPage) - $perPage;
        } 
        $response = array_slice($response,$offset,$perPage);
        /** end Paggination */

        return response()->json([
                "listCount" => $count,//count($response),
                "data" => $response
        ] ,200);
    }

    function renewAndCopiesCount(Request $request)
    {
        $rules = [
            'productName'            => 'nullable|string',
            'certificateNumber'      => 'nullable|numeric',
            'pageSize'               => 'nullable|numeric|min:1',
            'pageIndex'              => 'nullable|numeric',
            'sortColumn'             => 'nullable|string',
            'sortDirection'          => 'nullable|string|in:asc,desc',                
        ];
        $this->validate($request, $rules);

        $pageSize = $request->get("pageSize") ?? null;
        $pageIndex = (!empty($request->get("pageIndex"))) ? $request->get("pageIndex") : 0;
        $sortColumn = (!empty($request->get("sortColumn"))) ? $request->get("sortColumn") : "created_at";
        $sortDirection = (!empty($request->get("sortDirection"))) ? $request->get("sortDirection") : "desc";

        //get issued certificates
        $data = Certificate::whereHas('requests', function($query) {

            $query->whereHas('status', function($q) {

                $q->where('key', '=', 'Issued'); 
            });
        });


        $certificateNumber = $request->certificateNumber ?? null;
        $productName = $request->productName ?? null;

        if (filled($certificateNumber)) {
            $data = $data->where("certificateNumber" , $certificateNumber);
            
            if(count($data->get()) == 0)
                return response()->json("Not Found Certificates With this number!", Response::HTTP_NO_CONTENT);
        }  

        if (filled($productName)) {
            
            $data = $data->whereHas('requests', function($query) use ($productName) {

                $query->whereHas('assessments', function($q) use ($productName) {
    
                    $q->where('productName', 'LIKE', '%'.$productName.'%'); 
                });
            });
            
            if(count($data->get()) == 0)
                return response()->json("Not Found Certificates With this product name!", Response::HTTP_NO_CONTENT);
        }
            
        $data = $data->orderBy('id' , $sortDirection);

        $data = $data->get();
        $response = array();
        foreach ($data as $cert) { 

            $applicant = Applicant::findOrFail($cert->requests->applicantId);
            $assessment = Assessment::findOrFail($cert->requests->assessmentId);
            $certType = CertificateType::findOrFail($cert->certificateTypeId);

            $renewCount = 0;
            if ($cert->requests->isRenewal == false) {

                $renewCount = RequestModel::where('originalRequestId', $cert->requests->id)->get()->count();
            }

            $copyCount = certificateCopyRequest::where('certificateId',$cert->id )->get('count');
            $sum = 0;
            if(!empty($copyCount)){
                foreach($copyCount as $item){
                    $sum += $item['count'];
                }
            }
            array_push(
                $response, [
                    "id" => $cert->id,
                    "productName" => $assessment->productName,
                    "companyName"    => $applicant->facilityName,
                    "certificateNumber" =>  $cert->certificateNumber,
                    "certificateTypeNameAr" => $certType->nameAr,
                    "issueDate" => $cert->issueDate,
                    // "percentage" => ,
                    "renewCount" => $renewCount,
                    "copiesCount" => $sum,
                ]
            );
            
        }

        /** paggination */
        $count = count($response);
        $perPage = $pageSize;   
        $page = $pageIndex+1;
        if ($page > count($response) || $page < 1) { $page = 1; }
        if(empty($perPage)){
            $offset = 0;
            $perPage = null;
        }else{
            $offset = ($page * $perPage) - $perPage;
        } 
        $response = array_slice($response,$offset,$perPage);
        /** end Paggination */

        if($sortColumn != 'id'){
            $response = collect($response);
            if(strtolower($sortDirection) == "asc"){

                $response=$response->sortBy($sortColumn)->values();
            }else{
                $response=$response->sortByDesc($sortColumn)->values();
            }
        }
        
        if (!empty($response)) {
            return response()->json([
                "listCount" => $count,
                "data" => $response
            ], 200);
        }
        return [];
    }

}
