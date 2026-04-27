<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RazorpayTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->editColumn('status', function($row) {
                    $class = 'bg-warning';
                    if ($row->status == 'captured' || $row->status == 'success') $class = 'bg-success';
                    if ($row->status == 'failed') $class = 'bg-danger';
                    return '<span class="badge '.$class.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function($row) {
                    return '<button class="btn btn-sm btn-info" onclick="viewDetails('.$row->id.')">View JSON</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $transaction = RazorpayTransaction::findOrFail($id);
        return response()->json($transaction);
    }
}
