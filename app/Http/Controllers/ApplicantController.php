<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Applicant;
use App\Model\Attachment;
use Illuminate\Http\Response;
use App\Model\User;

class ApplicantController extends Controller
{
  const MODEL = "App\Model\Applicant";

  use RESTActions;

  public function saveApplicantProfile(Request $request)
  {
    $this->validate($request, Applicant::$rules);
    
    $user = userData();
    $userId = $user->id;
    
    $applicant = Applicant::where('userId', $userId)->first();
    if (is_null($applicant)) {
      return response()->json(__("applicant.noUser"),400);
    } else {
      $applicant = Applicant::find($applicant->id);
      $applicant->facilityName = $request->input("facilityName");
      $applicant->legalEntityIdentifier = $request->input("legalEntityIdentifier");
      $applicant->managerName = $request->input("managerName");
      $applicant->sectorId = $request->input("sectorId");
      $applicant->userId = $userId;
      $applicant->isInsideIndusterialArea = $request->input("isInsideIndusterialArea");
      $applicant->governorateId = $request->input("governorateId");
      $applicant->cityId = $request->input("cityId");
      if ($applicant->isInsideIndusterialArea == true) {
        $applicant->indusrialAreaName = $request->input("indusrialAreaName");
        $applicant->blockNumber = $request->input("blockNumber");
        $applicant->areaNumber = $request->input("areaNumber");
        $applicant->areaOrDistrict = null;
        $applicant->buildingNumber = null;
      } else {
        $applicant->indusrialAreaName = null;
        $applicant->blockNumber = null;
        $applicant->areaNumber = null;
        $applicant->areaOrDistrict = $request->input("areaOrDistrict");
        $applicant->buildingNumber = $request->input("buildingNumber");
      }
      $applicant->telephone = $request->input("telephone");
      $applicant->fax = $request->input("fax");
      $applicant->taxCard = $request->input("taxCard");
      $applicant->authorityAcceptanceNumber = $request->input("authorityAcceptanceNumber");
      $applicant->commercialRecord = $request->input("commercialRecord");
      $applicant->licenseNumber = $request->licenseNumber;
      $applicant->factoryArea = $request->input("factoryArea");
      $applicant->investmentCostAtConstructionTime = $request->input("investmentCostAtConstructionTime");
      $applicant->currentInvestmentCosts = $request->input("currentInvestmentCosts");
      $applicant->extentOfEnvironmentalConditionsImplementationAndTrashDisposal = $request->input("extentOfEnvironmentalConditionsImplementationAndTrashDisposal");
      $applicant->extentOfDangerousMaterialsUsage = $request->input("extentOfDangerousMaterialsUsage");
      $applicant->experienceTypeId = null;
      $applicant->annualInsuranceValue = $request->input("annualInsuranceValue");
      $applicant->certificatesAndExtentOfSpecificationsMatching = $request->input("certificatesAndExtentOfSpecificationsMatching");
      $applicant->insuredWorkersCount = $request->input("insuredWorkersCount");
      $applicant->entryNumberInIndusterialRecord = $request->input("entryNumberInIndusterialRecord");
      $applicant->yearOfFirstCertificate = $request->input("yearOfFirstCertificate");
      $applicant->expirationDate = date('Y-m-d', strtotime($request->input("expirationDate")));
      $attachmentIdsCollection = collect($request->input("attachmentIds"));
      $attachmentIds = $attachmentIdsCollection->toArray();
      $old=$applicant->getOriginal();
      if ($applicant->save()) {
        if (!empty($attachmentIds)) {
          $attachs = Attachment::where("applicantId", $applicant->id)->get();
          if (!empty($attachs)) {
            foreach ($attachs as $ids) {
              $item = Attachment::find($ids->id);
              $item->requestId = null;
              $item->applicantId = null;
              $item->isRepresentativeProof = false;
              $item->save();
            }
          }
          if (!empty($attachmentIds)) {
            foreach ($attachmentIds as $ids) {
              $item = Attachment::find($ids);
              if (!empty($item)) {
                $item->applicantId = $applicant->id;
                $item->isRepresentativeProof = false;
                $item->requestId = null;
                $item->save();
              }
            }
          }
        } else {
          Attachment::where("applicantId", $applicant->id)->forceDelete();
        }
        $user=User::find($applicant->userId);
        $user->name=$applicant->facilityName;
        $user->save();
        app('App\Http\Controllers\LogController')->Logging_update("applicants",$applicant,$old);
        return $this->respond(Response::HTTP_OK, $applicant->id);
      } else {
        return response()->json("Failed to update applicant", 400);
      }
    }
  }

  public function getApplicantById(Request $request, $id)
  {
    $applicant = Applicant::where('id', $id)->first();
    if (is_null($applicant)) {
      return response()->json(__("applicant.noUser"),400);
    } else {
      return $this->respond(Response::HTTP_OK, $applicant);
    }
  }

  public function getApplicantProfile(Request $request)
  {
    $user = userData();
    $userId = $user->id;

    $applicant = Applicant::where('userId', $userId)->first();
    if (is_null($applicant)) {
      return response()->json(__("applicant.noUser"),400);
    } else {
      return $this->respond(Response::HTTP_OK, $applicant);
    }
  }

  public function isFullRegistered(Request $request)
  {

    $user  = userData();

    $applicant = Applicant::where('userId', $user->id)->first();

    if (is_null($applicant)) {
      return response()->json(__("applicant.noUser"),400);
    } else {
      if (
        is_null($applicant->facilityName) ||
        is_null($applicant->legalEntityIdentifier) ||
        is_null($applicant->managerName) ||
        is_null($applicant->sectorId) ||
        is_null($applicant->userId) ||
        is_null($applicant->governorateId) ||
        is_null($applicant->cityId) ||
        is_null($applicant->telephone) ||
        is_null($applicant->authorityAcceptanceNumber) ||
        is_null($applicant->commercialRecord) ||
        //is_null($applicant->licenseNumber) ||
        is_null($applicant->factoryArea) ||
        is_null($applicant->taxCard) ||
        is_null($applicant->investmentCostAtConstructionTime) ||
        is_null($applicant->currentInvestmentCosts) ||
        is_null($applicant->extentOfEnvironmentalConditionsImplementationAndTrashDisposal) ||
        is_null($applicant->extentOfDangerousMaterialsUsage) ||
        is_null($applicant->annualInsuranceValue) ||
        is_null($applicant->certificatesAndExtentOfSpecificationsMatching) ||
        is_null($applicant->insuredWorkersCount) ||
        is_null($applicant->entryNumberInIndusterialRecord) ||
        is_null($applicant->yearOfFirstCertificate) ||
        is_null($applicant->expirationDate)) {
        return "false";
      }
    }
    return "true";
  }

  protected function respond($status, $data = [])
  {
    return response()->json($data, $status);
  }
}
