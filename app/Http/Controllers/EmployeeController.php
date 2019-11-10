<?php

namespace App\Http\Controllers;

use App\Model\Setting;

use App\Model\Role;
use App\Model\User;
use App\Model\Employee;
use App\Model\EmployeeSector;
use App\Model\Sector;
use App\Model\Chamber;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;
use App\Mail\ConfirmationMail;

class EmployeeController extends Controller
{


  const MODEL = "App\Model\Employee";

  use RESTActions;

  public function createEmployee(Request $request)
  {

    DB::beginTransaction();

    $this->validate($request, [
      'name' => 'required|max:255',
      'email' => 'required|email|max:255|unique:users',
      'mobile' => 'required|max:255',
      'roleKey' => 'required',
      'sectors' => 'required_if:roleKey,IDAEmployee',
      'chamberId' => 'required_if:roleKey,ChamberEmployee',
      'isActive' => 'required',
    ]);

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->telephone = $request->mobile;
    $user->roleId = Role::where("key", $request->roleKey)->first()->id;
    $user->isActive = $request->isActive;
    $password = $this->generate_password(8);
    $user->password = Hash::make($password);
    $user->emailConfirmationCode = $this->generate_password(6);
    $user->token = User::generateApiToken();
    $user->sectorId = null;
    $user->save();
    app('App\Http\Controllers\LogController')->Logging_create("users",$user);

    $employee = new Employee();
    $employee->mobile = $user->telephone;
    $employee->userId = $user->id;
    if ($user->save() && $employee->save()) {
      if ($request->roleKey == "IDAEmployee") {
        if (!empty($request->sectors)) {
          foreach ($request->sectors as $sect) {
            $empSector = new EmployeeSector();
            $empSector->employeeId = $employee->id;
            $empSector->sectorId = $sect['id'];
            $empSector->save();
          }
        } else {
          return response()->json("sectors must be not empty !!", 400);
        }
      } elseif ($request->roleKey  == "ChamberEmployee") {
        if (!empty($request->chamberId)) {
          $employee = Employee::find($employee->id);
          $employee->chamberId = $request->chamberId;
          $employee->save();
        } else {
          return response()->json("ChamberId must be not empty !!", 400);
        }
      }
    } else {
      return response()->json("employee not Updated !", 400);
    }

    Mail::to($user->email)->send(new AccountCreated($user->email, $password,$user->name));

    if(!$user->isEmailVerified){ // false

        Mail::to($user->email)->send(new ConfirmationMail($user)); 
    }

    if(count(Mail::failures()) > 0){

        DB::rollback();

        return response()->json(__('auth.mailNotSend'), 400);
    }

    DB::commit();

    return $this->respond(Response::HTTP_OK, $user->id);
  }

  public function updateEmployee(Request $request, $id)
  {
    $this->validate($request, [
      'name' => 'required|max:255',
      'mobile' => 'required|max:255',
      'roleKey' => 'required',
      'sectors' => 'required_if:roleKey,IDAEmployee',
      'chamberId' => 'required_if:roleKey,ChamberEmployee',
      'isActive' => 'required',
    ]);
    $employee = Employee::where("id", $id)->first();
    $user = User::find($employee->userId);
    $user->name = $request->name;
    $user->telephone = $request->mobile;
    $user->roleId = Role::where("key", $request->roleKey)->first()->id;
    $user->isActive = $request->isActive;
    $sectorId = NULL;
    $old=$user->getOriginal();
    if ($user->save()) {
      app('App\Http\Controllers\LogController')->Logging_update("users",$user,$old);

      if ($request->roleKey == "IDAEmployee") {
        if (!empty($request->sectors)) {
          $sector_in_database = EmployeeSector::where("employeeId", $id)->delete();
          // return $request->sectors;
          foreach ($request->sectors as $sect) {
            $empSector = new EmployeeSector();
            $empSector->employeeId = $id;
            $empSector->sectorId = $sect['id'];
            $empSector->save();
          }
        } else {
          return response()->json("sectors must be not empty !!", 400);
        }
      } elseif ($request->roleKey == "ChamberEmployee") {
        if (!empty($request->chamberId)) {
          $employee->chamberId = $request->chamberId;
          $employee->save();
        } else {
          return response()->json("ChamberId must be not empty !!", 400);
        }
      }
      $employee->mobile = $request->mobile;
      $employee->save();
      return response()->json("employee Updated Successfully", 200);
    } else {
      return response()->json("employee not Updated !", 400);
    }
  }

  public function getEmployee($id, $languageId)
  {
    $employee = [];

    if ($languageId == 'ar') {
      $employee = DB::table('users')
        ->join('roles', 'users.roleId', '=', 'roles.id')
        ->join('employees', 'users.id', '=', 'employees.userId')
        ->select('users.id', 'users.name', 'users.email', 'users.roleId', 'users.isActive', 'roles.roleNameAr', 'employees.mobile', 'employees.chamberId')
        ->where('users.id', $id)->where('users.isDeleted', 0)
        ->first();
      if (is_null($employee)) {
        return $this->respond(Response::HTTP_NOT_FOUND);
      }
    } else {
      $employee = DB::table('users')
        ->join('roles', 'users.roleId', '=', 'roles.id')
        ->join('employees', 'users.id', '=', 'employees.userId')
        ->select('users.id', 'users.name', 'users.email', 'users.roleId', 'users.isActive', 'roles.roleNameEn', 'employees.mobile', 'employees.chamberId')
        ->where('users.id', $id)->where('users.isDeleted', 0)
        ->first();
      if (is_null($employee)) {
        return $this->respond(Response::HTTP_NOT_FOUND);
      }
    }
  }

  public function getEmployeeById($id)
  {
    $emp = Employee::where("id", $id)->first();
    if (empty($emp)) {
      return response(" employee is null", 400);
    }

    $employees = DB::table('users')
      ->join('roles', 'users.roleId', '=', 'roles.id')
      ->join('employees', 'users.id', '=', 'employees.userId')
      ->where('employees.userId', $emp->userId)->get();
    if (is_null($employees)) {
      return response(" employee is null", 400);
    }
    foreach ($employees as $employee) {
      $sectorId = EmployeeSector::where("employeeId", $id)->get();

      $responseSector = array();
      foreach ($sectorId as $item) {
        array_push($responseSector, $sectors = Sector::find($item->sectorId));
      }
      $response = [
        "id" => $id,
        "name" => $employee->name,
        "email" => $employee->email,
        "roleId" => $employee->roleId,
        "isActive" => $employee->isActive,
        "roleKey" => $employee->key,
        "roleNameAr" => $employee->nameAr,
        "roleNameEn" => $employee->nameEn,
        "mobile" => $employee->mobile,
        "chamberId" => $employee->chamberId,
        "chamberNameAr" => ($employee->chamberId) ? Chamber::find($employee->chamberId)->nameAr : null,
        "chamberNameEn" => ($employee->chamberId) ? Chamber::find($employee->chamberId)->nameEn : null,
        "sectors" =>  $responseSector

      ];
    }

    return $this->respond(Response::HTTP_OK, $response);
  }

  public function getEmployeeByRoleId($roleId)
  {

    $employee = DB::table('users')
      ->join('employees', 'users.id', '=', 'employees.userId')
      ->where('users.roleId', $roleId)->get();
    if (is_null($employee)) {
      return $this->respond(Response::HTTP_NOT_FOUND);
    }
    $response = array();
    foreach ($employee as $emp) {
      $employee_sectors = DB::table('employee_sectors')
        ->join('sectors', 'employee_sectors.sectorId', '=', 'sectors.id')
        ->select('sectors.id', "sectors.nameAr", "sectors.nameEn")
        ->where("employee_sectors.employeeId", $emp->id)
        ->get();
      array_push($response, [
        "id" => $emp->id,
        "name" => $emp->name,
        "sectors" => $employee_sectors
      ]);
    }
    if (is_null($response)) {
      return $this->respond(Response::HTTP_NOT_FOUND);
    }
    return response($response, 200);
  }

  public function employeesList()
  {
    $employees = DB::table('users')
      ->join('roles', 'users.roleId', '=', 'roles.id')
      ->join('employees', 'users.id', '=', 'employees.userId')
      ->select('employees.id', 'users.name', 'users.email', 'employees.mobile', 'roles.nameAr as roleNameAr')
      ->where('roles.key', "!=", "Applicant")->get();
    if (is_null($employees)) {
      return $this->respond(Response::HTTP_NOT_FOUND);
    }
    return $this->respond(Response::HTTP_OK, $employees);
  }

  public function getEmployee_by_roleId_or_not()
  {

    $employee = DB::table('users')
      ->join('roles', 'users.roleId', '=', 'roles.id')
      ->join('employees', 'users.id', '=', 'employees.userId');
    if (isset($_GET['roleId']) && intval($_GET['roleId'])) {
      $employee = $employee->where('users.roleId', $_GET['roleId'])->get();
    } else {
      $employee = $employee->get();
    }
    if (is_null($employee)) {
      return $this->respond("employee is null", 400);
    }

    $response = array();
    foreach ($employee as $emp) {
      $employee_sectors = DB::table('employee_sectors')
        ->join('sectors', 'employee_sectors.sectorId', '=', 'sectors.id')
        ->select('sectors.id', "sectors.nameAr", "sectors.nameEn")
        ->where("employee_sectors.employeeId", $emp->id)
        ->get();
      array_push($response, [
        "id" => $emp->id,
        "name" => $emp->name,
        "email" => $emp->email,
        "roleId" => $emp->roleId,
        "isActive" => $emp->isActive,
        "roleKey" => $emp->key,
        "roleNameAr" => $emp->nameAr,
        "roleNameEn" => $emp->nameEn,
        "mobile" => $emp->mobile,
        "chamberId" => $emp->chamberId,
        "chamberNameAr" => ($emp->chamberId) ? Chamber::find($emp->chamberId)->nameAr : null,
        "chamberNameEn" => ($emp->chamberId) ? Chamber::find($emp->chamberId)->nameEn : null,
        "sectors" => $employee_sectors
      ]);
    }
    return response($response, 200);
  }

  protected function generate_password($length = 8)
  {
    $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
      '0123456789';

    $str = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++)
      $str .= $chars[random_int(0, $max)];

    return $str;
  }

  protected function respond($status, $data = [])
  {
    return response()->json($data, $status);
  }
}
