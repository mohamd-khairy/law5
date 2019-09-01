<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Model\CertificateType;
use App\Model\CertificateMinimumPercentage;


class CertificateMinimumPercentageController extends Controller
{
        
    public function store(Request $request)
    {
             
        $this->validate($request, [
            'certificateTypeId' => 'required|numeric',
            'fromDate' => 'nullable|date',
            'minimumPercentage' => 'required|numeric'
        ]);

        if (! CertificateType::where('id', $request->certificateTypeId)->exists()) {
            return response()->json("certificate type doesn't exist", 401);
        }

        if ($request->minimumPercentage < 0 || $request->minimumPercentage > 100) {

            return response()->json("Minimum Percentage must be in range (0 - 100)", 401);
        }

        if (CertificateMinimumPercentage::where('certificateTypeId', $request->certificateTypeId)
            ->where('fromDate', $request->fromDate)
            ->exists()) {

            return response()->json("Certificate type already exists in the that date", 401);
        }

        //Create new Certificate minimum percentage
        $data = $request->all();
        $certCpyReq = new CertificateMinimumPercentage($data);
        $certCpyReq->save();

        $response = $certCpyReq->id;
        return response()->json($response, 200); // OK   
    }


    public function destroy($id)
    {
        //there's more than one record in table.
        if (CertificateMinimumPercentage::count() > 1) {

            if (CertificateMinimumPercentage::where('id', '>', $id)->delete()) {

                return response()->json("Record deleted", 200); // OK
            }
        }

        return response()->json("Couldn't delete record", 401);        
        
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'certificateTypeId' => 'required|numeric',
            'fromDate' => 'nullable|date',
            'minimumPercentage' => 'required|numeric'
        ]);

        if ($request->minimumPercentage < 0 || $request->minimumPercentage > 100) {

            return response()->json("Minimum Percentage must be in range (0 - 100)", 401);
        }

        if (CertificateMinimumPercentage::where('certificateTypeId', $request->certificateTypeId)
            ->where('fromDate', $request->fromDate)
            ->exists()) {

            return response()->json("Certificate type already exists in the that date", 401);
        }

        $certMinPercentage = CertificateMinimumPercentage::where("id", $id)->first();
        $certMinPercentage->certificateTypeId = $request->certificateTypeId;
        $certMinPercentage->fromDate = $request->fromDate;
        $certMinPercentage->minimumPercentage = $request->minimumPercentage;

        if ($certMinPercentage->save())
            return response()->json("Certficate minimum percentage updated successfully", 200);
        else
            return response()->json("Error Updating certficate minimum percentage", 400);
        
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'certificateTypeId' => 'nullable|numeric',
            ]);

        // return all
        if (empty($request->query('certificateTypeId'))) {

            $data = CertificateMinimumPercentage::all();
        }
        
        else {
            $data = CertificateMinimumPercentage::where('certificateTypeId', $request->query('certificateTypeId'))->get();
        }

        $countObjectsResponse = $data->count();
        $response = array();
        foreach ($data as $item) {
            $certType = CertificateType::where('id', $item['certificateTypeId'])->first();
              
            array_push(
                $response,
                [
                    "id" => $item['id'],

                    "certificateType" => [
                        "id" => $certType->id, 
                        "NameAr" => $certType->nameAr
                    ],

                    "fromDate" => $item['fromDate'],
                    "minimumPercentage" => $item['minimumPercentage'],
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
        if (!is_int($id)) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }

        $certMinPercentage = CertificateMinimumPercentage::where('id', $id)->first();
        $certType = CertificateType::where('id', $certMinPercentage['certificateTypeId'])->first();
        $response =
        [
            "id" => $certMinPercentage['id'],

            "certificateType" => [
                "id" => $certType->id, 
                "NameAr" => $certType->nameAr
            ],

            "fromDate" => $certMinPercentage['fromDate'],
            "minimumPercentage" => $certMinPercentage['minimumPercentage'],
        ];


        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, $response);
        }
        else {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
    }

    public function showByDate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|date',
            ]);

        $certificateTypes = CertificateType::all();
        $data = array();
        foreach ($certificateTypes as $type) {
            array_push(
                $data,
                    [
                        'certMinPercentage' => CertificateMinimumPercentage::orderBy('fromDate', 'desc')
                            ->where('fromDate', '<=', $request->query('date'))
                            ->where('certificateTypeId', $type->id)->first(),
                        'certificateType' => $type->name,

                    ]
                );
            
        }

        $countObjectsResponse = count($data);
        $response = array();
        foreach ($data as $item) {
            if ($item['certificateType'] == "Law5") {
                
                array_push(
                    $response,
                    [
                        "law5CertificatePercentage" => $item['certMinPercentage']->minimumPercentage,
                    ]
                );
            } else if ($item['certificateType'] == "Export Fund") {

                array_push(
                    $response,
                    [
                        "exportFundPercentage" => $item['certMinPercentage']->minimumPercentage,
                    ]
                );
            }

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

    protected function respond($status, $data = [])
    {
      return response()->json($data, $status);
    }
}
