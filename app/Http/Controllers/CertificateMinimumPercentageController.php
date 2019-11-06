<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        // Convert fromDate to date string i.e. 2019-09-09 
        $request->merge(['fromDate' => Carbon::parse($request->fromDate)->toDateString()]);

        if (! CertificateType::where('id', $request->certificateTypeId)->exists()) {
            return response()->json("certificate type doesn't exist", 404);
        }

        if ($request->minimumPercentage < 0 || $request->minimumPercentage > 100) {

            return response()->json("Minimum Percentage must be in range (0 - 100)", 400);
        }

        if (CertificateMinimumPercentage::where('certificateTypeId', $request->certificateTypeId)
            ->where('fromDate', $request->fromDate)
            ->exists()) {

            return response()->json("Certificate type already exists in the that date", 400);
        }

        //Create new Certificate minimum percentage
        $data = $request->all();
        $certMinPercentage = new CertificateMinimumPercentage($data);
        $certMinPercentage->save();

        
        $response = $certMinPercentage->id;
        app('App\Http\Controllers\LogController')->Logging_create("certificateMinimumPercentage", $certMinPercentage);
        return response()->json($response, 200); // OK
    }


    public function destroy($id)
    {
        //there's more than one record in table.
        if (CertificateMinimumPercentage::count() > 1) {

            $certMinPercentage = CertificateMinimumPercentage::where('id', $id)->first();
            if ($certMinPercentage->delete()) {

                app('App\Http\Controllers\LogController')->Logging_delete("certificateMinimumPercentage" , $certMinPercentage);
                return response()->json("Record deleted", Response::HTTP_OK); // OK
            }
        }

        return response()->json("Couldn't delete record", Response::HTTP_NOT_IMPLEMENTED);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'certificateTypeId' => 'required|numeric',
            'fromDate' => 'nullable|date',
            'minimumPercentage' => 'required|numeric'
        ]);
        
        // Convert fromDate to date string i.e. 2019-09-09 
        $request->merge(['fromDate' => Carbon::parse($request->fromDate)->toDateString()]);

        if ($request->minimumPercentage < 0 || $request->minimumPercentage > 100) {

            return response()->json("Minimum percentage must be in range (0 - 100)", 400);
        }

        if (CertificateMinimumPercentage::where('certificateTypeId', $request->certificateTypeId)
            ->where('fromDate', $request->fromDate)
            ->exists()) {

            return response()->json("Certificate type already exists in the that date", 400);
        }
        
        $certMinPercentage = CertificateMinimumPercentage::findOrFail($id);
        $old = $certMinPercentage->getOriginal();
        
        $data = $request->all();
        $certMinPercentage->update($data);

        if ($certMinPercentage->save()) {
            
            app('App\Http\Controllers\LogController')->Logging_update("certificateMinimumPercentage", $certMinPercentage, $old);
            return response()->json("Updated successfully", 200);
        }
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

        $certMinPercentage = CertificateMinimumPercentage::findOrFail($id);
        $certType = CertificateType::findOrFail($certMinPercentage['certificateTypeId']);
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
