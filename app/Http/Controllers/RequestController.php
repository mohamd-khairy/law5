<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\RequestModel;
use App\Model\Certificate;
use App\Model\CertificateType;
use App\Model\RequestStatus;
use App\Model\RequestAction;
use App\Model\Action;
use App\Model\User;
use App\Model\Assessment;
use App\Model\Governorate;
use App\Model\Chamber;
use App\Model\Role;
use App\Model\Employee;
use App\Model\Attachment;
use App\Model\Setting;
use App\Model\Sector;
use App\Model\Applicant;
use App\Model\Component;
use App\Model\RepresentativeType;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\returnToCompany;

class RequestController extends Controller
{
    const MODEL = "App\Model\RequestModel";

    use RESTActions;

    public function create_request(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        if(app('App\Http\Controllers\ApplicantController')->isFullRegistered($request) == "false"){
            return response()->json(__("request.completeProfile"), 400);
        }
        //for delete old attachments
        app('App\Http\Controllers\AttachmentsController')->auto_delete();
        $data =  app('App\Http\Controllers\AssessmentController')->GetAssessment($request->assessmentId);
        $data = json_decode($data->content(), true);
        $settings = Setting::first();
        if ($request->exportSupportCertificateRequested == true && $data['assessmentScorePercent'] < $settings->exportFundPercentage) {
            return response()->json("ScorePercent must be more than " . $settings->exportFundPercentage . "% for this certificate", 400);
        } else if ($request->governmentTendersCertificateRequested == true && $data['assessmentScorePercent'] <  $settings->law5CertificatePercentage) {
            return response()->json("ScorePercent must be more than " . $settings->law5CertificatePercentage . "% for Law5 Certificate", 400);
        } else {
            if ($request->input("representativeTypeId") == RepresentativeType::where("key", "Concerned_person")->first()->id) {
                $this->validate($request, RequestModel::$rules_representativeTypeId);
            } else {
                $this->validate($request, RequestModel::$rules);
            }
            $req = new RequestModel();
            $req->statusId = RequestStatus::where("key", "New")->first()->id;
            $req->applicantId = Applicant::where("userId", $user->id)->first()->id;
            $req->assessmentId = $request->input("assessmentId");
            $req->employeeId = null;
            $req->sectorId = $request->input("sectorId");
            $req->sectionId = $request->input("sectionId");
            $req->representativeName = $request->input("representativeName");
            $req->representativeNationalId = $request->input("representativeNationalId");
            $req->representativeTypeId = $request->input("representativeTypeId");
            $req->representativeDelegationNumber = $request->input("representativeDelegationNumber");
            $req->representativeDelegationIssuedBy = $request->input("representativeDelegationIssuedBy");
            $req->chamberMemberNumber = $request->input("chamberMemberNumber");
            $req->representativeMailingAddress = $request->input("representativeMailingAddress");
            $req->representativeFax = $request->input("representativeFax");
            $req->representativeTelephone = $request->input("representativeTelephone");
            $req->representativeMobile = $request->input("representativeMobile");
            $req->representativeEmail = $request->input("representativeEmail");
            $req->industrialRegistry = $request->input("industrialRegistry");
            $req->save();
            app('App\Http\Controllers\LogController')->Logging_create("requests",$req);
            if (isset($req)) {
                if ($request->exportSupportCertificateRequested == true) {
                    $cer = new Certificate();
                    $cer->certificateTypeId = CertificateType::where("name", "=", "Export Fund")->first()->id;
                    $cer->requestId = $req->id;
                    $cer->save();
                    app('App\Http\Controllers\LogController')->Logging_create("certificate",$cer);

                }
                if ($request->governmentTendersCertificateRequested == true) {
                    $cer = new Certificate();
                    $cer->certificateTypeId = CertificateType::where("name", "=", "Law5")->first()->id;
                    $cer->requestId = $req->id;
                    $cer->save();
                    app('App\Http\Controllers\LogController')->Logging_create("certificate",$cer);

                }
                if (!empty($request->requestAttachmentIDs)) {
                    foreach ($request->requestAttachmentIDs as $ids) {
                        $item = Attachment::find($ids);
                        if (!empty($item)) {
                            $item->requestId = $req->id;
                            $item->isRepresentativeProof = false;
                            $old=$item->getOriginal();
                            $item->save();
                            app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);

                        }
                    }
                }
                if (!empty($request->representativeAttachmentIDs)) {
                    foreach ($request->representativeAttachmentIDs as $ids) {
                        $item = Attachment::find($ids);
                        if (!empty($item)) {
                            $item->requestId = $req->id;
                            $item->isRepresentativeProof = true;
                            $old=$item->getOriginal();
                            $item->save();
                            app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);
                        }
                    }
                }
                $action = new RequestAction();
                $action->requestId = $req->id;
                $action->actionId = Action::where("key", "Create")->first()->id;
                $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
                if (!empty($header2)) {
                    $user = User::where('token', $header2)->first();
                }
                $action->byUserId = ($user->id);
                $action->toUserId = null;
                $action->comment = null;
                $action->isAuto = 0;
                $action->save();
            }

            return response()->json($req->id, 200);
        }
    }

    public function CloneRequest(Request $request)
    {
        $reqq = RequestModel::find($request->requestId);
        $ass = Assessment::find($reqq->assessmentId);
        $ass  = $ass->replicate();
        $ass->save();
        $req = $reqq->replicate();
        $req->assessmentId = $ass->id;
        $req->statusId = RequestStatus::where("key", "Draft")->first()->id;
        $req->save();
        $attachments = Attachment::where("requestId", $reqq->id)->get();
        if (!empty($attachments)) {
            foreach ($attachments as $attach) {
                $fileName = time() . rand(1000, 9999) . substr(explode("/", $attach->relativePath)[2], 14);
                $fileName = "public/uploads/" . $fileName;
                $att = $attach->replicate();
                $att->requestId = $req->id;
                $att->relativePath = $fileName;
                $att->save();
                if (file_exists($attach->relativePath)) {
                    File::copy(base_path($attach->relativePath), base_path($fileName));
                }
            }
        }
        app('App\Http\Controllers\LogController')->Logging_create("requests",$req);

        return response()->json($req->id, 200);
    }

    public function update_request(Request $request, $id)
    {
        if ($request->exportSupportCertificateRequested == false && $request->governmentTendersCertificateRequested == false) {
            return response()->json("governmentTendersCertificateRequested or exportSupportCertificateRequested at least one true", 400);
        }
        $data =  app('App\Http\Controllers\AssessmentController')->GetAssessment($request->assessmentId);
        $data = json_decode($data->content(), true);
        $settings = Setting::first();
        if ($request->exportSupportCertificateRequested == true && $data['assessmentScorePercent'] < $settings->exportFundPercentage) {

            return response()->json("ScorePercent must be more than " . $settings->exportFundPercentage . "% for this certificate", 400);
        } else if ($request->governmentTendersCertificateRequested == true && $data['assessmentScorePercent'] <  $settings->law5CertificatePercentage) {

            return response()->json("ScorePercent must be more than " . $settings->law5CertificatePercentage . "% for Law5 Certificate", 400);
        } else {

            if ($request->input("representativeTypeId") == RepresentativeType::where("key", "Concerned_person")->first()->id) {
                $this->validate($request, RequestModel::$rules_representativeTypeId);
            } else {
                $this->validate($request, RequestModel::$rules);
            }
            $req = RequestModel::find($id);
            if (is_null($req)) {
                return response("request Not Found", 400);
            }
            if ($req->statusId ==  RequestStatus::where("key", "Draft")->first()->id) {
                $req->statusId =  RequestStatus::where("key", "New")->first()->id;
            }
            $req->sectionId = $request->input("sectionId");
            $req->representativeName = $request->input("representativeName");
            $req->representativeNationalId = $request->input("representativeNationalId");
            $req->representativeTypeId = $request->input("representativeTypeId");
            $req->representativeDelegationNumber = $request->input("representativeDelegationNumber");
            $req->representativeDelegationIssuedBy = $request->input("representativeDelegationIssuedBy");

            $req->chamberMemberNumber = $request->input("chamberMemberNumber");

            $req->representativeMailingAddress = $request->input("representativeMailingAddress");
            $req->representativeFax = $request->input("representativeFax");
            $req->representativeTelephone = $request->input("representativeTelephone");
            $req->representativeMobile = $request->input("representativeMobile");
            $req->representativeEmail = $request->input("representativeEmail");
            $req->industrialRegistry = $request->input("industrialRegistry");
            $old=$req->getOriginal();
            $req->save();
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);

            if (!empty($req)) {
                if ($request->exportSupportCertificateRequested == true) {
                    $certs = Certificate::where("requestId", $req->id)->where("certificateTypeId", CertificateType::where("name", "=", "Export Fund")->first()->id)->first();
                    if (empty($certs)) {
                        $cer = new Certificate();
                        $cer->certificateTypeId = CertificateType::where("name", "=", "Export Fund")->first()->id;
                        $cer->requestId = $req->id;
                        $cer->save();
                        app('App\Http\Controllers\LogController')->Logging_create("certificate",$cer);

                    }
                } else {
                    $certs = Certificate::where("requestId", $req->id)->where("certificateTypeId", CertificateType::where("name", "=", "Export Fund")->first()->id)->first();
                    if (!empty($certs)) {
                        Certificate::find($certs->id)->forceDelete();
                    }
                }


                if ($request->governmentTendersCertificateRequested == true) {
                    $certs = Certificate::where("requestId", $req->id)->where("certificateTypeId", CertificateType::where("name", "=", "law5")->first()->id)->first();
                    if (empty($certs)) {
                        $cer = new Certificate();
                        $cer->certificateTypeId = CertificateType::where("name", "=", "law5")->first()->id;
                        $cer->requestId = $req->id;
                        $cer->save();
                        app('App\Http\Controllers\LogController')->Logging_create("certificate",$cer);

                    }
                } else {
                    $certs = Certificate::where("requestId", $req->id)->where("certificateTypeId", CertificateType::where("name", "=", "law5")->first()->id)->first();
                    if (!empty($certs)) {
                        Certificate::find($certs->id)->forceDelete();
                    }
                }


                if (!empty($request->requestAttachmentIDs)) {
                    $attachs = Attachment::where("requestId", $req->id)->where("isRepresentativeProof", 0)->get();
                    foreach ($attachs as $ids) {
                        $item = Attachment::find($ids->id);
                        $item->requestId = null;
                        $item->isRepresentativeProof = false;
                        $old=$item->getOriginal();
                        $item->save();
                        app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);

                    }
                    foreach (($request->requestAttachmentIDs) as $ids) {
                        $item = Attachment::find($ids);
                        if (!empty($item)) {
                            $item->requestId = $req->id;
                            $item->isRepresentativeProof = false;
                            $old=$item->getOriginal();
                            $item->save();
                            app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);
                        }
                    }
                } else {
                    Attachment::where("requestId", $req->id)->where("isRepresentativeProof", 0)->forceDelete();
                }

                if (!empty($request->representativeAttachmentIDs)) {
                    $attachs = Attachment::where("requestId", $req->id)->where("isRepresentativeProof", 1)->get();
                    foreach ($attachs as $ids) {
                        $item = Attachment::find($ids->id);
                        $item->requestId = null;
                        $item->isRepresentativeProof = true;
                        $old=$item->getOriginal();
                        $item->save();
                        app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);
                    }
                    foreach ($request->representativeAttachmentIDs as $ids) {
                        $item = Attachment::find($ids);
                        if (!empty($item)) {
                            $item->requestId = $req->id;
                            $item->isRepresentativeProof = true;
                            $old=$item->getOriginal();
                            $item->save();
                            app('App\Http\Controllers\LogController')->Logging_update("attachments",$item,$old);                        }
                    }
                } else {
                    Attachment::where("requestId", $req->id)->where("isRepresentativeProof", 1)->forceDelete();
                }
                return response()->json("request Updated Successfully", 200);
            } else {
                return response()->json("update failed", 400);
            }
        }
    }

    public function get_request(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
            $applicant = Applicant::where("userId", $user->id)->first();
        } else {
            return response()->json("unAuthorize.", 401);
        }
        $rules = [
            'statusId'               => 'numeric|nullable',
            'assignedTo'             => 'numeric|nullable',
            'pageSize'               => 'nullable|numeric|min:1|max:100',
            'pageIndex'              => 'nullable|numeric',
            'sortColumn'             => 'nullable|string',
            'sortDirection'          => 'nullable|string',
            'searchText'             => 'nullable'
        ];
        $this->validate($request, $rules);
        $search = (!empty($request->input("searchText"))) ? $request->input("searchText")  : null;
        $statusId = (!empty($request->input("statusId"))) ? $request->input("statusId") : null;
        $employeeId = (!empty($request->input("assignedTo"))) ? $request->input("assignedTo") : null;
        $pageSize = (!empty($request->input("pageSize"))) ? $request->input("pageSize") : 100;
        $pageIndex = (!empty($request->input("pageIndex"))) ? $request->input("pageIndex") : 0;
        $sortColumn = (!empty($request->input("sortColumn"))) ? $request->input("sortColumn") : "createdAt";
        $sortDirection = (!empty($request->input("sortDirection"))) ? $request->input("sortDirection") : "desc";
        $data = RequestModel::with("certificate", "assessments", "sectors", "users_applicant", "users_employee", "sections", "status");
        $currentUserRole = Role::findOrFail($user->roleId);
        switch ($currentUserRole->key) {
            case 'Applicant':
                if (empty($applicant)) {
                    return response()->json("there is no applicant with this id.", 400);
                }
                $data = $data->where(function ($q) use ($applicant) {
                    $q->where("applicantId", $applicant->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "Draft")->first()->id);
                });
                break;
            case 'IDAEmployee':
                $data = $data->where(function ($q) {
                    $q->where("StatusId", RequestStatus::where("key", "Assigned")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "UnderReview_Closed")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "UnderReview_Opened")->first()->id);
                });
                break;
            case 'IDAManager':
                $data = $data->where(function ($q) {
                    $q->where("StatusId", RequestStatus::where("key", "New")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "Assigned")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "UnderReview_Closed")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "UnderReview_Opened")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "Accepted")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "Declined")->first()->id);
                });
                break;
            case 'ChamberEmployee':
                $empChamberId = Employee::where("userId", $user->id)->first();
                if (empty($empChamberId)) {
                    return response()->json(" there is no employee with this id ", 400);
                }
                $empChamberId=$empChamberId->chamberId;
                $data = $data->where(function ($qq) use ($empChamberId) {
                    $qq->whereHas("assessments",
                        function ($q) use ($empChamberId) {
                            $q->where("chamberId", "=", $empChamberId);
                        }
                    )->where("StatusId", RequestStatus::where("key", "AcceptanceConfirmed")->first()->id)
                        ->Orwhere("StatusId", RequestStatus::where("key", "Validated")->first()->id);
                });
                break;
            case 'FEIEmployee':
                $data = $data->where(function ($q) {
                    $q->where("StatusId", RequestStatus::where("key", "AcceptanceConfirmed")->first()->id)
                    ->Orwhere("StatusId", RequestStatus::where("key", "Validated")->first()->id)
                    ->Orwhere("StatusId", RequestStatus::where("key", "Issued")->first()->id);
                });
                
                break;
            case 'EOSUser':
                $data = $data->where("StatusId", RequestStatus::where("key", "Issued")->first()->id);
                break;
            case 'GAGSUser':
                $data = $data->where("StatusId", RequestStatus::where("key", "Issued")->first()->id);
                break;
            default:
                # code...
                break;
        }
        if (!empty($search)) {
            $data = $data->where(function ($qq) use ($search) {
                $qq->whereHas("assessments", function ($q) use ($search) {
                    $q->where('productName', 'LIKE', '%' . $search . '%');
                })->orWhereHas("users_applicant", function ($q) use ($search) {
                    $q->where("facilityName", 'LIKE', '%' . $search . '%');
                });
            });
            if ($currentUserRole == "GAGSUser" ||  $currentUserRole == "EOSUser") {
                $data = $data->whereHas("certificate", function ($q) use ($search) {
                    $q->where('certificateNumber', 'LIKE', '%' . $search . '%');
                });
            }
            if (count($data->get()) == 0)
                return array();
        }
        if (!empty($statusId)) {
            $data = $data->where("StatusId", $statusId);
            if (count($data->get()) == 0)
                return array();
        }
        if (!empty($employeeId)) {
            $data = $data->where("employeeId", $employeeId);
        }
        if (!empty($sortColumn) || !empty($sortDirection)) {
            switch ($sortColumn) {
                case "productName":
                    $sortBy = "assessments.productName";
                    break;
                case "companyName":
                    $sortBy = "users_applicant.facilityName";
                    break;
                case 'sectorName':
                    $sortBy = "sectors.nameAr";
                    break;
                case 'statusName':
                    $sortBy = "status.key";
                    break;
                case 'createdAt':
                    $sortBy = "created_at";
                    break;
                default:
                    $sortCol = $sortColumn;
                    $data = $data->orderBy($sortCol, $sortDirection);
                    break;
            }
            $count = $data->get()->count();
            $data=$data->paginate($pageSize, ['*'], 'page', $pageIndex + 1);
            if(strtolower($sortDirection) == "asc"){
                $data=$data->sortBy($sortBy)->values();
            }else{
                $data=$data->sortByDesc($sortBy)->values();
            }
        }
        $response = array();
        foreach ($data as $item) {
            $empdata = User::find($item['users_employee']['userId']);
            if (!empty($empdata)) {
                $empName = $empdata->name;
            } else {
                $empName = null;
            }
            array_push(
                $response,
                [
                    "id" => $item['id'],
                    "productName" => $item['assessments']['productName'],
                    "statusId" => $item['statusId'],
                    "statusNameEn" => $item['status']['nameEn'],
                    "statusNameAr" => $item['status']['nameAr'],
                    "statusKey" => $item['status']['key'],
                    "companyId" => $item['applicantId'],
                    "companyName" =>  $item['users_applicant']['facilityName'],
                    "assessmentId" => $item['assessmentId'],
                    "employeeId" => $item['employeeId'],
                    "employeeName" => $empName,
                    "sectorId" => $item['sectorId'],
                    "sectorNameEn" => $item['sectors']['nameEn'],
                    "sectorNameAr" => $item['sectors']['nameAr'],
                    "sectionId" => $item['sectionId'],
                    "sectionNameEn" => $item['sections']['nameEn'],
                    "sectionNameAr" => $item['sections']['nameAr'],
                    "telephone" => $item['representativeTelephone'],
                    "mobile" => $item['representativeMobile'],
                    "fax" => $item['representativeFax'],
                    "email" => $item['representativeEmail'],
                    "openedByEmployeeId" => $item['openedByEmployeeId'],
                    "chamberMemberNumber" => $item['chamberMemberNumber'],
                    "representativeName" => $item['representativeName'],
                    "representativeNationalNumber" => $item['representativeNationalId'],
                    "representativeTypeId" => $item['representativeTypeId'],
                    "representativeTypeName" => ($type = RepresentativeType::where("id", $item['representativeTypeId'])->first()) ? $type->key : null,
                    "representativeDelegationNumber" => $item['representativeDelegationNumber'],
                    "representativeDelegationIssuedBy" => $item['representativeDelegationIssuedBy'],
                    "MailingAddress" => $item['representativeMailingAddress'],
                    "industrialRegistry" => $item['industrialRegistry'],
                    "isOriginalsReceived" => $item['isOriginalsReceived'],
                    "isChamberMember" => $item['isChamberMember'],
                    "isIDAFeesPaid" => $item['isIDAFeesPaid'],
                    "isFEIFeesPaid" => $item['isFEIFeesPaid'],
                    "isSubscriptionFeesPaid" => $item['isSubscriptionFeesPaid'],
                    "createdAt" => $item['created_at'],
                    "updatedAt" => $item['updated_at']
                ]
            );
        }
        if (!empty($response)) {
            return [
                "listCount" => $count,
                "data" => $response
            ];
        }
    }

    public function get_request_by_id($id)
    {
        $item = RequestModel::with("assessments", "sectors", "users_applicant", "users_employee", "sections", "status")
            ->where("id", $id)->first();
        // return $item;
        if (!empty($item['assessments']['chamberId'])) {
            $c = Chamber::find($item['assessments']['chamberId']);
        }

        $certids = Certificate::where("requestId", $id)->get();
        $extreme = false;
        $law5 = false;
        $ids = [];
        foreach ($certids as $certid) {
            $ids[] = $certid->certificateTypeId;
        }
        if (in_array(1, $ids)) {
            $extreme = true;
        }
        if (in_array(2, $ids)) {
            $law5 = true;
        }
        // return $item;
        $appName = Applicant::find($item['applicantId']);
        if (!empty($appName)) {
            $appName = $appName->facilityName;
        } else {
            return response()->json("not found applicant with this id", 400);
        }
        if (!empty($item)) {
            $empdata = User::find($item['users_employee']['userId']);
            if (!empty($empdata)) {
                $empName = $empdata->name;
            } else {
                $empName = null;
            }
            return response()->json([
                "id" => $item['id'],
                "productName" => $item['assessments']['productName'],
                "companyId" => $item['applicantId'],
                "companyName" => $appName,
                "statusId" => $item['statusId'],
                "statusNameEn" => $item['status']['nameEn'],
                "statusNameAr" => $item['status']['nameAr'],
                "statusKey" => $item['status']['key'],
                "assessmentId" => $item['assessmentId'],
                "employeeId" => $item['employeeId'],
                "employeeName" => $empName,
                "openedByEmployeeId" => $item['openedByEmployeeId'],
                "chamberMemberNumber" => $item['chamberMemberNumber'],
                "sectorId" => $item['sectorId'],
                "sectorNameEn" => $item['sectors']['nameEn'],
                "sectorNameAr" => $item['sectors']['nameAr'],
                "sectionId" => $item['sectionId'],
                "sectionNameEn" => $item['sections']['nameEn'],
                "sectionNameAr" => $item['sections']['nameAr'],
                "chamberId" => $item['assessments']['chamberId'],
                "chamberNameAr" => ($c) ? $c->nameAr : " ",
                "chamberNameEn" => ($c) ? $c->nameEn : " ",
                "telephone" => $item['representativeTelephone'],
                "representativeMobile" => $item['representativeMobile'],
                "representativeName" => $item['representativeName'],
                "representativeNationalId" => $item['representativeNationalId'],
                "representativeTypeId" => $item['representativeTypeId'],
                "representativeTypeName" => ($type = RepresentativeType::where("id", $item['representativeTypeId'])->first()) ? $type->key : null,
                "representativeDelegationNumber" => $item['representativeDelegationNumber'],
                "representativeDelegationIssuedBy" => $item['representativeDelegationIssuedBy'],
                "representativeMailingAddress" => $item['representativeMailingAddress'],
                "representativeTelephone" => $item['representativeTelephone'],
                "representativeFax" => $item['representativeFax'],
                "representativeEmail" => $item['representativeEmail'],
                "industrialRegistry" => $item['industrialRegistry'],
                "exportSupportCertificateRequested" => $extreme,
                "governmentTendersCertificateRequested" => $law5,
                "mailingAddress" => $item['representativeMailingAddress'],
                "isOriginalsReceived" => ($item['isOriginalsReceived']) ? true : false,
                "isChamberMember" => ($item['isChamberMember']) ? true : false,
                "isSubscriptionFeesPaid" => ($item['isSubscriptionFeesPaid']) ? true : false,
                "isIDAFeesPaid" => ($item['isIDAFeesPaid']) ? true : false,
                // "isFEIFeesPaid" => ($item['isFEIFeesPaid']) ? true : false,
                "createdAt" => $item['created_at'],
                "updatedAt" => $item['updated_at']

            ]);
        }
    }

    public function get_request_status(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        if (!empty($user) ) {
            $currentUserRole = Role::findOrFail($user->roleId);
            switch ($currentUserRole->key) {
                case 'Applicant':
                $data = RequestStatus::get();
                    break;
                case 'IDAEmployee':
                $data = RequestStatus::where("key","Assigned")->Orwhere("key","UnderReview_Closed")->Orwhere("key","UnderReview_Opened")->get();
                //Assigned ,UnderReview_Closed,UnderReview_Opened
                    break;
                case 'IDAManager':
                $data = RequestStatus::where("key","New")->Orwhere("key","Assigned")->Orwhere("key","Accepted")->Orwhere("key","Declined")->Orwhere("key","UnderReview_Closed")
                ->Orwhere("key","UnderReview_Opened")->get();
                //Assigned ,UnderReview_Closed,UnderReview_Opened,Accepted,Declined
                    break;
                case 'ChamberEmployee':
                $data = RequestStatus::where("key","AcceptanceConfirmed")->Orwhere("key","Validated")->get();
                //AcceptanceConfirmed ,Validated
                    break;
                case 'FEIEmployee':
                $data = RequestStatus::where("key","AcceptanceConfirmed")->Orwhere("key","Issued")->get();
                //AcceptanceConfirmed ,Issued
                    break;
                case 'EOSUser':
                $data = RequestStatus::where("key","Issued")->get();
                //Issued
                break;
                case 'GAGSUser':
                $data = RequestStatus::where("key","Issued")->get();
                //Issued
                break;
                default:
                    $data = RequestStatus::get();
                    break;
            }
            return $this->respond(Response::HTTP_OK, $data);
        }
        return $this->respond(Response::HTTP_NOT_FOUND);
    }

    public function assign(Request $request)
    {
        $rules = [
            'requestId'               => 'integer|required',
            'employeeId'             => 'integer|required',
            'comment'               => 'nullable|string'
        ];
        $this->validate($request, $rules);
        $req = RequestModel::findOrFail($request->requestId);
        $req->employeeId = $request->employeeId;
        $status_id = $req->statusId = RequestStatus::where("key", "Assigned")->first()->id;
        $old=$req->getOriginal();
        if ($req->save()) {
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
            if (($status_id == RequestStatus::where("key", "Assigned")->first()->id) || ($status_id == RequestStatus::where("key", "New")->first()->id)) {
                $action = new RequestAction();
                $action->requestId = $req->id;
                $action->actionId = Action::where("key", "Assign")->first()->id;
                $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
                if (!empty($header2)) {
                    $user = User::where('token', $header2)->first();
                } else {
                    return response()->json("unAuthorize.", 401);
                }
                $action->byUserId = $user->id;
                $action->toUserId = $req->employeeId;
                $action->comment = $request->comment;
                $action->isAuto = 0;
                $action->save();
            }
        }
        return $this->respond(Response::HTTP_OK, $action);
    }

    public function StartReview(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        $rules = [
            'requestId'   => 'integer|required',
        ];
        $this->validate($request, $rules);
        $req = RequestModel::findOrFail($request->requestId);
        if (($req->statusId == RequestStatus::where("key", "Assigned")->first()->id) || ($req->statusId == RequestStatus::where("key", "UnderReview_Closed")->first()->id)
        ) {
            $req->statusId = RequestStatus::where("key", "UnderReview_Opened")->first()->id;
            $data = Employee::where("userId", $user->id)->first();

            if (!empty($data)) {
                $emp_id = $data->id;
            } else {
                $emp_id = null;
            }
            $req->openedByEmployeeId = $emp_id;
            $old=$req->getOriginal();
            if ($req->save()) {
                app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);

                $action = new RequestAction();
                $action->requestId = $req->id;
                $action->actionId = Action::where("key", "Start review")->first()->id;
                $action->byUserId = ($user->id) ? $user->id : null;
                $action->toUserId = null; //$req->employeeId;
                $action->comment = null;
                $action->isAuto = 0;
                $action->save();
            }
        } else {
            return response("this case for Assigned or UnderReview_Closed status only", 400);
        }
        return $this->respond(Response::HTTP_OK, $action);
    }

    public function CloseReview(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        $rules = [
            'requestId'   => 'integer|required',
        ];
        $this->validate($request, $rules);
        $req = RequestModel::findOrFail($request->requestId);
        $data = Employee::where("userId", $user->id)->first();
        if (!empty($data)) {
            $emp_id = $data->id;
        } else {
            $emp_id = null;
        }
        if (($req->statusId == RequestStatus::where("key", "UnderReview_Opened")->first()->id)) {
            if (($req->openedByEmployeeId == $emp_id)) {
                $req->statusId = RequestStatus::where("key", "UnderReview_Closed")->first()->id;
                $req->openedByEmployeeId = null;
                $old=$req->getOriginal();
                if ($req->save()) {
                    app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
                    $action = new RequestAction();
                    $action->requestId = $req->id;
                    $action->actionId = Action::where("key", "End Review")->first()->id;
                    $action->byUserId = ($user->id) ? $user->id : null;
                    $action->toUserId = null;//$req->employeeId;
                    $action->comment = null;
                    $action->isAuto = 0;
                    $action->save();
                }
            } else {
                return response()->json("this case not permission for this employee ");
            }
        } else {
            return response()->json("this case for UnderReview_Opened status only ");
        }
        return $this->respond(Response::HTTP_OK, $action);
    }

    public function ConfirmRequest(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        $rules = [
            'requestId'   => 'integer|required',
        ];
        $this->validate($request, $rules);
        $req = RequestModel::findOrFail($request->requestId);
        $action = new RequestAction();
        $action->requestId = $req->id;
        switch ($req->statusId) {
            case ($req->statusId == RequestStatus::where("key", "Accepted")->first()->id):
                $req->statusId = RequestStatus::where("key", "AcceptanceConfirmed")->first()->id;
                $req->isIDAFeesPaid = 1; /////////////
                $req->isFEIFeesPaid = 1; ///////////
                $action->actionId  = Action::where("key", "Accept")->first()->id;
                $cert = Certificate::where("requestId", $req->id)->get();
                foreach ($cert as $c) {
                    $certificate = Certificate::find($c->id);
                    $certificate->managerApproveDate = Carbon::now()->toDateTimeString();
                    $certificate->save();
                }
                break;
            case ($req->statusId == RequestStatus::where("key", "Declined")->first()->id):
                $req->statusId = RequestStatus::where("key", "DeclineConfirmed")->first()->id;
                $action->actionId  = Action::where("key", "Decline")->first()->id;
                break;
            default:
                return response("this case for Accepted or Decline actions only", 400);
                break;
        }
        $req->save();
        $action->byUserId = ($user->id);
        $action->toUserId = null;//$req->employeeId;
        $action->comment = $request->comment;
        $action->isAuto = 0;
        $action->save();
        return $this->respond(Response::HTTP_OK, $action);
    }

    public function SaveReview(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        } else {
            return response('Unauthorized.', 401);
        }
        $rules = [
            'requestId'               => 'integer|required',
            'isOriginalsReceived'      => 'boolean|required',
            'isAutoSave'             => 'boolean|required',
            'closeAfterSave'         => 'boolean|required',
            'comment'               => 'nullable|string'
        ];
        $this->validate($request, $rules);
        $req = RequestModel::findOrFail($request->requestId);
        if ($req->statusId != RequestStatus::where("key", "UnderReview_Opened")->first()->id) {
            return response()->json('this case for UnderReview_Opened status only.', 400);
        }
        $data = Employee::where("userId", $user->id)->first();
        if (!empty($data)) {
            $emp_id = $data->id;
        } else {
            $emp_id = null;
        }
        if (($req->openedByEmployeeId != $emp_id)) {
            return response()->json('not permissopn for this employee.', 400);
        }
        $req->isOriginalsReceived = $request->isOriginalsReceived;
        $close = 0;
        if ($request->closeAfterSave == true) {
            $close = 1;
            $req->statusId = RequestStatus::where("key", "UnderReview_Closed")->first()->id;
        }
        $old=$req->getOriginal();
        $req->save();
        app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);

        for ($i = 0; $i <= $close; $i++) {
            $action = new RequestAction();
            $action->requestId = $req->id;
            if ($i == 0) {
                $action->actionId  = Action::where("key", "Save")->first()->id;
                $action->toUserId = null;
            } else {
                $action->actionId  = Action::where("key", "End Review")->first()->id;
                $action->toUserId = $req->employeeId;
            }
            $action->byUserId = ($user->id) ? $user->id : null;
            $action->comment = $request->comment;
            $action->isAuto = $request->isAutoSave;
            $action->save();
        }

        return $this->respond(Response::HTTP_OK, "true");
    }

    public function RespondToRequest(Request $request)
    {
        $rules = [
            'requestId' => 'integer|required',
            'actionId'  => 'integer|required',
            'comment'   => 'string|required'
        ];
        $this->validate($request, $rules);
        if (($request->actionId == Action::where("key", "Accept")->first()->id) ||
         ($request->actionId == Action::where("key", "Return")->first()->id) ||
          ($request->actionId == Action::where("key", "Decline")->first()->id)
        ) {
            $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
            if (!empty($header2)) {
                $user = User::where('token', $header2)->first();
            } else {
                return "UnAuthorize !!";
            }
            $req = RequestModel::findOrFail($request->requestId);
            $data = Employee::where("userId", $user->id)->first();
            if (!empty($data)) {
                $emp_id = $data->id;
            } else {
                $emp_id = null;
            }
            if (isset($user) && ($user->roles->key == "IDAEmployee") &&
                 ($req->statusId == RequestStatus::where("key", "UnderReview_Opened")->first()->id)) 
            {
                if (($req->openedByEmployeeId == $emp_id)) {
                        switch ($request->actionId) {
                            case ($request->actionId == Action::where("key", "Accept")->first()->id):
                                if (!$req->isOriginalsReceived) {
                                    return response()->json(__('request.isOriginal'),400);
                                }
                                $action_id = Action::where("key", "Accept")->first()->id;
                                $to_id = null;// $req->employeeId;
                                $req->statusId = RequestStatus::where("key", "Accepted")->first()->id;
                                break;
                            case ($request->actionId == Action::where("key", "Return")->first()->id):
                                $action_id = Action::where("key", "Return")->first()->id;
                                $to_id = $req->applicantId;
                                $req->statusId = RequestStatus::where("key", "Returned")->first()->id;
                                $this->mail($req->applicantId ,$user);
                                break;
                            case ($request->actionId == Action::where("key", "Decline")->first()->id):
                                $action_id = Action::where("key", "Decline")->first()->id;
                                $to_id = null;//$req->employeeId;
                                $req->statusId = RequestStatus::where("key", "Declined")->first()->id;
                                break;
                            default:
                            return response()->json(__('request.ActionPermission'),400);
                            break;
                        }
                        $old=$req->getOriginal();
                        if ($req->save()) {
                            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
                            $action = new RequestAction();
                            $action->requestId = $req->id;
                            $action->actionId = $action_id;
                            $action->byUserId = ($user->id);
                            $action->toUserId = $to_id;
                            $action->comment = $request->comment;
                            $action->isAuto = 0;
                            $action->save();
                        }
                } else {
                    return response()->json(__('request.employeePermission'),400);
                }
            } else {
                return response()->json(__('request.UnderReview'),400);
            }
        } else {
            return response()->json(__('request.ActionPermission'),400);
        }
        return response()->json("true",200);
    }

    public function get_request_attachments_by_request_id($request_id)
    {
        $attachments_request = Attachment::where("requestId", $request_id)->where("isRepresentativeProof", false)->get();
        $attachments_representative = Attachment::where("requestId", $request_id)->where("isRepresentativeProof", true)->get();
        $response_attachments_request = array();
        if (!empty($attachments_request)) {
            foreach ($attachments_request as  $item) {
                array_push($response_attachments_request, [
                    "id" => $item['id'],
                    "url" => $item['relativePath'],
                    "fileName" => $item['originalName'],
                    "createdAt" => $item['created_at'],
                    "updatedAt" => $item['updated_at']
                ]);
            }
        }
        $response_attachments_representative = array();
        if (!empty($attachments_representative)) {
            foreach ($attachments_representative as  $item) {
                array_push($response_attachments_representative, [
                    "id" => $item['id'],
                    "url" =>$item['relativePath'],
                    "fileName" => $item['originalName'],
                    "createdAt" => $item['created_at'],
                    "updatedAt" => $item['updated_at']
                ]);
            }
        }

        $response = [
            "requestId" => (int)$request_id,
            "requestsDocs" => $response_attachments_request,
            "representativeProofs" => $response_attachments_representative
        ];
        return $this->respond(Response::HTTP_OK, $response);
    }

    public function get_request_attachments_by_applicant_id(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        } else {
            return response("unathorize", 401);
        }
        $applicantId = Applicant::where("userId", $user->id)->first();
        if (empty($applicantId)) {
            return response()->json("there is no applicant with this id ", 400);
        }
        $attachments_request = Attachment::where("applicantId", $applicantId->id)->get();
        $response_attachments_request = array();
        if (!empty($attachments_request)) {
            foreach ($attachments_request as $item) {
                array_push($response_attachments_request, [
                    "id" => $item['id'],
                    "url" => $item['relativePath'],
                    "fileName" => $item['originalName'],
                    "createdAt" => $item['created_at'],
                    "updatedAt" => $item['updated_at']
                ]);
            }
        }

        $response = [
            "applicantId" => $applicantId->id,
            "attachments" => $response_attachments_request,
        ];
        return $this->respond(Response::HTTP_OK, $response);
    }

    public function Return_To_Employee(Request $request)
    {
        $this->validate($request, [
            'requestId' => 'required',
            'comment' => 'nullable',
        ]);
        $req = RequestModel::findOrFail($request->requestId);
        $action = new RequestAction();
        if (
            $req->statusId == RequestStatus::where("key", "Accepted")->first()->id ||
            $req->statusId == RequestStatus::where("key", "Declined")->first()->id
        ) {

            $req->statusId = RequestStatus::where("key", "UnderReview_Opened")->first()->id;
        }
        $old=$req->getOriginal();
        if ($req->save()) {
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
            $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
            if (!empty($header2)) {
                $user = User::where('token', $header2)->first();
            } else {
                return response("unathorize", 401);
            }
            $action->requestId = $req->id;
            $action->actionId = Action::where("key", "ReturnToEmployee")->first()->id;
            $action->byUserId = $user->id;
            $action->toUserId = $req->employeeId;
            $action->comment = $request->comment;
            $action->isAuto = 0;
            $action->save();
        }
        return response("true", 200);
    }

    public function VerifyMembership(Request $request)
    {
        $this->validate($request, [
            'requestId' => 'required',
            'isChamberMember' => 'required|boolean',
            'isSubscriptionFeesPaid' => 'boolean',
            'comment' => 'nullable',
        ]);
        $req = RequestModel::findOrFail($request->requestId);
        if ($request->isChamberMember == true) {// && $request->isSubscriptionFeesPaid == true
            $req->statusId = RequestStatus::where("key", "Validated")->first()->id;
        }
        $req->isChamberMember = ($request->isChamberMember) ? $request->isChamberMember : 0;
        $req->isSubscriptionFeesPaid = ($request->isSubscriptionFeesPaid) ? $request->isSubscriptionFeesPaid : 0;
        $action = new RequestAction();
        $old=$req->getOriginal();
        if ($req->save()) {
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);

            $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
            if (!empty($header2)) {
                $user = User::where('token', $header2)->first();
            } else {
                return response("unathorize", 401);
            }
            $action->requestId = $req->id;
            $action->actionId = Action::where("key", "Validate")->first()->id;
            $action->byUserId = $user->id;
            $action->toUserId = null;//$req->employeeId;
            $action->comment = $request->comment;
            $action->isAuto = 0;
            $action->save();
        }
        return response()->json("true", 200);
    }

    public function ChangeChamber(Request $request)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        } else {
            return response("unathorize", 401);
        }
        $this->validate($request, [
            'requestId' => 'required',
            'chamberId' => 'required',
        ]);
        $req = RequestModel::findOrFail($request->requestId);
        $assessment = Assessment::findOrFail($req->assessmentId);
        $currentChamber = Chamber::findOrFail($assessment->chamberId);
        $newChamber = Chamber::findOrFail($request->chamberId);

        if ($newChamber->assessmentMethod == $currentChamber->assessmentMethod) {
            $assessment->chamberId = $request->chamberId;
            $old=$assessment->getOriginal();
            if ($assessment->save()) {
                app('App\Http\Controllers\LogController')->Logging_update("assessments",$assessment,$old);
                $action = new RequestAction();
                $action->requestId = $req->id;
                $action->actionId = Action::where("key", "ChangeChamber")->first()->id;
                $action->byUserId = $user->id;
                $action->toUserId = null;
                $action->comment = null;
                $action->isAuto = 0;
                $action->save();
            }
            return response()->json(__("request.changeChamberDone"), 200);
        } else {
            return response()->json(__("request.changeChamberFail"), 400);
        }
    }

    public function resend_request(Request $request)
    {

        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        $rules = [
            'requestId'               => 'integer|required',
            'comment'               => 'required|string'
        ];
        $this->validate($request, $rules);
        $id = $request->requestId;
        $req = RequestModel::find($id);
        $applicant = Applicant::where("userId", $user->id)->first();
        if (is_null($req) || is_null($applicant)) {
            return response("Not Found request", 400);
        }
        if ($req->applicantId != $applicant->id) {
            return response()->json(__('request.notBelongToYou'), 401);
        }
        if ($req->statusId != RequestStatus::where("key", "Returned")->first()->id) {
            return response()->json("The status of This Request not returned .. !", 400);
        }

        $data = app('App\Http\Controllers\AssessmentController')->GetAssessment($req->assessmentId);
        $data = json_decode($data->content(), true);
        $settings = Setting::first();
        $req->statusId = RequestStatus::where("key", "UnderReview_Opened")->first()->id;
        $old=$req->getOriginal();
        if ($req->save()) {
            app('App\Http\Controllers\LogController')->Logging_update("requests",$req,$old);
            $action = new RequestAction();
            $action->requestId = $req->id;
            $action->actionId = Action::where("key", "Resend")->first()->id;
            $action->byUserId = $user->id;
            $actionIdForReturnedCase = Action::where("key", "Return")->first()->id;
            $user_that_return_request=RequestAction::where("actionId", $actionIdForReturnedCase)
            ->where("requestId", $req->id)->first();
            $action->toUserId =$user_that_return_request->byUserId;
            $action->comment = $request->comment;
            $action->isAuto = 0;
            $action->save();
        }
        return response($req->id, 200);
        
    }

    public function getLatestReturnAction(Request $request, $requestId)
    {
        $header2 = trim(explode(' ', $request->header("Authorization"))[1]);
        if (!empty($header2)) {
            $user = User::where('token', $header2)->first();
        }
        $id = $requestId;
        $req = RequestModel::find($id);
        $applicant = Applicant::where("userId", $user->id)->first();
        if (!intval($id) || empty($req) || empty($applicant)) {
            return response()->json("This id not valid", 400);
        }
        if (!empty($req)) {
            if ($req->applicantId != $applicant->id) {
                return response()->json("This Request Not Belong To You .. !", 401);
            }
        } else {
            return response()->json("there is no request with this id .. !", 400);
        }

        $actionIdForReturnedCase = Action::where("key", "Return")->first()->id;
        $item = $requestAction = RequestAction::with("actions") //, "user_by", "user_to")
            ->where("actionId", $actionIdForReturnedCase)
            ->where("requestId", $req->id)->latest()->first();
        if (empty($item)) {
            return $this->respond(Response::HTTP_OK, ["comment" => null]);
        } else {
            return $this->respond(Response::HTTP_OK, ["comment" => $item['comment']]);
        }
        
    }

    public function GerRequestsBy_status_id(Request $request){
        $statusId=(!empty($request->get("statusId")))? $request->get("statusId") : null;
        if($statusId == null){
            $count=RequestModel::get()->count();
            $ManufacturedByOthersCount=RequestModel::with("assessments")->whereHas("assessments", function ($q) {
                $q->where('manufactoringByOthers', true);
            })->count();
        }else{
            $count=RequestModel::where("statusId",$statusId)->get()->count();
            $ManufacturedByOthersCount = RequestModel::with("assessments")->where(function ($qq) use ($statusId) {
                $qq->whereHas("assessments",function ($q) {
                    $q->where('manufactoringByOthers', true);
                    }
                )->where("StatusId", $statusId);
            })->count();
        }

        return response()->json([
            "manufacturedByOthersCount" => $ManufacturedByOthersCount,
            "totalCount" => $count],200);
    }

    public function GetApplicant_with_issue_certificate(Request $request)
    {
        $this->validate($request, [
            'certificateTypeId' => 'nullable|numeric',
            'sectorId'           => 'nullable|numeric',
            'chamberId'         => 'nullable|numeric',
            'governorateId'      => 'nullable|numeric'
        ]);
        $certificateTypeId = (!empty($request->get("certificateTypeId"))) ? $request->get("certificateTypeId") : null;
        $sectorId = (!empty($request->get("sectorId"))) ? $request->get("sectorId") : null;
        $chamberId = (!empty($request->get("chamberId"))) ? $request->get("chamberId") : null;
        $governorateId = (!empty($request->get("governorateId"))) ? $request->get("governorateId") : null;

        $data = RequestModel::with("certificate", "assessments", "users_applicant")
        ->where("statusId",RequestStatus::where("Key","Issued")->first()->id);
        if ($sectorId != null) {
            $data = $data->where('sectorId', $sectorId);
        }
        if ($certificateTypeId != null) {
            $data = $data->whereHas("certificate", function ($q) use ($certificateTypeId){
                $q->where('certificateTypeId', $certificateTypeId);
            });
        }
        if ($chamberId != null) {
            $data = $data->whereHas("assessments", function ($q) use($chamberId) {
                $q->where('chamberId',  $chamberId);
            });
        }
        if ($governorateId != null) {
            $data = $data->whereHas("users_applicant", function ($q) use($governorateId) {
                $q->where('governorateId',   $governorateId);
            });
        }
        $response = array();
        foreach ($data->get() as $item) {
            $obj=[
                "companyId" => $item['applicantId'],
                "companyName" =>  $item['users_applicant']['facilityName'],                    
            ]; 
            if (!in_array($obj, $response)){
                array_push($response,$obj);
            }
        }
       return response()->json($response ,200);
    }
    // reports
    public function CertificatedProducts_with_issue_certificate(Request $request)
    {
        $this->validate($request, [
            'applicantId'        => 'required|numeric',
            'certificateTypeId'  => 'nullable|numeric',
            'sectorId'           => 'nullable|numeric',
            'chamberId'          => 'nullable|numeric',
            'governorateId'      => 'nullable|numeric',
            'productName'        => 'nullable|string',
            'companyName'        => 'nullable|string'
        ]);
        $applicantId = $request->get("applicantId");
        $certificateTypeId = (!empty($request->get("certificateTypeId"))) ? $request->get("certificateTypeId") : null;
        $sectorId = (!empty($request->get("sectorId"))) ? $request->get("sectorId") : null;
        $chamberId = (!empty($request->get("chamberId"))) ? $request->get("chamberId") : null;
        $governorateId = (!empty($request->get("governorateId"))) ? $request->get("governorateId") : null;
        $productName = (!empty($request->get("productName"))) ? $request->get("productName") :  null;
        $companyName = (!empty($request->get("companyName"))) ? $request->get("companyName") : null;

        $data = RequestModel::with("certificate", "assessments", "users_applicant")
        ->where("applicantId",$applicantId)
        ->where("statusId",RequestStatus::where("Key","Issued")->first()->id);
        if ($sectorId != null) {
            $data = $data->where('sectorId', $sectorId);
        }
        if ($certificateTypeId != null) {
            $data = $data->whereHas("certificate", function ($q) use ($certificateTypeId){
                $q->where('certificateTypeId', $certificateTypeId);
            });
        }
        if ($chamberId != null) {
            $data = $data->whereHas("assessments", function ($q) use($chamberId) {
                $q->where('chamberId',  $chamberId);
            });
        }
        if ($governorateId != null) {
            $data = $data->whereHas("users_applicant", function ($q) use($governorateId) {
                $q->where('governorateId',   $governorateId);
            });
        }
        if ($productName != null) {
            $data = $data->whereHas("assessments", function ($q) use($productName) {
                $q->where('productName',  $productName);
            });
        }
        if ($companyName != null) {
            $data = $data->whereHas("users_applicant", function ($q) use($companyName) {
                $q->where('facilityName',   $companyName);
            });
        }

        $response = array();
        $export = false;
        $law5 = false;
        foreach ($data->get() as $item) {
            if(!empty($item['certificate'])){
                foreach($item['certificate'] as $cert){
                    if($cert['certificateTypeId'] == 1){
                        $export=true;
                    }
                    if($cert['certificateTypeId'] == 2){
                        $law5=true;
                    }
                }
            }
            
            $obj=[
                "assessmentId" => $item['assessmentId'],
                "productName"  =>  $item['assessments']['productName'],                    
                "requestId"    =>  $item['id'],                    
                "exportFundCertificate" =>  $export,
                "law5Certificate" =>  $law5                                   
            ]; 
            if (!in_array($obj, $response)){
                array_push($response,$obj);
            }
        }
       return response()->json($response ,200);
    }

    public function CertificatesCount_by_sector_cert_gover(Request $request)
    {
        $this->validate($request, [
            'groupBy'        => 'required|string|in:sector,certificateType,governorate',
            'fromDate'       => 'nullable|date',
            'toDate'         => 'nullable|date'
        ]);
        $data=DB::table('requests')->where("statusId",RequestStatus::where("Key","Issued")->first()->id);

        if(!empty($request->fromDate) && !empty($request->toDate)){
            $fromDate =date("Y-m-d",strtotime($request->fromDate));
            $toDate =date("Y-m-d",strtotime($request->toDate));
            $data=$data->whereBetween('requests.created_at',[$fromDate , $toDate]);
        }elseif(!empty($request->fromDate) && empty($request->toDate)){
            $fromDate = date("Y-m-d",strtotime($request->fromDate));
            $data=$data->whereDate('requests.created_at',">=", $fromDate);
        }elseif(!empty($request->toDate) && empty($request->fromDate)){
            $toDate = date("Y-m-d",strtotime($request->toDate));
            $data=$data->whereDate('requests.created_at',"<=", $toDate);
        }

        switch ($request->groupBy) {
            case 'sector':
                $data = $data->leftJoin('sectors as s', 's.id', '=', 'requests.sectorId')
                ->select("s.id as secId", DB::raw('count(*) as total'))
                ->groupBy('secId')
                ->get();          
                break;
            case 'certificateType':
                $data = $data->leftJoin('certificates as c', 'requests.id', '=', 'c.requestId')
                ->select("c.certificateTypeId", DB::raw('count(*) as total'))
                ->groupBy('c.certificateTypeId')
                ->get();          
                break;
            case 'governorate':
                $data = $data->leftJoin('applicants as a', 'a.id', '=', 'requests.applicantId')
                ->leftJoin('governorates as g', 'g.id', '=', 'a.governorateId')
                ->select("g.id as govId", DB::raw('count(*) as total'))
                ->groupBy('govId')
                ->get(); 
                break;
            default:
               return response()->json("you must input groupBy [sector or certificateType or governorate] only .. !" ,400);
                break;
        }
        $response = array();
        foreach ($data as  $item) {
            if(!empty($item->secId)){
                $obj=[
                    "id"        => $item->secId,
                    "nameAr"    =>  Sector::find($item->secId)->nameAr,                    
                    "nameEn"    =>  Sector::find($item->secId)->nameEn,                
                    "count"     =>  $item->total                                  
                ]; 
                if (!in_array($obj, $response)){
                    array_push($response,$obj);
                }
            }
            if(!empty($item->certificateTypeId)){
                $obj=[
                    "id"        =>  $item->certificateTypeId,
                    "nameAr"    =>  CertificateType::find($item->certificateTypeId)->nameAr,                    
                    "nameEn"    =>  CertificateType::find($item->certificateTypeId)->name,                
                    "count"     =>  $item->total                                  
                ]; 
                if (!in_array($obj, $response)){
                    array_push($response,$obj);
                }
            }
            if(!empty($item->govId)){
                $obj=[
                    "id"        =>  $item->govId,
                    "nameAr"    =>  Governorate::find($item->govId)->nameAr,                    
                    "nameEn"    =>  Governorate::find($item->govId)->nameEn,                 
                    "count"     =>  $item->total                                  
                ]; 
                if (!in_array($obj, $response)){
                    array_push($response,$obj);
                }
            }
        }
       return response()->json($response ,200);
    }

    public function ExpensesBySector(Request $request){    
        $this->validate($request, [
            'sectorId'      => 'nullable|numeric',
            'expensesItem'  => 'required|string|in:powerResources,localSpareParts,importedSpareParts,wages,annualDepreciation,researchAndDevelopment,marketingExpenses,administrativeExpenses,localComponents,localPackagingComponents,importedComponents,importedPackagingComponents'
        ]);

        $sectors=DB::table('assessments')
        ->join('requests as r', 'r.assessmentId', '=', 'assessments.id')
        ->select("r.sectorId")->groupBy('r.sectorId')
        ->get();

        if(!empty($request->sectorId)){
            $sectors= [['sectorId' => $request->sectorId]];
        }
        switch ($request->expensesItem) {
            case 'localComponents':
                $response = array();
                foreach($sectors as $sec){
                    if(empty($request->sectorId)){
                        $sectorId=  $sec->sectorId;
                    }else{
                        $sectorId=  $sec['sectorId'];
                    }
                    if(empty(Sector::find($sectorId))){
                        return response()->json("not found this sector",400);
                    }
                        $allAssessments=DB::table('assessments')
                        ->join('requests as r', 'r.assessmentId', '=', 'assessments.id')
                        ->select("r.sectorId as secId","r.id as requestId","assessments.id as assId" , "assessments.annualProductionCapacity","assessments.isTotals","assessments.localComponentsTotals")
                        ->where("r.sectorId" , $sectorId)->get();
                        
                        $componentPrice = array();         
                        foreach ($allAssessments as  $value) {
                            if($value->isTotals){
                                    $componentPrice[]=$value->localComponentsTotals;
                            }else{
                                $components=Component::where("assessmentId",$value->assId)->where("isPackaging",0)->where("isImported",0)->get();
                                foreach($components as $com){
                                    $componentPrice[]= (($com['unitPrice']) * $com['quantity'])* $value->annualProductionCapacity;
                                } 
                            }
                        }
                        $obj=[
                            "id"        => $sectorId,
                            "nameAr"    =>  Sector::find($sectorId)->nameAr,                    
                            "nameEn"    =>  Sector::find($sectorId)->nameEn,                
                            "expenses"  =>  array_sum($componentPrice) ?? 0                               
                        ]; 
                        if (!in_array($obj, $response)){
                            array_push($response,$obj);
                        }
                }
                break;
            case 'localPackagingComponents':
                    $response = array();
                    foreach($sectors as $sec){
                        if(empty($request->sectorId)){
                            $sectorId=  $sec->sectorId;
                        }else{
                            $sectorId=  $sec['sectorId'];
                        }
                        if(empty(Sector::find($sectorId))){
                            return response()->json("not found this sector",400);
                        }
                            $allAssessments=DB::table('assessments')
                            ->join('requests as r', 'r.assessmentId', '=', 'assessments.id')
                            ->select("r.sectorId as secId","r.id as requestId","assessments.id as assId" , "assessments.annualProductionCapacity","assessments.isTotals","assessments.localPackagingComponentsTotals")
                            ->where("r.sectorId" , $sectorId)->get();
                            
                            $componentPrice = array();         
                            foreach ($allAssessments as  $value) {
                                if($value->isTotals){
                                        $componentPrice[]=$value->localPackagingComponentsTotals;
                                }else{
                                    $components=Component::where("assessmentId",$value->assId)->where("isPackaging",1)->where("isImported",0)->get();
                                    foreach($components as $com){
                                        $componentPrice[]= (($com['unitPrice']) * $com['quantity'])* $value->annualProductionCapacity;
                                    } 
                                }
                            }
                            $obj=[
                                "id"        => $sectorId,
                                "nameAr"    =>  Sector::find($sectorId)->nameAr,                    
                                "nameEn"    =>  Sector::find($sectorId)->nameEn,                
                                "expenses"  =>  array_sum($componentPrice) ?? 0                                
                            ]; 
                            if (!in_array($obj, $response)){
                                array_push($response,$obj);
                            }
                    }
                    break;
            case 'importedComponents':
                    $response = array();
                    foreach($sectors as $sec){
                        if(empty($request->sectorId)){
                            $sectorId=  $sec->sectorId;
                        }else{
                            $sectorId=  $sec['sectorId'];
                        }
                        if(empty(Sector::find($sectorId))){
                            return response()->json("not found this sector",400);
                        }
                            $allAssessments=DB::table('assessments')
                            ->join('requests as r', 'r.assessmentId', '=', 'assessments.id')
                            ->select("r.sectorId as secId","r.id as requestId","assessments.id as assId" , "assessments.annualProductionCapacity","assessments.isTotals","assessments.importedComponentsTotals")
                            ->where("r.sectorId" , $sectorId)->get();
                            
                            $componentPrice = array();         
                            foreach ($allAssessments as  $value) {
                                if($value->isTotals){
                                        $componentPrice[]=$value->importedComponentsTotals;
                                }else{
                                    $components=Component::where("assessmentId",$value->assId)->where("isPackaging",0)->where("isImported",1)->get();
                                    foreach($components as $com){
                                        $componentPrice[]= ((($com['unitPrice']) * $com['quantity'] * $com['rate']) + $com['CIF'])* $value->annualProductionCapacity;
                                    } 
                                }
                            }
                            $obj=[
                                "id"        => $sectorId,
                                "nameAr"    =>  Sector::find($sectorId)->nameAr,                    
                                "nameEn"    =>  Sector::find($sectorId)->nameEn,                
                                "expenses"  =>  array_sum($componentPrice) ?? 0                                
                            ]; 
                            if (!in_array($obj, $response)){
                                array_push($response,$obj);
                            }
                    }
                    break;
            case 'importedPackagingComponents':
                    $response = array();
                    foreach($sectors as $sec){
                        if(empty($request->sectorId)){
                            $sectorId=  $sec->sectorId;
                        }else{
                            $sectorId=  $sec['sectorId'];
                        }
                        if(empty(Sector::find($sectorId))){
                            return response()->json("not found this sector",400);
                        }
                            $allAssessments=DB::table('assessments')
                            ->join('requests as r', 'r.assessmentId', '=', 'assessments.id')
                            ->select("r.sectorId as secId","r.id as requestId","assessments.id as assId" , "assessments.annualProductionCapacity","assessments.isTotals","assessments.importedPackagingComponentsTotals")
                            ->where("r.sectorId" , $sectorId)->get();
                            
                            $componentPrice = array();         
                            foreach ($allAssessments as  $value) {
                                if($value->isTotals){
                                        $componentPrice[]=$value->importedPackagingComponentsTotals;
                                }else{
                                    $components=Component::where("assessmentId",$value->assId)->where("isPackaging",1)->where("isImported",1)->get();
                                    foreach($components as $com){
                                        $componentPrice[]= ((($com['unitPrice']) * $com['quantity'] * $com['rate']) + $com['CIF'])* $value->annualProductionCapacity;
                                    } 
                                }
                            }
                            $obj=[
                                "id"        => $sectorId,
                                "nameAr"    =>  Sector::find($sectorId)->nameAr,                    
                                "nameEn"    =>  Sector::find($sectorId)->nameEn,                
                                "expenses"  =>  array_sum($componentPrice) ?? 0                               
                            ]; 
                            if (!in_array($obj, $response)){
                                array_push($response,$obj);
                            }
                    }
                    break;
            default:
                $data=DB::table('assessments')
                ->join('requests as r', 'r.assessmentId', '=', 'assessments.id');
                        if(!empty($request->sectorId)){
                            $data=$data->where("r.sectorId",$request->sectorId);
                        }else{
                            $data=$data->join('sectors as s', 's.id', '=', 'r.sectorId');
                        }
                        $data=$data->select("r.sectorId", DB::raw('sum(assessments.'.$request->expensesItem.') as total'))
                        ->groupBy('r.sectorId')->get();
                        $response = array();
                        foreach ($data as  $item) {
                            $obj=[
                                "id"        => $item->sectorId,
                                "nameAr"    =>  Sector::find($item->sectorId)->nameAr,                    
                                "nameEn"    =>  Sector::find($item->sectorId)->nameEn,                
                                "expenses"  =>  ($item->total) ?? 0                                 
                            ]; 
                            if (!in_array($obj, $response)){
                                array_push($response,$obj);
                            }
                        }
                break;
        }
        return response()->json($response ,200);
        
    }

    public function expensesBySectorKeys(Request $request){
        $data=[ 
            ["id" => "1","nameEn" => "powerResources" ,"nameAr" => "مصادر الطاقه","key" => "powerResources"],
            ["id" => "2","nameEn" => "localSpareParts" ,"nameAr" => "قطع غيار محليه","key" => "localSpareParts"],
            ["id" => "3","nameEn" => "importedSpareParts" ,"nameAr" => "قطع غيار مستورده","key" => "importedSpareParts"],
            ["id" => "4","nameEn" => "wages" ,"nameAr" => "الاجور","key" => "wages"],
            ["id" => "5","nameEn" => "annualDepreciation" ,"nameAr" => "الاهلاك السنوي","key" => "annualDepreciation"],
            ["id" => "6","nameEn" => "researchAndDevelopment" ,"nameAr" => "خدمات وابحاث","key" => "researchAndDevelopment"],
            ["id" => "7","nameEn" => "marketingExpenses" ,"nameAr" => "مصروفات تسويقيه","key" => "marketingExpenses"],
            ["id" => "8","nameEn" => "administrativeExpenses" ,"nameAr" => "مصروفات اداريه","key" => "administrativeExpenses"],
            ["id" => "9","nameEn" => "localComponents" ,"nameAr" => "مكونات محليه","key" => "localComponents"],
            ["id" => "10","nameEn" => "localPackagingComponents" ,"nameAr" => "مكونات التعبئه المحليه","key" => "localPackagingComponents"],
            ["id" => "11","nameEn" => "importedComponents" ,"nameAr" => "مكونات  مستورده","key" => "importedComponents"],
            ["id" => "12","nameEn" => "importedPackagingComponents" ,"nameAr" => "مكونات التعبئه المستورده","key" => "importedPackagingComponents"]
        ];
        return response()->json($data ,200);
    }
    //reports
    public function mail($applicantId,$emp)
    {
        $setting = Setting::first();
        $app = Applicant::find($applicantId);
        $user= User::find($app->userId);
        $app_email=$user->email;
        $app_name=$user->name;
        $emp_name=$emp->name;
        app('App\Http\Controllers\SettingsController')->SendMailSettings();
        Mail::to($app_email)->send(new returnToCompany($app_name, $emp_name));
    }
    
    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
