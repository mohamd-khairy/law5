<?php

namespace App\Exceptions;

use Exception;

class JsonHandler extends Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $rendered = parent::render($request, $exception);

        $jsonResponseArray['error']['code'] = $rendered->getStatusCode();
        $jsonResponseArray['error']['message'] = $exception->getMessage();
        $jsonResponseArray['error']['exception'] = get_class($exception);
        if (env('APP_DEBUG')) {
            $jsonResponseArray['error']['debug'] = $exception->getTrace();
        }

        return response()->json(
            $jsonResponseArray,
            $rendered->getStatusCode()
        );
    }
}
