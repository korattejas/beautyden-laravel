<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;

class CityController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/CityController";
    }

    public function index()
    {
        try {
            return view('admin.cities.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            return view('admin.cities.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $city = City::findOrFail(decryptId($id));
            return view('admin.cities.edit', compact('city'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataCity(Request $request)
    {
        try {
            if ($request->ajax()) {
                $cities = City::query();

                return DataTables::of($cities)
                    ->addColumn('status', function ($c) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $c->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_popular', function ($c) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $c->is_popular
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($c) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.city.edit', encryptId($c->id)),
                            'delete_id' => $c->id,
                            'current_status' => $c->status,
                            'current_is_popular_priority_status' => $c->is_popular,
                            'hidden_id' => $c->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->addColumn('icon', function ($c) {
                        if ($c->icon && file_exists(public_path('uploads/city/' . $c->icon))) {
                            $imageUrl = asset('uploads/city/' . $c->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Category Icon" />';
                        }
                        return '';
                    })
                    ->rawColumns(['action', 'icon', 'status', 'is_popular'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataCity');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);

        $validateArray = [
            'name'   => ['required', $id == 0 ? 'unique:cities,name' : 'unique:cities,name,' . $id],
            'state'  => 'required|string|max:50',
            'area'   => 'nullable|string|max:50',
            'slug'   => $id == 0 ? 'nullable|unique:cities,slug' : 'nullable|unique:cities,slug,' . $id,
            'icon'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];

        $validator = Validator::make($request->all(), $validateArray);
        if ($validator->fails()) {
            logValidationException($this->controller_name, 'store', $validator);
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $icon = null;

            if ($request->hasFile('icon')) {
                $icon = City::where('id', $id)->first();
                if ($icon) {
                    $filePath = public_path('uploads/city/' . $icon->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $icon = ImageUploadHelper::cityimageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $icon = City::find($id)?->icon;
            }

            $data = [
                'name'           => $request->name,
                'state'          => $request->state,
                'area'           => $request->area,
                'slug'           => $request->slug,
                'launch_quarter' => $request->launch_quarter,
                'icon'           => $icon,
                'is_popular'     => (int) $request->is_popular,
                'status'         => (int) $request->status,
            ];

            if ($id == 0) {
                City::create($data);
                return response()->json(['success' => true, 'message' => "City added successfully"]);
            } else {
                City::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "City updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            City::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        try {
            City::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $city = City::find($id);
            if ($city) {
                $filePath = public_path('uploads/city/' . $city->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $city->delete();
                return response()->json(['message' => trans('admin_string.city_deleted_successfully')]);
            }
            return response()->json(['error' => 'City not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
