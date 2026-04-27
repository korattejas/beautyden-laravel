<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class MembershipPlanController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/MembershipPlanController";
    }

    public function index()
    {
        return view('admin.membership.index');
    }

    public function getData(Request $request)
    {
        $function_name = 'getData';
        try {
            if ($request->ajax()) {
                $plans = MembershipPlan::query();

                return DataTables::of($plans)
                    ->addColumn('status', function ($item) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $item->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($item) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.membership.edit', encryptId($item->id)),
                            'delete_id' => $item->id,
                            'current_status' => $item->status,
                            'hidden_id' => $item->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->editColumn('price', function($item) {
                        return '₹' . number_format($item->price, 2);
                    })
                    ->editColumn('duration_months', function($item) {
                        return $item->duration_months . ' Month' . ($item->duration_months > 1 ? 's' : '');
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        return view('admin.membership.create');
    }

    public function edit($id)
    {
        $plan = MembershipPlan::findOrFail(decryptId($id));
        return view('admin.membership.edit', compact('plan'));
    }

    public function store(Request $request)
    {
        $function_name = 'store';
        try {
            $id = $request->input('edit_value', 0);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'discount_percentage' => 'required|integer|min:0|max:100',
                'duration_months' => 'required|in:1,3,6,12',
                'status' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = $request->only(['name', 'description', 'price', 'discount_percentage', 'duration_months', 'status']);

            if ($id == 0) {
                MembershipPlan::create($data);
                $msg = 'Membership plan created successfully';
            } else {
                MembershipPlan::where('id', $id)->update($data);
                $msg = 'Membership plan updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            MembershipPlan::where('id', $id)->update(['status' => (int)$status]);
            return response()->json(['message' => 'Status updated']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            MembershipPlan::where('id', $id)->delete();
            return response()->json(['message' => 'Plan deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
