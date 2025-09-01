<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ContactSubmissionsController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ContactSubmissionsController";
    }
    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.contact-submissions.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $contact = ContactSubmission::leftJoin('services as sc', 'sc.id', '=', 'contact_submissions.service_id')
            ->select('contact_submissions.*', 'sc.name as service_name')
            ->findOrFail($id);

        return response()->json(['data' => $contact], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataContactSubmissions(Request $request)
    {
        $function_name = 'getDataContactSubmissions';
        try {
            if ($request->ajax()) {
                $contact = ContactSubmission::query()
                    ->leftJoin('services as sc', 'sc.id', '=', 'contact_submissions.service_id')
                    ->select('contact_submissions.*', 'sc.name as service_name');
                return DataTables::of($contact)
                    ->addColumn('status', function ($contact) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $contact->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($contact) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'delete_id' => $contact->id,
                            'current_status' => $contact->status,
                            'hidden_id' => $contact->id,
                            'view_id' => $contact->id,

                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->rawColumns(['action', 'status'])
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
            ContactSubmission::where('id', $id)->update(['status' => $status]);
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
            $user = ContactSubmission::where('id', $id)->first();
            if ($user) {
                $user->delete();

                return response()->json([
                    'message' => 'Contact submission deleted successfully'
                ]);
            } else {
                logger()->error("$function_name: Failed to delete the user not found.");
                return response()->json(['error' => 'Failed to delete the user not found..'], 500);
            }
        } catch (\Exception $e) {
            logger()->error("$function_name: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
