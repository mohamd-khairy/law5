<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\RequestModel;
use App\Model\RequestStatus;
use App\Model\RequestAction;
use App\Model\Action;
use App\Model\Setting;
use App\Model\Employee;
use App\Model\EmployeeSector;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AutoApprove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Auto:Approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic Approve Request';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();//->toDateTimeString();
        $setting = Setting::first();

        $Accepted = RequestStatus::where("key", "Accepted")->first()->id;
        $declined = RequestStatus::where("key", "Declined")->first()->id;
        $request = RequestModel::where("statusId",  $Accepted)->Orwhere("statusId",  $declined)->get();
        foreach ($request as $req) {
           $diff_in_minutes =  $now->diffInMinutes($req->created_at) / 60;
           if (($diff_in_minutes) >= ($setting->automaticIDAApproveDelay)) {
                $action = new RequestAction();
                switch ($req->statusId) {
                    case ($req->statusId == RequestStatus::where("key", "Accepted")->first()->id):
                        $reqq = RequestModel::find($req->id);
                        $reqq->statusId = RequestStatus::where("key", "AcceptanceConfirmed")->first()->id;
                        $reqq->isIDAFeesPaid = 1; /////////////
                        $reqq->isFEIFeesPaid = 1; ///////////
                        $reqq->save();
                        $action->actionId  = Action::where("key", "Accept")->first()->id;
                        break;
                    case ($req->statusId == RequestStatus::where("key", "Declined")->first()->id):
                        $reqq = RequestModel::find($req->id);
                        $reqq->statusId = RequestStatus::where("key", "DeclineConfirmed")->first()->id;
                        $reqq->save();
                        $action->actionId  = Action::where("key", "Decline")->first()->id;
                        break;
                    default:
                        break;
                }
                $action->requestId = $req->id;
                $action->byUserId =  null;
                $action->toUserId = $req->employeeId;
                $action->comment = null;
                $action->isAuto = 1;
                $action->save();
           }
        }
        $this->info('Approved successfully!'); 
    }
}
