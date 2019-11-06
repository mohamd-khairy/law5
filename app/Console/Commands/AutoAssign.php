<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\RequestModel;
use App\Model\RequestStatus;
use App\Model\RequestAction;
use App\Model\Action;
use App\Model\Employee;
use App\Model\EmployeeSector;
use Illuminate\Support\Facades\DB;
use App\Model\Setting;
use Carbon\Carbon;

class AutoAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Auto:Assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Request To Employee';

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
        $setting = Setting::first();
        $status_id = RequestStatus::where("key", "New")->first()->id;
        $requests = RequestModel::where("statusId", $status_id)->get();
        $now = Carbon::now(); //->toDateTimeString();
        foreach ($requests as $reques) {
            $diff_in_minutes =  $reques->created_at->diffInMinutes($now)/60;
            if (($diff_in_minutes) >= ($setting->automaticAssignDelay)) {
                $employee = EmployeeSector::where("sectorId", $reques->sectorId)->first();
                if (!empty($employee)) {
                    $req = RequestModel::find($reques->id);
                    $req->employeeId = $employee->employeeId;
                    $status_id = $req->statusId = RequestStatus::where("key", "Assigned")->first()->id;
                    if ($req->save()) {
                        $action = new RequestAction();
                        $action->requestId = $req->id;
                        $action->actionId = Action::where("key", "Assign")->first()->id;
                        $action->byUserId = null;
                        $action->toUserId = $req->employeeId;
                        $action->comment = null;
                        $action->isAuto = 1;
                        $action->save();
                    }
                }
            }
        }
        
        $this->info('Assigned successfully!');//
    }
}
