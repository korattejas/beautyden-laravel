<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCityPrice;
use App\Models\Service;
use App\Models\City;
use App\Exports\ServiceCityPricesExport;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceCityPriceController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceCityPriceController";
    }

    public function index()
    {
        $function_name = 'index';
        try {
            $cities = City::select('id', 'name')->get();
            return view('admin.service-city-prices.index', compact('cities'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataServiceCityPrice(Request $request)
    {
        $function_name = 'getDataServiceCityPrice';
        try {
            if ($request->ajax()) {
                $prices = DB::table('service_city_prices as scp')
                    ->leftJoin('services as s', 'scp.service_id', '=', 's.id')
                    ->leftJoin('cities as c', 'scp.city_id', '=', 'c.id')
                    ->leftJoin('service_categories as sc', 'sc.id', '=', 's.category_id')
                    ->select(
                        'scp.id',
                        'scp.city_id',
                        'scp.price',
                        'scp.discount_price',
                        'scp.status',
                        'scp.created_at',
                        'scp.updated_at',
                        's.name as service_name',
                        'sc.name as service_category_name',
                        'c.name as city_name'
                    );

                if ($request->status !== null && $request->status !== '') {
                    $prices->where('scp.status', $request->status);
                }

                if ($request->created_date) {
                    $prices->whereDate('scp.created_at', $request->created_date);
                }

                if ($request->city_id) {
                    $prices->where('scp.city_id', $request->city_id);
                }

                return DataTables::of($prices)
                    ->addColumn('status', function ($p) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status'   => $p->status
                        ];
                        return view('admin.render-view.datable-label', compact('status_array'))->render();
                    })
                    ->addColumn('action', function ($p) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.service-city-price.edit', encryptId($p->id)),
                            'delete_id' => $p->id,
                            'current_status' => $p->status,
                            'hidden_id' => $p->id,
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


    public function create()
    {
        try {
            $services = Service::select('id', 'name')->where('status', 1)->get();
            $categories = ServiceCategory::select('id', 'name')->where('status', 1)->get();
            $cities = City::select('id', 'name')->get();
            return view('admin.service-city-prices.create', compact('services', 'categories', 'cities'));
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        try {
            $serviceCityPrice = ServiceCityPrice::findOrFail(decryptId($id));
            $services = Service::select('id', 'name')->where('status', 1)->get();
            $categories = ServiceCategory::select('id', 'name')->where('status', 1)->get();
            $cities = City::select('id', 'name')->get();
            return view('admin.service-city-prices.edit', compact('serviceCityPrice', 'services', 'categories', 'cities'));
        } catch (\Exception $e) {
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
                'city_id'    => 'required|exists:cities,id',
                'category_id' => 'required|exists:service_categories,id',
                'service_id' => [
                    'required',
                    'exists:services,id',
                    Rule::unique('service_city_prices')
                        ->where(function ($query) use ($request) {
                            return $query->where('city_id', $request->city_id)
                                ->where('category_id', $request->category_id);
                        })
                        ->ignore($id),
                ],
                'price'      => 'required|numeric',
                'discount_price' => 'nullable|numeric',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = [
                'city_id'        => $request->city_id,
                'category_id'     => $request->category_id,
                'service_id'     => $request->service_id,
                'price'          => $request->price,
                'discount_price' => $request->discount_price,
            ];

            if ($id == 0) {
                ServiceCityPrice::create($data);
                return response()->json(['success' => true, 'message' => "Service City Price added successfully"]);
            } else {
                ServiceCityPrice::where('id', $id)->update($data);
                return response()->json(['success' => true, 'message' => "Service City Price updated successfully"]);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy(int $id)
    {
        try {
            $price = ServiceCityPrice::find($id);
            if ($price) {
                $price->delete();
                return response()->json(['message' => "Service City Price deleted successfully"]);
            }
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            ServiceCityPrice::where('id', $id)->update(['status' => $status]);
            return response()->json(['message' => trans('admin_string.msg_status_change')]);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function getServicesByCategory(Request $request)
    {
        try {
            $services = Service::where('category_id', $request->category_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }

    public function exportPdf()
    {
        try {
            $prices = DB::table('service_city_prices as scp')
                ->leftJoin('services as s', 's.id', '=', 'scp.service_id')
                ->leftJoin('cities as c', 'c.id', '=', 'scp.city_id')
                ->leftJoin('service_categories as cat', 'cat.id', '=', 'scp.category_id')
                ->select(
                    'scp.id',
                    'c.name as city_name',
                    'cat.name as category_name',
                    's.name as service_name',
                    'scp.price',
                    'scp.discount_price',
                    'scp.status',
                    'scp.created_at'
                )
                ->get();

            $pdf = PDF::loadView('admin.service-city-prices.export-pdf', compact('prices'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('service_city_prices_list.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export PDF');
        }
    }


    public function exportExcel()
    {
        try {
            return Excel::download(new ServiceCityPricesExport, 'service_city_prices_list.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export Excel');
        }
    }
}
