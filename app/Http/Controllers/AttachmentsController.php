<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Attachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        $path = Storage::disk('public')->path('upload/'.$request->file);
        $file = File::get($path);
        $type = File::mimeType($path);
        return response()->make($file, 200, ['content-type' =>  $type ]);
    }
}
