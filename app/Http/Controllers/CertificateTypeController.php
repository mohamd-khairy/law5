<?php namespace App\Http\Controllers;
use App\Model\CertificateType;
class CertificateTypeController extends Controller {

    const MODEL = "App\Model\CertificateType";

    use RESTActions;

    public function certificateType()
    {
        $CertificateType = CertificateType::get();
        $response = array();
        foreach ($CertificateType as $model) {
            array_push($response, [
                "id" => $model->id,
                "nameEn" => $model->name,
                "nameAr" => $model->nameAr
            ]);
        }
        return response()->json( $response ,200);
    }
}
