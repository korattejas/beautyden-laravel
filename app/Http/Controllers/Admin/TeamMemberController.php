<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\DB;

class TeamMemberController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/TeamMemberController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.team.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $team = TeamMember::find($id);
            if (!$team) {
                return response()->json(['error' => 'Team not found'], 404);
            }
            return response()->json(['data' => $team], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.team.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $team = TeamMember::findOrFail(decryptId($id));
            return view('admin.team.edit', compact('team'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataTeamMembers(Request $request)
    {
        $function_name = 'getDataTeamMembers';
        try {
            if ($request->ajax()) {
                $members = DB::table('team_members')->select('team_members.*');

                if ($request->status !== null && $request->status !== '') {
                    $members->where('team_members.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $members->where('team_members.is_popular', $request->popular);
                }

                if ($request->year_of_experience !== null && $request->year_of_experience !== '') {
                    if ($request->year_of_experience === '10+') {
                        $members->where('team_members.experience_years', '>', 10);
                    } else {
                        $members->where('team_members.experience_years', $request->year_of_experience);
                    }
                }

                if ($request->created_date) {
                    $members->whereDate('team_members.created_at', $request->created_date);
                }

                return DataTables::of($members)
                    ->addColumn('status', function ($members) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $members->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($members) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $members->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($members) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.team.edit', encryptId($members->id)),
                            'delete_id' => $members->id,
                            'current_status' => $members->status,
                            'current_is_popular_priority_status' => $members->is_popular,
                            'hidden_id' => $members->id,
                            'view_id' => $members->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($members) {
                        if ($members->icon && file_exists(public_path('uploads/team-member/' . $members->icon))) {
                            $imageUrl = asset('uploads/team-member/' . $members->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Team Icon" />';
                        }
                        return '';
                    })

                    ->rawColumns(['action', 'icon', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value', 0);

            $validateArray = [
                'name' => 'required|string|max:100',
                'role' => 'nullable|string|max:150',
                'experience_years' => 'nullable|integer|min:0',
                'specialties' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'icon' => $id == 0 ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'certifications' => 'nullable',
                'is_popular' => 'nullable|boolean',
                'status' => 'nullable|boolean',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }


            $photoFilename = null;
            if ($request->hasFile('icon')) {
                $team = TeamMember::where('id', $id)->first();
                if ($team) {
                    $filePath = public_path('uploads/team-member/' . $team->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $photoFilename = ImageUploadHelper::teamMemberImageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $photoFilename = TeamMember::find($id)?->icon;
            }

            $certifications = null;

            if ($request->filled('certifications')) {
                $array = array_map('trim', explode(',', $request->certifications));

                $array = array_filter($array, fn($val) => $val !== '');

                $certifications = json_encode(array_values($array));
            }

            $specialties = null;

            if ($request->filled('specialties')) {
                $array = array_map('trim', explode(',', $request->specialties));

                $array = array_filter($array, fn($val) => $val !== '');

                $specialties = json_encode(array_values($array));
            }

            $data = [
                'name' => $request->name,
                'role' => $request->role,
                'experience_years' => $request->experience_years,
                'specialties' => $specialties,
                'bio' => $request->bio,
                'icon' => $photoFilename,
                'certifications' => $certifications,
                'state' => $request->state,
                'city' => $request->city,
                'taluko' => $request->taluko,
                'village' => $request->village,
                'address' => $request->address,
                'is_popular' => (int) $request->input('is_popular', 0),
                'status' => (int) $request->input('status', 1),
            ];

            if ($id == 0) {
                TeamMember::create($data);
                $msg = 'Team member added successfully';
            } else {
                TeamMember::where('id', $id)->update($data);
                $msg = 'Team member updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logger()->error("store: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            TeamMember::where('id', $id)->update(['status' => (int)$status]);
            return response()->json(['message' => 'Status updated']);
        } catch (\Exception $e) {
            logger()->error("changeStatus: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        try {
            TeamMember::where('id', $id)->update(['is_popular' => (int)$status]);
            return response()->json(['message' => 'Priority status updated']);
        } catch (\Exception $e) {
            logger()->error("changePriorityStatus: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $member = TeamMember::find($id);
            if ($member) {
                $filePath = public_path('uploads/team-member/' . $member->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $member->delete();
            }
            return response()->json(['message' => 'Team member deleted successfully']);
        } catch (\Exception $e) {
            logger()->error("destroy: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
