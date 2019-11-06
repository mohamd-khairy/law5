<?php

namespace App\Http\Controllers;

use App\Model\RequestAction;
use App\Model\User;
use App\Model\Applicant;
use Illuminate\Http\Response;


class ActionController extends Controller
{
    const MODEL = "App\Model\RequestAction";

    use RESTActions;

    public function show($id)
    {
        $item = RequestAction::with("actions", "user_by", "user_to")->where('id', $id)->first();
        $username=User::find($item['user_to']['userId']);
        if(!empty($username)){
            $username=$username->name;
        }else{
            $username=null;
        }
        $response =  [
            "id" => $item['id'],
            "requestId" => $item['requestId'],
            "actionId" => $item['actionId'],
            // "actionNameEn" => $item['actions']['nameEn'],
            "actionName" => $item['actions']['nameAr'],
            "actionKey" => $item['actions']['key'],
            "byUserId" => $item['byUserId'],
            "byUserName" => $item['user_by']['name'],
            "toUserId" => $item['toUserId'],
            "toUserName" => $username,
            "isAuto" =>  ($item['isAuto'] == 0) ? false : true, 
            "comment" => $item['comment'],
            "createdAt" => $item['created_at'],
            "updatedAt" => $item['updated_at']
        ];
        return $this->respond(Response::HTTP_OK, $response);
    }

    public function get_request_actions_by_request_id($request_id)
    {
        $requestActions = RequestAction::with("actions", "user_by", "user_to")->where("requestId", $request_id)->orderBy("id", "Desc")->get();
        if (empty($requestActions)) {
            return response()->json(__("action.noAction"), 400);
        }
        //return $requestActions;
        $response = array();
       $i = count($requestActions);
        foreach ($requestActions as  $item) {
            if ($item['actions']['key'] == "Return") {
               $userApplicant  = Applicant::find($item['toUserId']);
                if(!empty($userApplicant)){
                    $username = User::find($userApplicant->userId);
                }
            }
            else if ($item['actions']['key'] == "Resend") {
                $username = User::find($item['toUserId']);
             } else {
                $username = User::find($item['user_to']['userId']);
            }
            if (!empty($username)) {
                $username = $username->name;
            } else {
                $username = null;
            }
            array_push($response, [
                "id" => $item['id'],
                "requestId" => (int)$request_id,
                "actionId" => $item['actionId'],
                // "actionNameEn" => $item['actions']['nameEn'],
                "actionName" => $item['actions']['nameAr'],
                "actionKey" => $item['actions']['key'],
                "byUserId" => $item['byUserId'],
                "byUserName" => $item['user_by']['name'],
                "toUserId" => $item['toUserId'],
                "toUserName" => $username, //applicant in case return only, under review_open,save 
                "isAuto" =>  ($item['isAuto'] == 0) ? false : true, 
                "comment" => $item['comment'],
                "createdAt" => $item['created_at'],
                "updatedAt" => $item['updated_at']
            ]);
        }
        return $this->respond(Response::HTTP_OK, $response);
    }

    public function get_last_request_action_by_request_id($request_id)
    {
        $item = RequestAction::with("actions", "user_by", "user_to")->where("requestId", $request_id)->latest()->first();
        if (empty($item)) {
            return response()->json(__("action.noAction"), 400);
        }
        $username=User::find($item['user_to']['userId']);
        if(!empty($username)){
            $username=$username->name;
        }else{
            $username=null;
        }
        $response =  [
            "id" => $item['id'],
            "requestId" => (int)$request_id,
            "actionId" => $item['actionId'],
            // "actionNameEn" => $item['actions']['nameEn'],
            "actionName" => $item['actions']['nameAr'],
            "actionKey" => $item['actions']['key'],
            "byUserId" => $item['byUserId'],
            "byUserName" => $item['user_by']['name'],
            "toUserId" => $item['toUserId'],
            "toUserName" => $username,
            "isAuto" =>  ($item['isAuto'] == 0) ? false : true, 
            "comment" => $item['comment'],
            "createdAt" => $item['created_at'],
            "updatedAt" => $item['updated_at']
        ];
        return $this->respond(Response::HTTP_OK, $response);
    }

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }
}
