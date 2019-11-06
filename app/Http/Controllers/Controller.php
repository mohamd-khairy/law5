<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
  /**
   * @OA\Info(
   *   title="Law5 API",
   *   version="1.0",
   *   @OA\Contact(
   *     email="orchtech@orchtech.com",
   *     name="Support Team"
   *   )
   * )
   *  @OA\SecurityScheme(
   *      securityScheme="bearerAuth",
   *      in="header",
   *      type="http",
   *      name="Authorization",
   *      scheme="bearer",
   *      bearerFormat="JWT",
   * )
   */
}
