<?php

namespace App\Http\Controllers; 
use App\Model\Log;
use App\Model\Section;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class SectionController extends Controller
{
     const MODEL = "App\Model\Section";

     use RESTActions;

     public function sectionsList()
     {
       $sections = DB::table('chambers')
                       ->join('sections', 'chambers.id', '=', 'sections.chamberId')
                       ->select('sections.id', 'sections.nameAr as nameAr', 'sections.nameEn as nameEn', 'sections.chamberId', 'chambers.nameAr as chamberNameAr','chambers.nameEn as chamberNameEn')
                       ->where("sections.isDeleted",0)->get(); 
        if(is_null($sections))
        {
        	return $this->respond(Response::HTTP_NOT_FOUND);
        } 

         return $this->respond(Response::HTTP_OK, $sections);              
     }  

    public function getSection($id)
    {
        $section = DB::table('chambers')
                    ->join('sections', 'chambers.id', '=', 'sections.chamberId')
                    ->select('sections.id', 'sections.nameAr as nameAr', 'sections.nameEn as nameEn', 'sections.chamberId', 'chambers.nameAr as chamberNameAr','chambers.nameEn as chamberNameEn') 
                    ->where('sections.id', $id)->where("isDeleted",0)->first();
        if(is_null($section))
        {
            return $this->respond(Response::HTTP_NOT_FOUND);
        } 

        return $this->respond(Response::HTTP_OK, $section);                

    }

    public function getSectionByChamberId($chamberId){
        $section = Section::where("chamberId",$chamberId)
        ->select("id","nameAr","nameEn")->get();
        return $this->respond(Response::HTTP_OK, $section); 
    }

    public function updateSection(Request $request, $id)
    {
        
        $this->validate($request, Section::$rules);
        $input = $request->all();
        $section = Section::where('id', $id)->first();
        if(is_null($section))
        {
          return $this->respond(Response::HTTP_NOT_FOUND);
        }
        else
        {
        $section->nameEn = $input['nameEn'];
        $section->nameAr = $input['nameAr'];
        $section->chamberId = $input['chamberId']; 
        $old=$section->getOriginal();
        $section->save();
        app('App\Http\Controllers\LogController')->Logging_update("sections",$section,$old);
        return $this->respond(Response::HTTP_OK,$section);
        }
        
    }   

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }   
}
