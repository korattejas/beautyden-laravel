<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentType;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class PaymentTypeController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/PaymentTypeController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.payment-type.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.payment-type.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $paymentType = PaymentType::where('id', decryptId($id))->first();
            if ($paymentType) {
                return view('admin.payment-type.edit', [
                    'paymentType' => $paymentType
                ]);
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
            $id = $request->input('edit_value');
            $validateArray = [
                'name' => [
                    'required',
                    $id == 0 ? 'unique:payment_types,name' : 'unique:payment_types,name,' . $id . ',id',
                ],
                'icon' => $id == 0 ? 'image|mimes:jpeg,png,jpg,gif,svg,webp' : 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ];

            $validateMessage = [
                'name.required' => 'The payment type name is required.',
                'name.unique' => 'The payment type name has already been taken.',
                'icon.image' => 'The file must be an image.',
                'icon.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg, webp.',
            ];

            $validator = Validator::make($request_all, $validateArray, $validateMessage);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $photoFilename = null;
            if ($request->hasFile('icon')) {
                $paymentType = PaymentType::where('id', $id)->first();
                if ($paymentType) {
                    $filePath = public_path('uploads/payment-type/' . $paymentType->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $photoFilename = ImageUploadHelper::paymentTypeImageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $photoFilename = PaymentType::find($id)?->icon;
            }

            $data = [
                'name' => $request->name,
                'icon' => $photoFilename ?? null,
                'status' => (int) $request->input('status', 1),
            ];

            if ($id == 0) {
                PaymentType::create($data);
                $msg = 'Payment Type added successfully';
            } else {
                PaymentType::where('id', $id)->update($data);
                $msg = 'Payment Type updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logger()->error("store: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataPaymentType(Request $request)
    {
        $function_name = 'getDataPaymentType';
        try {
            if ($request->ajax()) {
                $payment_types = DB::table('payment_types')->select('payment_types.*');
                return DataTables::of($payment_types)
                    ->addColumn('status', function ($payment_types) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $payment_types->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($payment_types) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.payment-type.edit', encryptId($payment_types->id)),
                            'delete_id' => $payment_types->id,
                            'current_status' => $payment_types->status,
                            'hidden_id' => $payment_types->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($payment_types) {
                        if ($payment_types->icon && file_exists(public_path('uploads/payment-type/' . $payment_types->icon))) {
                            $imageUrl = asset('uploads/payment-type/' . $payment_types->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Icon" />';
                        }
                        return '';
                    })
                    ->rawColumns(['action', 'icon', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        $function_name = 'changeStatus';
        try {
            PaymentType::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        $function_name = 'destroy';
        try {
            $paymentType = PaymentType::where('id', $id)->first();
            if ($paymentType) {
                $filePath = public_path('uploads/payment-type/' . $paymentType->icon);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $paymentType->delete();
                return response()->json([
                    'message' => 'Payment type deleted successfully'
                ]);
            } else {
                return response()->json(['error' => 'Payment type not found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
