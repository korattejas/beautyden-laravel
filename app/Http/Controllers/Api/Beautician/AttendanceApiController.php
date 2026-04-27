<?php

namespace App\Http\Controllers\Api\Beautician;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;

class AttendanceApiController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;
    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/Beautician/AttendanceApiController';
        $this->success_status = config('custom.status_code_for_success', 200);
        $this->exception_status = config('custom.status_code_for_exception_error', 500);
        $this->backend_error_status = config('custom.status_code_for_backend_error', 500);
        $this->validation_error_status = config('custom.status_code_for_validation_error', 422);
        $this->common_error_message = config('custom.common_error_message', 'Something went wrong.');
    }

    /**
     * Get the TeamMember for the authenticated user
     */
    private function getTeamMember()
    {
        $user = auth()->guard('user')->user();
        if ($user) {
            $phone = preg_replace('/\D/', '', $user->mobile_number);
            return TeamMember::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '(', '') LIKE '%$phone%'")
                ->orWhere('phone', $user->mobile_number)
                ->first();
        }
        return null;
    }

    /**
     * Get unavailability list for the beautician
     */
    public function index(Request $request): JsonResponse
    {
        $function_name = 'index';
        try {
            $teamMember = $this->getTeamMember();
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $query = StaffUnavailability::where('team_member_id', $teamMember->id)
                ->where('status', 1)
                ->orderBy('start_date', 'desc');

            if ($request->filled('month')) {
                $query->whereMonth('start_date', $request->month);
            }
            if ($request->filled('year')) {
                $query->whereYear('start_date', $request->year);
            }

            $data = $query->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'start_date' => $item->start_date,
                    'end_date' => $item->end_date,
                    'type' => $item->type,
                    'type_text' => $item->type_text,
                    'reason' => $item->reason,
                ];
            });

            return $this->sendResponse($data, 'Attendance data fetched successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Mark leave/unavailability from App
     */
    public function store(Request $request): JsonResponse
    {
        $function_name = 'store';
        try {
            $teamMember = $this->getTeamMember();
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'type' => 'required|in:1,2,3,4',
                'reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $unavailability = StaffUnavailability::create([
                'team_member_id' => $teamMember->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 1
            ]);

            return $this->sendResponse($unavailability, 'Leave marked successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }

    /**
     * Delete/Cancel leave
     */
    public function destroy(Request $request): JsonResponse
    {
        $function_name = 'destroy';
        try {
            $teamMember = $this->getTeamMember();
            if (!$teamMember) {
                return $this->sendError('Beautician profile not found.', 404);
            }

            $record = StaffUnavailability::where('id', $request->id)
                ->where('team_member_id', $teamMember->id)
                ->first();

            if (!$record) {
                return $this->sendError('Record not found.', 404);
            }

            $record->delete();

            return $this->sendResponse(null, 'Leave cancelled successfully.', $this->success_status);
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return $this->sendError($this->common_error_message, $this->exception_status);
        }
    }
}
