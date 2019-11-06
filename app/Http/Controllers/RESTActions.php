<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\Attachment;
use App\Model\Applicant;
use App\Model\RequestModel;
use App\Model\Section;
use App\Model\Assessment;
use App\Model\Employee;
use App\Model\EmployeeSector;

trait RESTActions
{

    public function all()
    {
        $m = self::MODEL;
        return $this->respond(Response::HTTP_OK, $m::get());
    }

    public function get($id)
    {
        $m = self::MODEL;
        $model = $m::where('id', $id)->first();
        if (is_null($model)) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
        return $this->respond(Response::HTTP_OK, $model);
    }

    public function add(Request $request)
    {
        $m = self::MODEL;
        $this->validate($request, $m::$rules);
        $data=$m::create($request->all());
        app('App\Http\Controllers\LogController')->Logging_create(strtolower(substr($m, 10)."s") , $data);

        return $this->respond(Response::HTTP_CREATED,$data );
    }

    public function put(Request $request, $id)
    {
        $m = self::MODEL;
        $this->validate($request, $m::$rules);
        $model = $m::find($id);
        if (is_null($model)) {
            return $this->respond(Response::HTTP_NOT_FOUND);
        }
        $old=$model->getOriginal();
        $model->update($request->all());
        app('App\Http\Controllers\LogController')->Logging_update(strtolower(substr($m, 10)."s"),$model,$old);

        return $this->respond(Response::HTTP_OK, $model);
    }

    public function remove($id)
    {
        $m = self::MODEL;
        $model = $m::find($id);
        if($this->check_after_delete($m , $id)){
            if (is_null($model)) {
                return $this->respond(Response::HTTP_NOT_FOUND);
            }
            if (isset($model->isDeleted)) {
                $model->update(['isDeleted' => 1]);
                $model->save();
            }
            $m::destroy($id);
            app('App\Http\Controllers\LogController')->Logging_delete(strtolower(substr($m, 10)."s") , $model);

            return $this->respond(Response::HTTP_OK, $model);
        }else{
            return response()->json(__('auth.noDelete'),400);//trans
        }
    }

    public function delete_file($id)
    {
        $m = self::MODEL;
        $model = $m::find($id);
        if ($model->requestId != null) {
            $model->update(['isDeleted' => 1]);
            $m::destroy($id);
        } else {
            app('filesystem')->disk('public')->delete('upload/'.$model->relativePath);
            $model->forceDelete();
        }
        app('App\Http\Controllers\LogController')->Logging_delete(strtolower(substr($m, 10)."s") , $model);
        return $this->respond(Response::HTTP_OK, $model);
    }
   

    public function upload(Request $request)
    {
        $m = self::MODEL;
        $rules = [
            "UploadFiles" => "required|file|mimes:pdf,jpeg,jpg,png|max:9216",
        ];
        $this->validate($request, $rules);
        $file = $request->file("UploadFiles");
        if (isset($file)) {
            $relativePath=app('filesystem')->disk('public')->put('upload', $file);
            $model = new Attachment();
            $model->relativePath = explode("/" , $relativePath)[1];
            $model->originalName = $file->getClientOriginalName();
            $model->save();
            app('App\Http\Controllers\LogController')->Logging_create(strtolower(substr($m, 10)."s") , $model);

            if (isset($model)) {
                return response()->json([
                    "id" =>  $model->id,
                    "url" => $model->relativePath,
                    "fileName" => $model->originalName,
                    "createdAt" => $model->created_at,
                    "updatedAt" => $model->updated_at
                ]);
            } else {
                return $this->response()->json("not Upload",400);
            }
        } else {
            return $this->response()->json("there is no file",400);
        }
    }

    public function check_after_delete($model,$id){
        switch ($model) {
            case 'App\Model\Chamber':
                $ass=Assessment::with("chamber")->where("chamberId",$id)->count();
                $emp=Employee::with("chamber")->where("chamberId",$id)->count();
                $sec=Section::with("chamber")->where("chamberId",$id)->count();
                if($ass==0 && $emp ==0 && $sec == 0){
                    $check=0;
                }else{
                    $check=1;
                }
                break;
            case 'App\Model\Section':
                $check=RequestModel::with("sections")->where("sectionId",$id)->count();
                break;
            case 'App\Model\Sector':
                $asss=RequestModel::with("sectors")->where("sectorId",$id)->count();
                $app=Applicant::with("sector")->where("sectorId",$id)->count();
                $emp_sec=EmployeeSector::with("sector")->where("sectorId",$id)->count();
                if($asss==0 && $app ==0 && $emp_sec == 0){
                    $check=0;
                }else{
                    $check=1;
                }
                break;
            
            default:
                $check=0;
                break;
        }
        if($check <= 0){
            return 1; // yes delete
        }
        return 0; // dont delete
    }

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
