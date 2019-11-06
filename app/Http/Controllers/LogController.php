<?php

namespace App\Http\Controllers;

use App\Model\Assessment;
use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\Log;
use App\Model\RequestModel;
use App\Model\RequestStatus;
use App\Model\User;
use App\Model\Role;
use Carbon\Carbon;

class LogController extends Controller
{

    public function ListOfLogging(Request $request)
    {
        $rules = [
            'pageSize'               => 'nullable|numeric|min:1|max:100',
            'pageIndex'              => 'nullable|numeric',
            'sortColumn'             => 'nullable|string',
            'sortDirection'          => 'nullable|string',
            'searchText'             => 'nullable'
        ];
        $this->validate($request, $rules);
        $search = (!empty($request->get("searchText"))) ? $request->input("searchText")  : null;
        $pageSize = (!empty($request->get("pageSize"))) ? $request->input("pageSize") : 100;
        $pageIndex = (!empty($request->get("pageIndex"))) ? $request->input("pageIndex") : 0;
        $sortColumn = (!empty($request->get("sortColumn"))) ? $request->input("sortColumn") : "createdAt";
        $sortDirection = (!empty($request->get("sortDirection"))) ? $request->input("sortDirection") : "desc";

        $data = Log::with("user_by");
        if (!empty($search)) {
            $data = $data->where(function ($qq) use ($search) {
                $qq->whereHas("user_by", function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                })->Orwhere("Action", 'LIKE', '%' . $search . '%');
            });
            if (count($data->get()) == 0)
                return array();
        }
        if (!empty($sortColumn) || !empty($sortDirection)) {
            switch ($sortColumn) {
                case "userName":
                    $sortCol = "R.name";
                    $data=$data->join('users as R', 'R.id', '=', 'log.createdByUserId')
                    ->select("log.id","log.TableName","log.Column","log.Action","log.oldValue","log.newValue",
                    "log.RecordId","R.id as uID","R.name","R.roleId" ,"log.createdAt")
                    ->orderBy($sortCol, $sortDirection);
                    break;
                    case "actionName":
                    $sortCol = "Action";
                    $data = $data->orderBy($sortCol, $sortDirection);
                    break;
                default:
                    $sortCol = $sortColumn;
                    $data = $data->orderBy($sortCol, $sortDirection);
                    break;
            }
        }

        $count = $data->get()->count();
        $data = $data->paginate($pageSize, ['*'], 'page', $pageIndex + 1)->toArray()['data'];
        $response = array();
        foreach ($data as $log) {
            if($log['user_by'] != null){
                $userId =$log['user_by']['id'];
                $userName =$log['user_by']['name'];
                $userkey =Role::find($log['user_by']['roleId'])->key;
            }else{
                $userId   = $log['uID'];
                $userName = $log['name'];
                $userkey  = Role::find($log['roleId'])->key;
            }
            array_push($response,[
                "id"        => $log['id'],
                "tableName" => $log['TableName'],
                "column"    => $log['Column'],
                "action"    => $log['Action'],
                "oldValue"  => $log['oldValue'],
                "newValue"  => $log['newValue'],
                "recordId"  => $log['RecordId'],
                "user"      => [
                    "id"      => $userId,
                    "name"    => $userName,
                    "roleKey" => $userkey
                ],
                "createdAt" => $log['createdAt']
            ]);
        }
        return response()->json([
            "listCount" => $count,
            "data" => $response
        ] , 200);
    }

    public function Logging_by_id($id)
    {
        $log = Log::with("user_by")->where("id",$id)->first();
        return response()->json([
            "id" => $log->id,
            "tableName" => $log->TableName,
            "column" => $log->Column,
            "action" => $log->Action,
            "oldValue" => $log->oldValue,
            "newValue" => $log->newValue,
            "recordId" => $log->RecordId,
            "user" => [
                "id" => $log['user_by']['id'],
                "name" => $log['user_by']['name'],
                "roleKey" => Role::find($log['user_by']['roleId'])->key
            ],
            "createdAt" => $log->createdAt
        ] , 200);
    }

    public  function Logging_update($TableName,$colums,$old)
    {

        $user = userData();
        
        $data=$colums->getChanges();
        if(count($data) >0){            
            unset($data["updated_at"]);
            foreach($data as $key => $value){
                switch ($key) {
                    case 'statusId':
                        $oldValue = RequestStatus::find($old[$key])->nameAr ?? null;
                        $newValue = RequestStatus::find($value)->nameAr ?? null;
                         break;
                    default:
                       $oldValue = $old[$key];
                       $newValue = $value;
                        break;
                }
                $log=new Log();
                $log->RecordId=$colums->id;
                $log->createdByUserId= $user->id;
                $log->TableName=$TableName;
                $log->Action="update";
                $log->Column=$key;
                $log->oldValue= $oldValue;
                $log->newValue= $newValue;
                $log->createdAt=Carbon::now()->toDateTimeString();
                $log->save();
            }
        }
    }

    public  function Logging_create($TableName,$colums)
    {
        $user = userData();

        $log=new Log();
        $log->RecordId=$colums->id;
        $log->createdByUserId=$user->id;
        $log->TableName=$TableName;
        $log->Action="create";
        $log->Column=null;
        $log->oldValue=null;
        $log->newValue=null;
        $log->createdAt=Carbon::now()->toDateTimeString();
        $log->save();
    }

    public  function Logging_delete($TableName,$colums)
    {
        $user = userData();

        $log=new Log();
        $log->RecordId=$colums->id;
        $log->createdByUserId=$user->id;
        $log->TableName=$TableName;
        $log->Action="delete";
        $log->Column=null;
        $log->oldValue=null;
        $log->newValue=null;
        $log->createdAt=Carbon::now()->toDateTimeString();
        $log->save();
    }

    public  function Logging($TableName,$colums,$action ,$colums_change = null){
        $log=new Log();
        $log->RecordId=$colums->id;
        $log->createdByUserId=$colums->id;
        $log->TableName=$TableName;
        $log->Action=$action;
        $log->Column=$colums_change;
        $log->oldValue=null;
        $log->newValue=null;
        $log->createdAt=Carbon::now()->toDateTimeString();
        $log->save();
    }
}
