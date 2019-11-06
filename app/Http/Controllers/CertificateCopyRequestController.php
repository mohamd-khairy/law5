<?php

namespace App\Http\Controllers;

use App\Http\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\CertificateType;
use App\Model\User;
use App\Model\Applicant;
use App\Model\Certificate;
use App\Model\CertificateCopyRequest;
use App\Model\Role;
use App\Model\Sector;
use App\Model\RequestModel;
use App\Model\RequestAction;
use App\Model\Assessment;
use App\Model\Action;

class CertificateCopyRequestController extends Controller
{
    use RESTActions;

    private $certNumberDelimter = '/';

    public function store(Request $request)
    {
        $this->validate($request, [
            'certificateId' => 'required|numeric',
            'count' => 'required|numeric',
        ]);

        $cert = Certificate::findOrFail($request->certificateId);

        //Extract Applicant Id from Header
        $user = userData();

        $applicantId = $this->getApplicantId($user);
        $req = $cert->requests; // use the Eloquent Relationship.
        if ($req->applicantId != $applicantId)
            return response()->json("Applicant is not the owner of the certificate.", 400);

        if (empty($cert->issueDate)) {

            return response()->json("Certificate is not issued.", 400);
        }
        else {
            //check if certificate is not issued.
            if ($cert->requests->status->key != 'Issued') {
                return response()->json("Certificate is not issued.", 400);
            }
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

        $certCpyReq->CopyNumberFrom = CertificateCopyRequest::where('certificateId', $request->certificateId)->max('CopyNumberTo');

        $certCpyReq->CopyNumberFrom ++;
        
        $certCpyReq->CopyNumberTo = $certCpyReq->CopyNumberFrom + $request->count - 1;

        $certCpyReq->save();

        $response = $certCpyReq->id;

        app('App\Http\Controllers\LogController')->Logging_create("certificateCopyRequest", $certCpyReq);
        return response()->json($response, 200); // OK
    }

    public function index(Request $request) {
        $rules = [
            'pageSize'               => 'numeric|min:1|max:100',
            'pageIndex'              => 'numeric',
            'applicantId'            => 'nullable|numeric',
        ];
        $this->validate($request, $rules);

        $DEFAULT_PAGE_SIZE = 50;
        $DEFAULT_PAGE_INDEX = 0;

        $pageSize = (!empty($request->pageSize)) ? $request->pageSize : $DEFAULT_PAGE_SIZE;
        $pageIndex = (!empty($request->pageIndex)) ? $request->pageIndex : $DEFAULT_PAGE_INDEX;
        $skip = $pageSize * $pageIndex;
        $limit = $pageSize;


        $responseCountRecords = CertificateCopyRequest::count();

        // $data = CertificateCopyRequest::all();

        $user = userData();

        $currentUserRole = Role::findOrFail($user->roleId);

        switch ($currentUserRole->key) {
            case 'Applicant':
                $applicant = Applicant::where("userId", $user->id)->first();
                if (empty($applicant)) {
                    return response()->json("there is no applicant with this id.", 400);
                }
                $data = CertificateCopyRequest::with('certificate', 'applicant')->where("applicantId", $applicant->id)->orderBy('created_at','desc')->skip($skip)->take($limit)->get();
                break;

            case 'FEIEmployee':
                // $FEIEmployee = Employee::where("userId", $user->id)->first();
                if (empty($request->applicantId)) {

                    $data = CertificateCopyRequest::with('certificate', 'applicant')->orderBy('created_at','desc')->skip($skip)->take($limit)->get();
                } else {

                    $data = CertificateCopyRequest::with('certificate', 'applicant')->where("applicantId", $request->applicantId)->orderBy('created_at','desc')->skip($skip)->take($limit)->get();
                }

                break;

            default:
                break;
        }


        $responseList = array();

        foreach ($data as $item) {
            $applicant = Applicant::findOrFail($item['applicantId']);
            $sector = Sector::findOrFail($applicant->sectorId);
            $certificate = Certificate::findOrFail($item['certificateId']);
            $certType = CertificateType::findOrFail($certificate->certificateTypeId);
            $requestObject = RequestModel::findOrFail($certificate->requestId);
            $assessmentObject = Assessment::findOrFail($requestObject->assessmentId);
            $user = User::findOrFail($applicant->userId);
            $response =
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

            ];
            array_push(
                $responseList, $response
            );

        }
        if (!empty($responseList)) {
            return $this->respond(Response::HTTP_OK, [
                "listCount" => $responseCountRecords,
                "data" => $responseList
            ]);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function show(Request $request, $id)
    {

        $user = userData();

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
        if (empty($data))
            return $this->respond(Response::HTTP_NOT_FOUND);


        $response = $this->getCertificateCopyRequestResponse($data);


        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, $response);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'isChamberMember' => 'boolean',
            'isSubscriptionFeesPaid' => 'boolean',
        ]);

        $certCpyReq = CertificateCopyRequest::findOrFail($id);
        $old = $certCpyReq->getOriginal();
        
        //data to update
        $data = $request->all();
        $certCpyReq->update($data);
        
        if ($certCpyReq->save())
        {
            $response = $this->getCertificateCopyRequestResponse($certCpyReq);
            app('App\Http\Controllers\LogController')->Logging_update("certificateCopyRequest", $certCpyReq, $old);
            return $this->respond(Response::HTTP_OK, $response);
        }
        else
            return $this->respond(Response::HTTP_NOT_MODIFIED);

    }

    public function issueCertificateCopies(Request $request, $id) {

        $certCpyReq = CertificateCopyRequest::findOrFail($id);
        $old = $certCpyReq->getOriginal();
        
        $actionId = Action::where('key', 'Confirm')->first()->id;
        
        $certificate = Certificate::findOrFail($certCpyReq['certificateId']);
        $requestId = $certificate->requests->id;
        $requestAction = RequestAction::where('requestId', $requestId)
        ->where('actionId', $actionId)->first();

        if(empty($requestAction)) {
            return $this->respond(Response::HTTP_NOT_MODIFIED, "Request is not yet confirmed");            
        }
        //data to update
        $certCpyReq->issueDate = $requestAction->created_at;
        $certCpyReq->isIssued = TRUE;

        $certType = CertificateType::findOrFail($certificate->certificateTypeId);

        $certificateNumberList = $this->getCertificateNumberList($certCpyReq);

        if ($certCpyReq->save())
        {
            $response = [
                "originalCertificate" => [
                    "id" =>  $certificate->id,
                    "typeId" => $certType->id,
                    "typeNameEn" => $certType->name,
                    "typeNameAr" => $certType->nameAr,
                    "expiryDate" => CertificateService::calcExpiryDate($certificate),
                ],

                "copyCertificatesNumbers" => $certificateNumberList,
            ];

            app('App\Http\Controllers\LogController')->Logging_update("certificateCopyRequest", $certCpyReq, $old);
            return $this->respond(Response::HTTP_OK, $response);
        }
        else
            return $this->respond(Response::HTTP_NOT_MODIFIED);
   }

   public function certificateCopyPDF(Request $request)
   {
        $this->validate($request, [
            'copyRequestId' => 'required|numeric',
            'certNumber' => 'required|string',
        ]);
        $certCpyReq = CertificateCopyRequest::findOrFail($request->copyRequestId);
        if ($certCpyReq->isIssued) {
            
            $certificate = Certificate::findOrFail($certCpyReq->certificateId);

            $combinedCertNumber = explode($this->certNumberDelimter, $request->certNumber);// 1/1 or 1/2 ...
            $inputCertNumber = $combinedCertNumber[0]; 
            $inputCopyNumber = $combinedCertNumber[1]; 
            
            if ($inputCertNumber == $certificate->certificateNumber)
            {
                $min = $certCpyReq->CopyNumberFrom;
                $max = $certCpyReq->CopyNumberTo;
                if (($min <= $inputCopyNumber) && ($inputCopyNumber <= $max)) {

                    return CertificateService::generateCertificatePDF($certificate, $request->certNumber);
                }
                else {
                    
                    return $this->respond(Response::HTTP_BAD_REQUEST, "Certificte Copy Number is out of range");
                }
            } else {
                return $this->respond(Response::HTTP_BAD_REQUEST, "Certificte numbers don't match");
            }
            
        } else {
            return $this->respond(Response::HTTP_BAD_REQUEST, "Certificte Copy Request is not issued");
        }
   }


    private function getCertificateCopyRequestResponse(CertificateCopyRequest $certCpyReq)
    {
        $applicant = Applicant::findOrFail($certCpyReq['applicantId']);
        $sector = Sector::findOrFail($applicant->sectorId);
        $user = User::findOrFail($applicant->userId);

        $certificate = Certificate::findOrFail($certCpyReq['certificateId']);
        $certType = CertificateType::findOrFail($certificate->certificateTypeId);
        $requestObject = RequestModel::findOrFail($certificate->requestId);
        $assessmentObject = Assessment::findOrFail($requestObject->assessmentId);

        $certificateNumberList = $this->getCertificateNumberList($certCpyReq);

        $response =
        [
            "id" => $certCpyReq['id'],

            "applicant" => [
                "id" => $applicant->id,
                "name" => $user['name'],
                "companyName" => $applicant->facilityName,
                "sectorNameAr" => $sector->nameAr,
                "sectorNameEn" => $sector->nameEn,
            ],

            "originalCertificate" => [

                "id" =>  $certificate->id,
                "typeId" => $certType->id,
                "typeNameEn" => $certType->name,
                "typeNameAr" => $certType->nameAr,
                "issueDate" => $certificate->issueDate,
                "expiryDate" => CertificateService::calcExpiryDate($certificate),
            ],

            "copyCertificatesNumbers" => $certificateNumberList,

            'productName' => $assessmentObject->productName,
            "count" => $certCpyReq['count'],
            "isChamberMember" => $certCpyReq['isChamberMember'],
            "isSubscriptionFeesPaid" => $certCpyReq['isSubscriptionFeesPaid'],
            "isIDAFeesPaid" => $certCpyReq['isIDAFeesPaid'],
            "isFEIFeesPaid" => $certCpyReq['isFEIFeesPaid'],
            "isIssued" =>  $certCpyReq['isIssued'],
            "created_at" => $certCpyReq['created_at'],
            "updated_at" => $certCpyReq['updated_at'],
            "issueDate" => $certCpyReq['issueDate']

        ];
        return $response;
    }

    private function getCertificateNumberList(CertificateCopyRequest $certCpyReq)
    {
        if ($certCpyReq->isIssued) {

            $certificate = Certificate::findOrFail($certCpyReq['certificateId']);

            $certificateNumberList = array();
            for ($i = $certCpyReq->CopyNumberFrom; $i <= $certCpyReq->CopyNumberTo; $i++) {

                array_push(
                    $certificateNumberList, ($certificate->certificateNumber . $this->certNumberDelimter . $i)
                );
            }
            return $certificateNumberList;
        }
        return NULL;
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
