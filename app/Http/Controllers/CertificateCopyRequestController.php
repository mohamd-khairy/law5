<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Http\Services\CertificateService;
use App\Model\CertificateType;


use App\Model\User;
use App\Model\Applicant;
use App\Model\Certificate;
use App\Model\CertificateCopyRequest;
use App\Model\Employee;
use App\Model\Role;
use App\Model\Sector;
use App\Model\RequestModel;
use App\Model\Assessment;



class CertificateCopyRequestController extends Controller
{
    use RESTActions;
  
    public function store(Request $request)
    {        
        $this->validate($request, [
            'certificateId' => 'required|numeric',
            'count' => 'required|numeric',
        ]);

        $cert = Certificate::find($request->certificateId); 
        if (empty($cert)) {
            return response()->json("Certificate was not found.", 400);
        }

        //Extract Applicant Id from Header
        $user = $this->getUser($request);
        if (empty($user))
            return response()->json("Missing user token", 400);
            
        $applicantId = $this->getApplicantId($user);
        $req = $cert->requests; // use the Eloquent Relationship.
        if ($req->applicantId != $applicantId)
            return response()->json("Applicant is not the owner of the certificate.", 400);

        if (empty($cert->issueDate)) {

            return response()->json("Certificate is not issued.", 400);
        }
        else {
            if (CertificateService::isExpired($cert)) {

                return response()->json("Certificate is expired.", 400);
            }
        }

        //Create new Certificate Copy Request
        $data = $request->all();
        $certCpyReq = new CertificateCopyRequest($data);
        $certCpyReq->applicantId = $applicantId;
        // Temporarly until integration with Fawry is implemented
        $certCpyReq->isIDAFeesPaid = TRUE;
        $certCpyReq->isFEIFeesPaid = TRUE;
        $certCpyReq->save();

        $response = $certCpyReq->id;
        return response()->json($response, 200); // OK
    }


    
    public function index(Request $request) {
        $rules = [
            'pageSize'               => 'numeric|min:1|max:100',
            'pageIndex'              => 'numeric',
            'applicantId'            => 'nullable|numeric',
        ];
        $this->validate($request, $rules);

        $pageSize = (!empty($request->pageSize)) ? $request->pageSize : 50;
        $pageIndex = (!empty($request->pageIndex)) ? $request->pageIndex : 0;
        $skip = $pageSize * $pageIndex;
        $limit = $pageSize;

        // $data = CertificateCopyRequest::all();

        $countObjectsResponse;
        $user = $this->getUser($request);

        $currentUserRole = Role::findOrFail($user->roleId);

        switch ($currentUserRole->key) {
            case 'Applicant':
                $applicant = Applicant::where("userId", $user->id)->first();
                if (empty($applicant)) {
                    return response()->json("there is no applicant with this id.", 400);
                }
                $data = CertificateCopyRequest::with('certificate', 'applicant')->where("applicantId", $applicant->id)->skip($skip)->take($limit)->get();     
                break;
           
            case 'FEIEmployee':
                // $FEIEmployee = Employee::where("userId", $user->id)->first();
                if (empty($request->applicantId)) {

                    $data = CertificateCopyRequest::with('certificate', 'applicant')->skip($skip)->take($limit)->get();
                } else {

                    $data = CertificateCopyRequest::with('certificate', 'applicant')->where("applicantId", $request->applicantId)->skip($skip)->take($limit)->get();
                }

                break;

            default:
                break;
        }

        $countObjectsResponse = $data->count();
        $response = array();
        foreach ($data as $item) {
            $applicant = Applicant::where('id', $item['applicantId'])->first();
            $sector = Sector::findOrFail($applicant->sectorId);
            $certificate = Certificate::where('id', $item['certificateId'])->first();
            $certType = CertificateType::findOrFail($certificate->certificateTypeId);
            $requestObject = RequestModel::findOrFail($certificate->requestId);
            $assessmentObject = Assessment::findOrFail($requestObject->assessmentId);
            $user = User::where('id', $applicant->userId)->first();

            array_push(
                $response,
                [
                    "id" => $item['id'],

                    "applicant" => [
                        "id" => $applicant->id, 
                        "name" => $user['name'],
                        "companyName" => $applicant->facilityName,
                        "sectorNameAr" => $sector->nameAr,
                        "sectorNameEn" => $sector->nameEn,
                    ],

                    "certificate" => [

                        "id" =>  $certificate->id,
                        "certificateTypeId" => $certType->id,
                        "certificateTypeNameEn" => $certType->name,
                        "certificateTypeNameAr" => $certType->nameAr,
                        "certificateIssueDate" => $certificate->issueDate,
                    ],

                    'productName' => $assessmentObject->productName,
                    "count" => $item['count'],
                    "isChamberMember" => $item['isChamberMember'],
                    "isSubscriptionFeesPaid" => $item['isSubscriptionFeesPaid'],
                    "isIDAFeesPaid" => $item['isIDAFeesPaid'],
                    "isFEIFeesPaid" => $item['isFEIFeesPaid'],
                    "isIssued" =>  $item['isIssued'],
                    "created_at" => $item['created_at'],
                    "updated_at" => $item['updated_at'],
                    "issueDate" => $item['issueDate']

                ]
            );

        }
        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, [
                "listCount" => $countObjectsResponse,
                "data" => $response
            ]);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function show(Request $request, $id)
    {

        $user = $this->getUser($request);
        $currentUserRole = Role::findOrFail($user->roleId);

        switch ($currentUserRole->key) {
            case 'Applicant':
                $applicant = Applicant::where("userId", $user->id)->first();
                if (empty($applicant)) {
                    return response()->json("there is no applicant with this id.", 400);
                }

                $data = CertificateCopyRequest::with('certificate', 'applicant')->where("applicantId", $applicant->id)
                ->where('id', $id)
                ->first();
                break;
           
            case 'FEIEmployee':
                    $data = CertificateCopyRequest::with('certificate', 'applicant')->where('id', $id)->first();

                break;

            default:
                break;
        }

        $applicant = Applicant::where('id', $data['applicantId'])->first();
        $sector = Sector::findOrFail($applicant->sectorId);
        $certificate = Certificate::where('id', $data['certificateId'])->first();
        $certType = CertificateType::findOrFail($certificate->certificateTypeId);
        $requestObject = RequestModel::findOrFail($certificate->requestId);
        $assessmentObject = Assessment::findOrFail($requestObject->assessmentId);
        $user = User::where('id', $applicant->userId)->first();

        
        $response = 
        [
            "id" => $data['id'],

            "applicant" => [
                "id" => $applicant->id, 
                "name" => $user['name'],
                "companyName" => $applicant->facilityName,
                "sectorNameAr" => $sector->nameAr,
                "sectorNameEn" => $sector->nameEn,
            ],

            "certificate" => [

                "id" =>  $certificate->id,
                "certificateTypeId" => $certType->id,
                "certificateTypeNameEn" => $certType->name,
                "certificateTypeNameAr" => $certType->nameAr,
                "certificateIssueDate" => $certificate->issueDate,
            ],

            'productName' => $assessmentObject->productName,
            "count" => $data['count'],
            "isChamberMember" => $data['isChamberMember'],
            "isSubscriptionFeesPaid" => $data['isSubscriptionFeesPaid'],
            "isIDAFeesPaid" => $data['isIDAFeesPaid'],
            "isFEIFeesPaid" => $data['isFEIFeesPaid'],
            "isIssued" =>  $data['isIssued'],
            "created_at" => $data['created_at'],
            "updated_at" => $data['updated_at'],
            "issueDate" => $data['issueDate']

        ];

        
        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, $response);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
       // $this->validate($request, [
        //    'isChamberMember' => 'numeric',
        //    'isSubscriptionFeesPaid' => 'numeric',
       // ]);
        $certCpyReq = CertificateCopyRequest::where("id", $id)->first();
        $certCpyReq->isChamberMember = $request->isChamberMember;
        $certCpyReq->isSubscriptionFeesPaid = $request->isSubscriptionFeesPaid;
        if ($certCpyReq->save())
            return response()->json("Certficate copy request updated successfully", 200);
        else
            return response()->json("Error Updating certficate copy request", 400);

    }

    public function issueCertificateCopies(Request $request, $id) {


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


        $certCpyReq = CertificateCopyRequest::where('id', $id)->first();
        $certCpyReq->isIssued = TRUE;
        $certCpyReq->issuedate = Carbon::now()->toDateTimeString();

        $cert = Certificate::where('id', $certCpyReq->certificateId)->first();

        $response = [
            'certificateNumber' => $cert->certificateNumber,
            'certificateURL' => ""
        ];
        return response()->json($response, 200);

    }

    private function getUser(Request $request) {

        $header = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header)) {
            if ($header == User::where('token', $header)->first()['token']) {
                $user = User::with('roles')->where('token', $header)->first();
            }
        }
        return $user;
    }

    private function getApplicantId(User $user)
    {
        $app = Applicant::where("userId", $user->id)->first();
        return $app->id;
    }

    protected function respond($status, $data = [])
    {
      return response()->json($data, $status);
    }
}
