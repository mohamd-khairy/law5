<?php namespace App\Http\Controllers;

use App\Model\Role;

class RolesController extends Controller
{

    const MODEL = "App\Model\Role";

    use RESTActions;

    public function roleWithoutApplicant()
    {
        $Allroles = Role::get();
        $response = array();
        foreach ($Allroles as $item) {
            if ($item->key != "Applicant") {
                array_push($response, [
                    "id" => $item->id,
                    "nameAr" => $item->nameAr,
                    "nameEn" => $item->nameEn,
                    "key" => $item->key,
                ]);
            }
        }
        return response()->json($response, 200);
    }
}
