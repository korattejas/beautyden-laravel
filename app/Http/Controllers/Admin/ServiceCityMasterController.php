<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCityMaster;
use App\Models\ServiceMaster;
use App\Models\City;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceCityMasterController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/ServiceCityMasterController";
    }

    public function index()
    {
        $cities = City::select('id', 'name')->get();
        return view('admin.service-city-master.index', compact('cities'));
    }

    public function getData(Request $request)
    {
        try {
            if ($request->ajax()) {
                $prices = DB::table('service_city_masters as scm')
                    ->leftJoin('service_masters as sm', 'scm.service_master_id', '=', 'sm.id')
                    ->leftJoin('cities as c', 'scm.city_id', '=', 'c.id')
                    ->leftJoin('service_categories as sc', 'sc.id', '=', 'scm.category_id')
                    ->leftJoin('service_subcategories as ssc', 'ssc.id', '=', 'scm.sub_category_id')
                    ->select(
                        'scm.id',
                        'scm.price',
                        'scm.discount_price',
                        'scm.app_discount_percentage',
                        'scm.is_available',
                        'scm.status',
                        'sm.name as service_name',
                        'sc.name as category_name',
                        'ssc.name as sub_category_name',
                        'c.name as city_name'
                    );

                if ($request->city_id) {
                    $prices->where('scm.city_id', $request->city_id);
                }

                return DataTables::of($prices)
                    ->addColumn('status', function ($p) {
                        return view('admin.render-view.datable-label', [
                            'status_array' => ['is_simple_active' => 1, 'current_status' => $p->status]
                        ])->render();
                    })
                    ->addColumn('is_available', function ($p) {
                        $badge = $p->is_available ? 'bg-success' : 'bg-danger';
                        $text = $p->is_available ? 'Available' : 'Disabled';
                        return '<span class="badge '.$badge.'">'.$text.'</span>';
                    })
                    ->addColumn('action', function ($p) {
                        return view('admin.render-view.datable-action', [
                            'action_array' => [
                                'is_simple_action' => 1,
                                'edit_route' => route('admin.service-city-master.edit', encryptId($p->id)),
                                'delete_id' => $p->id,
                                'current_status' => $p->status,
                                'hidden_id' => $p->id,
                            ]
                        ])->render();
                    })
                    ->rawColumns(['action', 'status', 'is_available'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'getData');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $categories = ServiceCategory::where('status', 1)->get();
        $cities = City::get();
        return view('admin.service-city-master.create', compact('categories', 'cities'));
    }

    public function edit($id)
    {
        $data = ServiceCityMaster::findOrFail(decryptId($id));
        $services = ServiceMaster::where('category_id', $data->category_id)->get();
        $categories = ServiceCategory::where('status', 1)->get();
        $subcategories = ServiceSubcategory::where('service_category_id', $data->category_id)->get();
        $cities = City::get();
        return view('admin.service-city-master.edit', compact('data', 'services', 'categories', 'subcategories', 'cities'));
    }

    public function store(Request $request)
    {
        try {
            $id = $request->input('edit_value', 0);
            $validator = Validator::make($request->all(), [
                'city_id' => 'required',
                'category_id' => 'required',
                'service_master_id' => 'required',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }

            $data = $request->only(['city_id', 'category_id', 'sub_category_id', 'service_master_id', 'price', 'discount_price', 'app_discount_percentage', 'beautician_commission', 'is_available', 'status']);

            // Set defaults for optional numeric fields
            $data['discount_price']         = $data['discount_price'] ?? 0;
            $data['app_discount_percentage']= $data['app_discount_percentage'] ?? 0;
            $data['beautician_commission']  = $data['beautician_commission'] ?? 0;

            if ($id == 0) {
                ServiceCityMaster::create($data);
                $msg = "App Service City added successfully";
            } else {
                ServiceCityMaster::find($id)->update($data);
                $msg = "App Service City updated successfully";
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, 'store');
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        ServiceCityMaster::find($id)->delete();
        return response()->json(['message' => "Deleted successfully"]);
    }

    /**
     * Return ServiceMaster records filtered by category — used in create/edit AJAX
     */
    public function getServiceMastersByCategory(Request $request)
    {
        try {
            $services = ServiceMaster::where('category_id', $request->category_id)
                ->where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json(['error' => $this->error_message], 500);
        }
    }
}
