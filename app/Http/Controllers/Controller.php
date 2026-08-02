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

    public function formatDuration($duration)
    {
        if (empty($duration)) {
            return $duration;
        }

        if (stripos((string)$duration, 'min') !== false) {
            if (preg_match('/(\d+)/', (string)$duration, $matches)) {
                $totalMinutes = (int) $matches[1];
                if ($totalMinutes >= 60) {
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    if ($minutes > 0) {
                        return $hours . ' hrs ' . $minutes . ' mins';
                    } else {
                        return $hours . ' hrs';
                    }
                } else {
                    return $totalMinutes . ' mins';
                }
            }
        }
        return $duration;
    }
}
