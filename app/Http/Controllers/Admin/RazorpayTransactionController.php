<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RazorpayTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Razorpay\Api\Api;

class RazorpayTransactionController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/RazorpayTransactionController";
    }

    public function index()
    {
        return view('admin.razorpay.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = RazorpayTransaction::with('user')->orderBy('id', 'desc');
            
            return DataTables::of($data)
                ->addColumn('user_name', function($row) {
                    $name = $row->user ? $row->user->name : 'N/A';
                    $phone = $row->user ? $row->user->mobile_number : '';
                    if ($phone) {
                        return '<div>'.$name.'</div><small class="text-muted">'.$phone.'</small>';
                    }
                    return $name;
                })
                ->addColumn('appointment_link', function($row) {
                    $meta = $row->meta_data;
                    
                    if (is_string($meta)) {
                        $meta = json_decode($meta, true);
                    }

                    $appointmentId = null;
                    if (is_array($meta) && isset($meta['appointment_id'])) {
                        $appointmentId = $meta['appointment_id'];
                    }
                    if ($appointmentId) {
                        $appointment = \App\Models\Appointment::find($appointmentId);
                        if ($appointment) {
                            $orderNo = $appointment->order_number;
                            $encodedOrder = urlencode($orderNo);
                            return '<a href="'.url('admin/appointments?order_no='.$encodedOrder).'" class="fw-bold" target="_blank">'.$orderNo.'</a>';
                        }
                    }
                    return '-';
                })
                ->editColumn('status', function($row) {
                    $class = 'bg-warning';
                    if ($row->status == 'captured' || $row->status == 'success') $class = 'bg-success';
                    if ($row->status == 'failed') $class = 'bg-danger';
                    if ($row->status == 'refunded') $class = 'bg-secondary';
                    return '<span class="badge '.$class.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function($row) {
                    $buttons = '<div class="d-flex gap-1">';
                    $buttons .= '<button class="btn btn-sm btn-info text-nowrap" onclick="viewDetails('.$row->id.')">View JSON</button>';
                    
                    if (in_array(strtolower($row->status), ['captured', 'success'])) {
                        $buttons .= '<button class="btn btn-sm btn-danger text-nowrap" onclick="refundTransaction('.$row->id.', '.$row->amount.')">Refund</button>';
                    }
                    $buttons .= '</div>';
                    
                    return $buttons;
                })
                ->rawColumns(['user_name', 'appointment_link', 'status', 'action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $transaction = RazorpayTransaction::findOrFail($id);
        
        // Handle double encoded json for old records
        if (is_string($transaction->meta_data)) {
            $transaction->meta_data = json_decode($transaction->meta_data, true);
        }
        if (is_string($transaction->payment_details)) {
            $transaction->payment_details = json_decode($transaction->payment_details, true);
        }

        return response()->json($transaction);
    }

    public function refund(Request $request, $id)
    {
        try {
            $transaction = RazorpayTransaction::findOrFail($id);

            if (!in_array(strtolower($transaction->status), ['captured', 'success'])) {
                return response()->json(['success' => false, 'message' => 'Transaction cannot be refunded.']);
            }

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            
            $refundData = [
                "notes" => [
                    "reason" => "Refund requested by admin"
                ]
            ];

            // If a specific amount is requested for partial refund
            if ($request->filled('amount') && $request->amount > 0) {
                // Razorpay expects amount in paise
                $refundData['amount'] = $request->amount * 100;
            }

            // Initiate refund
            $refund = $api->payment->fetch($transaction->razorpay_payment_id)->refund($refundData);

            if ($refund && $refund->status == 'processed') {
                $transaction->status = 'refunded';
                $transaction->save();

                return response()->json(['success' => true, 'message' => 'Refund processed successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to process refund from Razorpay.']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
