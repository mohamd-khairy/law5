<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class UnitController extends Controller
{
    const MODEL = "App\Model\Unit";

    use RESTActions;

    protected function respond($status, $data = [])
    {
        return response()->json($data, $status);
    }

}
