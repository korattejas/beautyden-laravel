<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractSigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class ContractSignedController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ContractSignedController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.contract-signed.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.contract-signed.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $contract = ContractSigned::where('id', decryptId($id))->first();
            if ($contract) {
                return view('admin.contract-signed.edit', [
                    'contract' => $contract
                ]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataContracts(Request $request)
    {
        $function_name = 'getDataContracts';
        try {
            if ($request->ajax()) {
                $contracts = DB::table('contracts_signed')->select('contracts_signed.*');

                if ($request->status !== null && $request->status !== '') {
                    $contracts->where('contracts_signed.status', $request->status);
                }

                if ($request->signed_date) {
                    $contracts->whereDate('contracts_signed.signed_at', $request->signed_date);
                }

                return DataTables::of($contracts)
                    ->addColumn('status', function ($contracts) {
                        switch ($contracts->status) {
                            case '0':
                                return '<span class="badge badge-glow bg-warning text-dark">Pending</span>';
                            case '1':
                                return '<span class="badge badge-glow bg-info text-dark">Signed</span>';
                            default:
                                return '<span class="badge badge-glow bg-secondary">Unknown</span>';
                        }
                    })
                    ->addColumn('action', function ($contracts) {
                        $action_array = [
                            'is_simple_action' => 1,
                            // 'edit_route' => route('admin.contract-signed.edit', encryptId($contracts->id)),
                            'delete_id' => $contracts->id,
                            'current_status' => $contracts->status,
                            'hidden_id' => $contracts->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('signed_pdf', function ($contracts) {
                        if ($contracts->signed_pdf && file_exists(public_path($contracts->signed_pdf))) {
                            $pdfUrl = asset($contracts->signed_pdf);
                            return '<a href="' . $pdfUrl . '" target="_blank">View PDF</a>';
                        }
                        return '-';
                    })
                    ->addColumn('signature_image', function ($contracts) {
                        if ($contracts->signature_image && file_exists(public_path($contracts->signature_image))) {
                            $imgUrl = asset($contracts->signature_image);
                            return '<img src="' . $imgUrl . '" style="max-width:100px;" alt="Signature" />';
                        }
                        return '-';
                    })
                    ->rawColumns(['action', 'status', 'signed_pdf', 'signature_image'])
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
            $id = $request->input('edit_value');
            $validateArray = [
                'provider_name'   => 'required|string|max:100',
                'provider_mobile' => 'required|string|max:20',
                'signed_pdf'      => 'nullable|mimes:pdf|max:2048',
                'signature_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            if ($id == 0) {
                if ($request->hasFile('signed_pdf')) {
                    $pdfName = time() . '_' . $request->signed_pdf->getClientOriginalName();
                    $request->signed_pdf->move(public_path('uploads/contracts'), $pdfName);
                }

                if ($request->hasFile('signature_image')) {
                    $imgName = time() . '_' . $request->signature_image->getClientOriginalName();
                    $request->signature_image->move(public_path('uploads/contracts'), $imgName);
                }

                ContractSigned::create([
                    'provider_name'   => $request->provider_name,
                    'provider_mobile' => $request->provider_mobile,
                    'provider_address' => $request->provider_address,
                    'signed_pdf'      => $pdfName ?? null,
                    'signature_image' => $imgName ?? null,
                    'ip_address'      => $request->ip(),
                    'signed_at'       => $request->signed_at,
                    'status'          => (int) $request->status,
                ]);

                return response()->json(['success' => true, 'message' => "Contract signed entry added successfully"]);
            } else {
                $contract = ContractSigned::where('id', $id)->first();

                if ($request->hasFile('signed_pdf')) {
                    $filePath = public_path('uploads/contracts/' . $contract->signed_pdf);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $pdfName = time() . '_' . $request->signed_pdf->getClientOriginalName();
                    $request->signed_pdf->move(public_path('uploads/contracts'), $pdfName);
                } else {
                    $pdfName = $contract->signed_pdf;
                }

                if ($request->hasFile('signature_image')) {
                    $filePath = public_path('uploads/contracts/' . $contract->signature_image);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $imgName = time() . '_' . $request->signature_image->getClientOriginalName();
                    $request->signature_image->move(public_path('uploads/contracts'), $imgName);
                } else {
                    $imgName = $contract->signature_image;
                }

                ContractSigned::where('id', $id)->update([
                    'provider_name'   => $request->provider_name,
                    'provider_mobile' => $request->provider_mobile,
                    'provider_address' => $request->provider_address,
                    'signed_pdf'      => $pdfName,
                    'signature_image' => $imgName,
                    'ip_address'      => $request->ip(),
                    'signed_at'       => $request->signed_at,
                    'status'          => (int) $request->status,
                ]);

                return response()->json(['success' => true, 'message' => "Contract signed entry updated successfully"]);
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
            ContractSigned::where('id', $id)->update(['status' => $status]);
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
            $contract = ContractSigned::where('id', $id)->first();
            if ($contract) {
                $pdfPath = public_path($contract->signed_pdf);
                if (File::exists($pdfPath)) {
                    File::delete($pdfPath);
                }

                $imgPath = public_path($contract->signature_image);
                if (File::exists($imgPath)) {
                    File::delete($imgPath);
                }

                $contract->delete();
                return response()->json(['message' => trans('admin_string.contract_deleted_successfully')]);
            } else {
                logger()->error("$function_name: No contract found.");
                return response()->json(['error' => 'No contract found.'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
