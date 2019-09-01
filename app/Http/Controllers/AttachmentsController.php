<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\Attachment;
use App\Model\Applicant;
use App\Model\RequestModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentsController extends Controller
{
    const MODEL = "App\Model\Attachment";
    const FolderFileUploadName = "public/uploads";

    use RESTActions;

    public function auto_delete(){
        $twoDay = Carbon::now()->addDays(-2);
        $allAttach = Attachment::withTrashed()->where('created_at', "<", $twoDay)->where("applicantId", null)->where("requestId", null)->get();
        foreach ($allAttach as $att) {
            app('filesystem')->disk('public')->delete('upload/'.$att->relativePath);
            $att->forceDelete();
        }
    }

    public function download(Request $request){
        $this->validate($request, ['file' => 'required']);
        
        $file = File::get('public/storage/upload/'.$request->file);
        $type = File::mimeType('public/storage/upload/'.$request->file);
        return response()->make($file, 200, ['content-type' =>  $type ]);
    }
}
