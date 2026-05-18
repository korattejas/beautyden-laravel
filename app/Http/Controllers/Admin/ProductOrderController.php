<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class ProductOrderController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ProductOrderController";
    }

    public function index()
    {
        try {
            return view('admin.product-order.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataProductOrder(Request $request)
    {
        try {
            if ($request->ajax()) {
                $orders = DB::table('product_orders')
                    ->leftJoin('users', 'users.id', '=', 'product_orders.user_id')
                    ->select('product_orders.*', 'users.name as user_name', 'users.mobile_number');

                if ($request->status !== null && $request->status !== '') {
                    $orders->where('product_orders.status', $request->status);
                }

                return DataTables::of($orders)
                    ->addColumn('status', function ($order) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $order->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($order) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => '#', // Placeholder or show route
                            'delete_id' => $order->id,
                            'current_status' => $order->status,
                            'hidden_id' => $order->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataProductOrder');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
