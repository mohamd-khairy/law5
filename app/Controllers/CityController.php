<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\City;
use App\Model\Governorate;

class CityController extends Controller
{
    const MODEL = "App\Model\City";

    use RESTActions;
    
    public function get_all_data_for_this_city_by_gov_id($gov_id)
    {
        $data_city = City::where("governorateId" ,$gov_id) ->get();
        return response()->json($data_city, 200);
    }
}
