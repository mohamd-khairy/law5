<?php namespace App\Http\Controllers;

use App\Model\Chamber;
use App\Model\AssessmentMethod;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ChambersController extends Controller
{

    const MODEL = Chamber::class;

    use RESTActions;

    public function addChamber(Request $request)
    {
        $this->validate($request, Chamber::$rules);
        $data=new Chamber();
        $data->nameAr=$request->nameAr;
        $data->nameEn=$request->nameEn;
        $data->assessmentMethod=$request->assessmentMethodId;
        $data->save();
        app('App\Http\Controllers\LogController')->Logging_create("chambers",$data);
        return $this->respond(Response::HTTP_CREATED, $data);
    }

    public function putChamber(Request $request,$id)
    {
        $this->validate($request, Chamber::$rules);
        $model = Chamber::find($id);
        if (is_null($model)) {
            return response()->json(__("chamber.noChamber"),400);
        }
        $model->nameAr=$request->nameAr;
        $model->nameEn=$request->nameEn;
        $model->assessmentMethod=$request->assessmentMethodId;
        $old=$model->getOriginal();
        $model->save();
        app('App\Http\Controllers\LogController')->Logging_update("chambers",$model,$old);
        return $this->respond(Response::HTTP_OK, $model);
    }

    public function get_list()
    {
        $chambers = Chamber::get();
        if (is_null($chambers)) {
            return response()->json(__("chamber.noChamber"),400);
        }
        $response = array();
        foreach ($chambers as $model) {
            array_push($response, [
                "id" => $model->id,
                "nameEn" => $model->nameEn,
                "nameAr" => $model->nameAr,
                "assessmentMethodId" => AssessmentMethod::find($model->assessmentMethod)->id,
                "assessmentMethodNameAr" => AssessmentMethod::find($model->assessmentMethod)->nameAr,
                "assessmentMethodNameEn" => AssessmentMethod::find($model->assessmentMethod)->nameEn,
            ]);
        }
        return $this->respond(Response::HTTP_OK, $response);
    }

    public function get_by_id($id)
    {
        $model = Chamber::where('id', $id)->first();
        if (is_null($model)) {
            return response()->json(__("chamber.noChamber"),400);
        }
        $assessmentMethod=AssessmentMethod::find($model->assessmentMethod);
        if (is_null($assessmentMethod)) {
            return response()->json(__("chamber.noassessmentMethod"),400);
        }
        $response = [
            "id" => $model->id,
            "nameEn" => $model->nameEn,
            "nameAr" => $model->nameAr,
            "assessmentMethodId" => $assessmentMethod->id,
            "assessmentMethodNameAr" => $assessmentMethod->nameAr,
            "assessmentMethodNameEn" => $assessmentMethod->nameEn,
        ];

        return $response;//$this->respond(Response::HTTP_OK, $response);
    }

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
