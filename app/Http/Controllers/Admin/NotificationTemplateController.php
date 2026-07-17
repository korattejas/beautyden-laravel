<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class NotificationTemplateController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/NotificationTemplateController";
    }

    public function index()
    {
        try {
            return view('admin.notification-templates.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'index');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        try {
            $template = new NotificationTemplate();
            $screenTypes = NotificationTemplate::getScreenTypes();
            return view('admin.notification-templates.create', compact('template', 'screenTypes'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'create');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $template = NotificationTemplate::where('id', decryptId($id))->first();
            if ($template) {
                $screenTypes = NotificationTemplate::getScreenTypes();
                return view('admin.notification-templates.edit', compact('template', 'screenTypes'));
            }
            return redirect()->route('admin.notification-templates.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'edit');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getData(Request $request)
    {
        try {
            if ($request->ajax()) {
                $templates = NotificationTemplate::query();

                return DataTables::of($templates)
                    ->addColumn('status', function ($row) {
                        $badgeClass = $row->status == 1 ? 'bg-success' : 'bg-danger';
                        $badgeText = $row->status == 1 ? 'Active' : 'Inactive';
                        $changeTo = $row->status == 1 ? 0 : 1;
                        
                        return '<span class="badge badge-glow ' . $badgeClass . ' status-change" style="cursor:pointer;" data-id="' . $row->id . '" data-change-status="' . $changeTo . '" title="Click to Change Status">' . $badgeText . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.notification-templates.edit', encryptId($row->id)),
                            'current_status' => $row->status,
                            'delete_id' => $row->id,
                            'hidden_id' => $row->id,
                        ];
                        
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getData');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            NotificationTemplate::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'changeStatus');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function store(Request $request)
    {
        $id = $request->input('edit_value', 0);
        $rules = [
            'event_name' => 'required|string|max:255|unique:notification_templates,event_name,' . $id,
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
        }

        try {
            $data = [
                'event_name' => str_replace(' ', '_', strtolower($request->event_name)),
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
            ];

            if ($id != 0) {
                NotificationTemplate::where('id', $id)->update($data);
                $msg = "Template updated successfully";
            } else {
                NotificationTemplate::create($data);
                $msg = "Template created successfully";
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $template = NotificationTemplate::find($id);
            if ($template) {
                $template->delete();
                return response()->json(['message' => 'Template deleted successfully']);
            }
            return response()->json(['error' => 'Template not found'], 404);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'destroy');
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
