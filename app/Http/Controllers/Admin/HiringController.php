<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class HiringController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/HiringController";
    }

    public function index()
    {
        try {
            return view('admin.hirings.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $hiring = Hiring::findOrFail($id);

            if (!$hiring) {
                return response()->json(['error' => 'Hiring not found'], 404);
            }

            return response()->json(['data' => $hiring], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            return view('admin.hirings.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $hiring = Hiring::findOrFail(decryptId($id));
            return view('admin.hirings.edit', compact('hiring'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataHirings(Request $request)
    {
        try {
            if ($request->ajax()) {
                $hirings = Hiring::query();

                if ($request->status !== null && $request->status !== '') {
                    $hirings->where('hirings.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $hirings->where('hirings.is_popular', $request->popular);
                }

                if ($request->min_experience !== null && $request->min_experience !== '') {
                    $hirings->where('hirings.min_experience', '>=', $request->min_experience);
                }

                if ($request->max_experience !== null && $request->max_experience !== '') {
                    $hirings->where('hirings.max_experience', '<=', $request->max_experience);
                }

                if ($request->salary_range !== null && $request->salary_range !== '') {
                    $hirings->where('hirings.salary_range', 'like', '%' . $request->salary_range . '%');
                }

                if ($request->created_date) {
                    $hirings->whereDate('hirings.created_at', $request->created_date);
                }

                return DataTables::of($hirings)
                    ->addColumn('status', function ($h) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $h->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_popular', function ($h) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $h->is_popular
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($h) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.hirings.edit', encryptId($h->id)),
                            'delete_id' => $h->id,
                            'current_status' => $h->status,
                            'current_is_popular_priority_status' => $h->is_popular,
                            'hidden_id' => $h->id,
                            'view_id' => $h->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataHirings');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        try {
            $rules = [
                'title'             => 'required|string|max:150',
                'description'       => 'nullable|string',
                'city'              => 'required|string|max:100',
                'min_experience'    => 'nullable|integer|min:0',
                'max_experience'    => 'nullable|integer|min:0',
                'salary_range'      => 'nullable|string|max:150',
                'experience_level'  => 'required|in:1,2,3',
                'hiring_type'       => 'required|in:1,2,3,4',
                'gender_preference' => 'required|in:1,2,3',
                'required_skills'   => 'nullable',
                'is_popular'        => 'nullable|boolean',
                'status'            => 'nullable|boolean',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                logValidationException($this->controller_name, 'store', $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $required_skills = null;

            if ($request->filled('required_skills')) {
                $array = array_map('trim', explode(',', $request->required_skills));

                $array = array_filter($array, fn($val) => $val !== '');

                $required_skills = json_encode(array_values($array));
            }

            $data = [
                'title'             => $request->title,
                'description'       => $request->description,
                'city'              => $request->city,
                'min_experience'    => $request->min_experience,
                'max_experience'    => $request->max_experience,
                'salary_range'      => $request->salary_range,
                'experience_level'  => $request->experience_level,
                'hiring_type'       => $request->hiring_type,
                'gender_preference' => $request->gender_preference,
                'required_skills'   => $required_skills,
                'is_popular'        => (int) $request->is_popular,
                'status'            => (int) $request->status,
            ];

            if ($id == 0) {
                Hiring::create($data);
                return response()->json(['success' => true, 'message' => "Hiring added successfully"]);
            } else {
                Hiring::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Hiring updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Hiring::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changePopularStatus($id, $status)
    {
        try {
            Hiring::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $hiring = Hiring::find($id);
            if ($hiring) {
                $hiring->delete();
                return response()->json(['message' => 'Hiring deleted successfully']);
            }
            return response()->json(['error' => 'Hiring not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
