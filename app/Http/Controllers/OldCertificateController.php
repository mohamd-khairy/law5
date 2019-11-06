<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Model\OldCertificate;
use App\Model\CertificateType;


class OldCertificateController extends Controller
{

    use RESTActions;

    public function index(Request $request)
    {
        $rules = [
            'certificateTypeId'       =>   'nullable|integer',
            'manufacturedByOthers'    =>   'nullable|boolean',
            'issueDateFrom'           =>   'nullable|date|before:tomorrow',
            'issueDateTo'             =>   'nullable|date|before:tomorrow',
            'searchText'              =>   'nullable',
            'pageSize'                =>   'numeric|min:1|max:100',
            'pageIndex'               =>   'numeric',
        ];
        $this->validate($request, $rules);

        $DEFAULT_PAGE_SIZE = 50;
        $DEFAULT_PAGE_INDEX = 0;
        
        $issueDateFrom = $request->filled('issueDateFrom')? carbon::parse($request->issueDateFrom) : "0000-00-00";
        $issueDateTo = $request->filled('issueDateTo')? carbon::parse($request->issueDateTo) : Carbon::now();

        $data = OldCertificate::whereBetween('startDate', [$issueDateFrom, $issueDateTo]);

        if ($request->filled('certificateTypeId')) {
            $data = $data->where('certificateTypeId', $request->certificateTypeId);
        }

        if ($request->filled('manufacturedByOthers')) {
            $data = $data->where('manufacturingByOthers', $request->manufacturedByOthers);
        }
        
        if ($request->filled('searchText')) {
            $searchFor = $request->searchText;

            $data = $data->searchCompanyName('where', $searchFor)
            ->searchChamber('orWhere', $searchFor)
            ->searchCertificateNumber('orWhere', $searchFor);
        }
        $responseCountRecords = $data->count();

        $pageSize = (!empty($request->pageSize)) ? $request->pageSize : $DEFAULT_PAGE_SIZE;
        $pageIndex = (!empty($request->pageIndex)) ? $request->pageIndex : $DEFAULT_PAGE_INDEX;
        $skip = $pageSize * $pageIndex;
        $limit = $pageSize;

        $data = $data->skip($skip)->take($limit)->get();
        $response = array();
        foreach ($data as $item) {
            array_push($response, $item);
        }
        
        if (!empty($response)) {
            return $this->respond(Response::HTTP_OK, [
                "listCount" => $responseCountRecords,
                "data" => $response
            ]);
        }
        else {
            return [];
        }
    }
    
    public function show(Request $request, $id)
    {
        $certificate = OldCertificate::findOrFail($id);

        if (!empty($certificate)) {
            return $this->respond(Response::HTTP_OK, $certificate);
        }
    
        return $this->respond(Response::HTTP_NOT_FOUND);
    }

    public function generatePDF($id)
    {
        $certificate = OldCertificate::findOrFail($id);

        //make Pdf
        $pdf = app()->make('dompdf.wrapper');
        $pdf->setPaper('A4', 'landscape');

        $type = CertificateType::findOrFail($certificate->certificateTypeId);

        if ($type->name == "Export Fund") {

            $manufacture = $certificate->manufacturingByOthers;
            $pdf->loadView("oldCertificate-export", compact('certificate', 'manufacture'));
            
        } elseif ($type->name == "Law5") {

            $pdf->loadView("oldCertificate-law5", compact('certificate'));
        }
        return response()->make($pdf->stream(), 200, ['content-type' =>  'application/pdf']);
    }


    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
