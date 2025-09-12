<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesExport;


class ServiceController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.services.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $service = Service::leftJoin('service_categories as sc', 'sc.id', '=', 'services.category_id')
                ->select('services.*', 'sc.name as category_name')
                ->where('services.id', $id)
                ->first();

            if (!$service) {
                return response()->json(['error' => 'Service not found'], 404);
            }
            return response()->json(['data' => $service], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            $categories = ServiceCategory::where('status', 1)->select('name', 'id')->get();
            return view('admin.services.create', compact('categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $service = Service::findOrFail(decryptId($id));
            $categories = ServiceCategory::where('status', 1)->select('name', 'id')->get();

            return view('admin.services.edit', compact('service', 'categories'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataService(Request $request)
    {
        $function_name = 'getDataService';
        try {
            if ($request->ajax()) {
                $services = Service::query()
                    ->leftJoin('service_categories as sc', 'sc.id', '=', 'services.category_id')
                    ->select('services.*', 'sc.name as category_name');

                if ($request->status !== null && $request->status !== '') {
                    $services->where('services.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $services->where('services.is_popular', $request->popular);
                }

                if ($request->created_date) {
                    $services->whereDate('services.created_at', $request->created_date);
                }

                return DataTables::of($services)
                    ->addColumn('status', function ($s) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $s->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('is_popular', function ($s) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => 3,
                            'current_is_popular_priority_status' => $s->is_popular
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($s) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service.edit', encryptId($s->id)),
                            'delete_id' => $s->id,
                            'current_status' => $s->status,
                            'current_is_popular_priority_status' => $s->is_popular,
                            'hidden_id' => $s->id,
                            'view_id' => $s->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status', 'is_popular'])
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
        $request_all = $request->all();
        try {
            $id = $request->input('edit_value', 0);

            $validateArray = [
                'category_id' => 'required|exists:service_categories,id',
                'name'        => 'required',
                // 'price'       => 'required|numeric|min:0',
                // 'discount_price' => 'nullable|numeric|min:0',
                'duration'    => 'required|string|max:50',
                'description' => 'required|string',
                'rating'      => 'nullable|numeric|min:0|max:5',
                'reviews'     => 'nullable|integer|min:0',
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg' : 'image|mimes:jpeg,png,jpg,gif,svg',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $icon = null;
            if ($request->hasFile('icon')) {
                $service = Service::where('id', $id)->first();
                if ($service) {
                    $filePath = public_path('uploads/service/' . $service->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $icon = ImageUploadHelper::serviceimageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $icon = Service::find($id)?->icon;
            }

            $includes = null;

            if ($request->filled('includes')) {
                $array = array_map('trim', explode(',', $request->includes));

                $array = array_filter($array, fn($val) => $val !== '');

                $includes = json_encode(array_values($array));
            }

            $data = [
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'price'       => $request->price,
                'discount_price' => $request->discount_price,
                'duration'    => $request->duration,
                'rating'    => $request->rating,
                'reviews'    => $request->reviews,
                'description' => $request->description,
                'includes'    => $includes,
                'icon'        => $icon,
                'is_popular'  => (int) $request->is_popular,
                'status'      => (int) $request->status,
            ];

            if ($id == 0) {
                Service::create($data);
                return response()->json(['success' => true, 'message' => "Service added successfully"]);
            } else {
                Service::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Service updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Service::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        try {
            Service::where('id', $id)->update(['is_popular' => $status]);
            return response()->json(['message' => trans('admin_string.msg_priority_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }


    public function destroy(int $id)
    {
        try {
            $service = Service::find($id);
            if ($service) {
                $filePath = public_path('uploads/service/' . $service->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $service->delete();
                return response()->json(['message' => trans('admin_string.service_deleted_successfully')]);
            }
            return response()->json(['error' => 'Service not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function exportPdf()
    {
        try {
            $services = Service::leftJoin('service_categories as sc', 'sc.id', '=', 'services.category_id')
                ->select('services.*', 'sc.name as category_name')
                ->get();

            $pdf = Pdf::loadView('admin.services.export-pdf', compact('services'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('services_list.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export PDF');
        }
    }

    public function exportExcel()
    {
        try {
            return Excel::download(new ServicesExport, 'services_list.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export Excel');
        }
    }
}
