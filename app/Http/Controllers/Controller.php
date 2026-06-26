<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;


abstract class Controller
{
    public function sendResponse($data, $message, $code = 200, $extra = []): JsonResponse
    {
        $response = [
            'code' => $code,
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response);
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
