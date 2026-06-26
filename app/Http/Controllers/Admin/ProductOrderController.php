<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
                    ->addColumn('order_number', function ($order) {
                        return 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                    })
                    ->addColumn('order_status', function ($order) {
                        $statusBadge = '';
                        switch ($order->order_status) {
                            case 'Pending':
                                $statusBadge = '<span class="badge badge-glow bg-warning text-dark">Pending</span>';
                                break;
                            case 'Processing':
                                $statusBadge = '<span class="badge badge-glow bg-info text-dark">Processing</span>';
                                break;
                            case 'Shipped':
                                $statusBadge = '<span class="badge badge-glow bg-primary">Shipped</span>';
                                break;
                            case 'Delivered':
                                $statusBadge = '<span class="badge badge-glow bg-success">Delivered</span>';
                                break;
                            case 'Cancelled':
                                $statusBadge = '<span class="badge badge-glow bg-danger">Cancelled</span>';
                                break;
                            default:
                                $statusBadge = '<span class="badge badge-glow bg-secondary">'.$order->order_status.'</span>';
                        }

                        $dropdown = '<div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                ' . $statusBadge . '
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow" style="background-color: #ffffff !important; z-index: 9999;">
                                <a class="dropdown-item order-status-change" href="javascript:void(0);" data-id="' . $order->id . '" data-status="Pending">
                                    <i class="bi bi-clock-history me-50 text-warning"></i>
                                    <span>Pending</span>
                                </a>
                                <a class="dropdown-item order-status-change" href="javascript:void(0);" data-id="' . $order->id . '" data-status="Processing">
                                    <i class="bi bi-box-seam me-50 text-info"></i>
                                    <span>Processing</span>
                                </a>
                                <a class="dropdown-item order-status-change" href="javascript:void(0);" data-id="' . $order->id . '" data-status="Shipped">
                                    <i class="bi bi-truck me-50 text-primary"></i>
                                    <span>Shipped</span>
                                </a>
                                <a class="dropdown-item order-status-change" href="javascript:void(0);" data-id="' . $order->id . '" data-status="Delivered">
                                    <i class="bi bi-check2-circle me-50 text-success"></i>
                                    <span>Delivered</span>
                                </a>
                                <a class="dropdown-item order-status-change" href="javascript:void(0);" data-id="' . $order->id . '" data-status="Cancelled">
                                    <i class="bi bi-x-circle me-50 text-danger"></i>
                                    <span>Cancelled</span>
                                </a>
                            </div>
                        </div>';

                        return $dropdown;
                    })
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
                            'delete_id' => $order->id,
                            'current_status' => $order->status,
                            'hidden_id' => $order->id,
                            'view_id' => $order->id,
                        ];
                        $html = view('admin.render-view.datable-action', compact('action_array'))->render();
                        return $html;
                    })
                    ->rawColumns(['action', 'status', 'order_status'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getDataProductOrder');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            $order = ProductOrder::findOrFail($id);
            $order->order_status = $status;
            $order->save();
            return response()->json(['success' => 'Status updated successfully']);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'changeStatus');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        try {
            $order = DB::table('product_orders')
                ->leftJoin('users', 'users.id', '=', 'product_orders.user_id')
                ->select('product_orders.*', 'users.name as user_name', 'users.mobile_number', 'users.email')
                ->where('product_orders.id', $id)
                ->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $orderData = is_string($order->order_data) ? json_decode($order->order_data, true) : $order->order_data;

            return response()->json([
                'data' => [
                    'id'              => $order->id,
                    'order_number'    => 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'user_name'       => $order->user_name,
                    'phone'           => $order->mobile_number,
                    'email'           => $order->email,
                    'address'         => $order->address,
                    'total_amount'    => $order->total_amount,
                    'payment_status'  => $order->payment_status,
                    'order_status'    => $order->order_status,
                    'created_at'      => $order->created_at,
                    'order_data'      => $orderData,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadPdf($id)
    {
        try {
            $order = ProductOrder::findOrFail($id);
            $user = DB::table('users')->where('id', $order->user_id)->first();
            
            $teamMember = null;
            if ($user) {
                $phone = preg_replace('/\D/', '', $user->mobile_number);
                $teamMember = \App\Models\TeamMember::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '(', '') LIKE '%$phone%'")
                    ->orWhere('phone', $user->mobile_number)
                    ->first();
            }

            $orderNumber = 'BDPROD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

            $pdf = Pdf::loadView('admin.product_orders.pdf', compact('order', 'orderNumber', 'teamMember', 'user'))
                      ->setPaper('a4', 'portrait');

            $fileName = 'Invoice_' . $orderNumber . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'downloadPdf');
            return redirect()->back()->with('error', $this->error_message);
        }
    }
}
