<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/AttendanceController";
    }

    /**
     * Display a calendar/timeline view of staff availability
     */
    public function index(Request $request)
    {
        $function_name = 'index';
        try {
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);
            
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = (clone $startDate)->endOfMonth();
            
            $daysInMonth = $startDate->daysInMonth;
            
            $teamMembers = TeamMember::where('status', 1)->get();
            
            $unavailabilities = StaffUnavailability::where('status', 1)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function($q) use ($startDate, $endDate) {
                              $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                          });
                })
                ->get();

            return view('admin.attendance.index', compact(
                'teamMembers', 
                'unavailabilities', 
                'month', 
                'year', 
                'daysInMonth',
                'startDate'
            ));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return back()->with('error', $this->error_message);
        }
    }

    /**
     * Store a new unavailability (Leave/Busy)
     */
    public function store(Request $request)
    {
        $function_name = 'store';
        try {
            $validator = Validator::make($request->all(), [
                'team_member_id' => 'required|exists:team_members,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'type' => 'required|in:1,2,3,4',
                'reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            StaffUnavailability::create([
                'team_member_id' => $request->team_member_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 1
            ]);

            return response()->json(['success' => true, 'message' => 'Attendance updated successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    /**
     * Delete unavailability (Mark as Available again)
     */
    public function destroy($id)
    {
        $function_name = 'destroy';
        try {
            $record = StaffUnavailability::findOrFail($id);
            $record->delete();
            return response()->json(['success' => true, 'message' => 'Staff is now marked as available']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
