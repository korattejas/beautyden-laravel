<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/FaqController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            return view('admin.faqs.index');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.faqs.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $faq = Faq::findOrFail(decryptId($id));
            return view('admin.faqs.edit', compact('faq'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataFaqs(Request $request)
    {
        $function_name = 'getDataFaqs';
        try {
            if ($request->ajax()) {
                $faqs = Faq::query()->select('*');

                return DataTables::of($faqs)
                    ->addColumn('status', function ($f) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $f->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($f) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.faqs.edit', encryptId($f->id)),
                            'delete_id' => $f->id,
                            'current_status' => $f->status,
                            'hidden_id' => $f->id,
                        ];
                        return view('admin.render-view.datable-action', compact('action_array'))->render();
                    })
                    ->rawColumns(['action', 'status'])
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
        $request_all = $request->all();
        try {
            $id = $request->input('edit_value', 0);

            $validateArray = [
                'question' => [
                    'required',
                    $id == 0 ? 'unique:faqs,question' : 'unique:faqs,question,' . $id,
                ],
                'answer'   => 'required|string',
                'status'   => 'required|in:0,1',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = [
                'question' => $request->question,
                'answer'   => $request->answer,
                'status'   => (int) $request->status,
            ];

            if ($id == 0) {
                Faq::create($data);
                return response()->json(['success' => true, 'message' => "FAQ added successfully"]);
            } else {
                Faq::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "FAQ updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            Faq::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $faq = Faq::find($id);
            if ($faq) {
                $faq->delete();
                return response()->json(['message' => trans('admin_string.deleted_successfully')]);
            }
            return response()->json(['error' => 'FAQ not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
