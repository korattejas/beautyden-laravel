<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;


abstract class Controller
{
    public function sendResponse($data, $message, $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function sendError($errorMessages = [], $code = 422)
    {
        return response()->json([
            'code' => $code,
            'status' => false,
            'message' => $errorMessages,
        ]);
    }
}
