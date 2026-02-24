<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class TeamMemberController extends Controller
{
    protected mixed $success_status, $exception_status, $backend_error_status, $validation_error_status, $common_error_message;

    protected string $controller_name;

    public function __construct()
    {
        $this->controller_name = 'API/TeamMemberController';
        $this->success_status = config('custom.status_code_for_success');
        $this->exception_status = config('custom.status_code_for_exception_error');
        $this->backend_error_status = config('custom.status_code_for_backend_error');
        $this->validation_error_status = config('custom.status_code_for_validation_error');
        $this->common_error_message = config('custom.common_error_message');
    }

    public function getTeamMembers(): JsonResponse
    {
        $function_name = 'getTeamMembers';

        try {
            $teamMembers = DB::table('team_members as t')
                ->select(
                    't.id',
                    't.name',
                    't.role',
                    't.experience_years',
                    't.bio',
                    't.address',
                    DB::raw('CONCAT("' . asset('uploads/team-member') . '/", t.icon) AS photo'),
                    't.is_popular',
                    't.specialties',
                    't.certifications'
                )
                ->where('t.status', 1)
                ->orderByDesc('t.is_popular')
                ->get()
                ->map(function ($member) {
                    $member->specialties = $member->specialties ? json_decode($member->specialties, true) : [];
                    $member->certifications = $member->certifications ? json_decode($member->certifications, true) : [];
                    return $member;
                });

            if ($teamMembers->isEmpty()) {
                return $this->sendError('No team member found.', $this->backend_error_status);
            }

            return $this->sendResponse(
                $teamMembers,
                'Team members retrieved successfully',
                $this->success_status
            );
        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }

    public function beauticianInquiryFormSubmit(Request $request): JsonResponse
    {
        $function_name = 'beauticianInquiryFormSubmit';

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'phone'      => 'required|string|max:20',
                'experience_years' => 'required',
                'address'    => 'nullable|string',
                'bio'    => 'nullable|string',
            ]);

            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return $this->sendError($validator->errors()->first(), $this->validation_error_status);
            }

            $contact = TeamMember::create([
                'name' => $request->name,
                'phone'      => $request->phone,
                'experience_years' => $request->experience_years,
                'address'    => $request->address,
                'bio'    => $request->bio,
                'status' => 0,
            ]);

            return $this->sendResponse(
                $contact,
                'Beautician inquiry form submitted successfully.',
                $this->success_status
            );

        } catch (Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);

            return $this->sendError(
                $this->common_error_message,
                $this->exception_status
            );
        }
    }
}
