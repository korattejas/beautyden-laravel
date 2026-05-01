<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\ServiceCombo;
use App\Models\ServiceComboItem;
use App\Models\ServiceMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ServiceComboController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceComboController";
    }

    public function index()
    {
        return view('admin.combo.index');
    }

    public function getData(Request $request)
    {
        $function_name = 'getData';
        try {
            if ($request->ajax()) {
                $combos = ServiceCombo::query();
                return DataTables::of($combos)
                    ->addColumn('status', function ($item) {
                        return view('admin.render-view.datable-label', [
                            'status_array' => ['is_simple_active' => 1, 'current_status' => $item->status]
                        ])->render();
                    })
                    ->addColumn('action', function ($item) {
                        return view('admin.render-view.datable-action', [
                            'action_array' => [
                                'is_simple_action' => 1,
                                'edit_route' => route('admin.combo.edit', encryptId($item->id)),
                                'delete_id' => $item->id,
                                'current_status' => $item->status,
                                'hidden_id' => $item->id,
                            ]
                        ])->render();
                    })
                    ->addColumn('image', function ($item) {
                        if ($item->image && file_exists(public_path('uploads/combos/' . $item->image))) {
                            return '<img src="' . asset('uploads/combos/' . $item->image) . '" style="max-width:80px;" />';
                        }
                        return 'N/A';
                    })
                    ->editColumn('min_price', function($item) {
                        return '₹' . number_format($item->min_price, 2);
                    })
                    ->rawColumns(['action', 'status', 'image'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $services = ServiceMaster::where('status', 1)->get();
        return view('admin.combo.create', compact('services'));
    }

    public function edit($id)
    {
        $combo = ServiceCombo::with('items')->findOrFail(decryptId($id));
        $services = ServiceMaster::where('status', 1)->get();
        
        $selectedServiceIds = $combo->items->pluck('service_master_id')->toArray();
        $defaultServiceIds = $combo->items->where('is_default', 1)->pluck('service_master_id')->toArray();
        
        return view('admin.combo.edit', compact('combo', 'services', 'selectedServiceIds', 'defaultServiceIds'));
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        try {
            $id = $request->input('edit_value', 0);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'services' => 'required|array|min:1',
                'min_price' => 'required|numeric|min:0',
                'icon' => $id == 0 ? 'required|image' : 'nullable|image',
            ], [
                'icon.required' => 'The image field is required.'
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $imageName = null;
            $uploadedFile = $request->file('icon') ?? $request->file('image');

            if ($uploadedFile) {
                // Delete old image if updating
                if ($id != 0) {
                    $old = ServiceCombo::find($id);
                    if ($old && $old->image && File::exists(public_path('uploads/combos/' . $old->image))) {
                        File::delete(public_path('uploads/combos/' . $old->image));
                    }
                }
                $imageName = ImageUploadHelper::comboImageUpload($uploadedFile);
            } elseif ($id != 0) {
                $imageName = ServiceCombo::find($id)->image;
            }

            $comboData = [
                'name' => $request->name,
                'description' => $request->description,
                'min_price' => $request->min_price,
                'image' => $imageName,
                'status' => $request->status ?? 1,
            ];

            if ($id == 0) {
                $combo = ServiceCombo::create($comboData);
            } else {
                $combo = ServiceCombo::find($id);
                $combo->update($comboData);
                ServiceComboItem::where('combo_id', $combo->id)->delete();
            }

            // Sync Services
            $defaultServices = $request->input('default_services', []);
            foreach ($request->services as $serviceId) {
                ServiceComboItem::create([
                    'combo_id' => $combo->id,
                    'service_master_id' => $serviceId,
                    'is_default' => in_array($serviceId, $defaultServices) ? 1 : 0
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Combo saved successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $combo = ServiceCombo::find($id);
            if ($combo && $combo->image && File::exists(public_path('uploads/combos/' . $combo->image))) {
                File::delete(public_path('uploads/combos/' . $combo->image));
            }
            $combo->delete();
            return response()->json(['message' => 'Combo deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
