<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;
use App\Helpers\ImageUploadHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TeamMemberController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/TeamMemberController";
    }

    public function index(Request $request)
    {
        $function_name = 'index';
        try {
            $query = TeamMember::query();

            // Search by Name or Address
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('address', 'like', '%' . $request->search . '%');
                });
            }

            // Other filters
            if ($request->filled('status')) {
                $query->where('status', (int)$request->status);
            }
            if ($request->filled('popular')) {
                $query->where('is_popular', (int)$request->popular);
            }
            if ($request->filled('year_of_experience')) {
                if ($request->year_of_experience === '10+') {
                    $query->where('experience_years', '>', 10);
                } else {
                    $query->where('experience_years', (int)$request->year_of_experience);
                }
            }
            if ($request->filled('created_date')) {
                $query->whereDate('created_at', $request->created_date);
            }

            // Proximity Search
            $searchLat = $searchLng = null;
            if ($request->filled('address_search')) {
                $geo = $this->geocode($request->address_search);
                if ($geo) {
                    $searchLat = (float)$geo['lat'];
                    $searchLng = (float)$geo['lng'];
                    $radius = 2000; // Updated by user to 2000km

                    // Stable Haversine formula for better precision
                    $query->selectRaw("team_members.*, 
                        ( 6371 * 2 * ASIN(SQRT(
                            POWER(SIN((radians(latitude) - radians(?)) / 2), 2) +
                            COS(radians(?)) * COS(radians(latitude)) *
                            POWER(SIN((radians(longitude) - radians(?)) / 2), 2)
                        ))) AS distance", [$searchLat, $searchLat, $searchLng])
                        ->having('distance', '<=', $radius);
                }
            }

            $allMembers = $query->get();
            
            // Summary Stats (Total global counts)
            $active_count = TeamMember::where('status', 1)->count();
            $inactive_count = TeamMember::where('status', 0)->count();

            $allStats = [];
            $selected_month = $request->month;
            $selected_year = $request->year;

            foreach ($allMembers as $member) {
                // Fetch completed appointments for this member with date filtering
                $appQuery = DB::table('appointments')
                    ->whereRaw("FIND_IN_SET(?, assigned_to)", [$member->id])
                    ->where('status', 3); // Completed

                if ($selected_month) {
                    $appQuery->whereMonth('appointment_date', $selected_month);
                }
                if ($selected_year) {
                    $appQuery->whereYear('appointment_date', $selected_year);
                }

                $appointments = $appQuery->get();

                $totalRevenue = 0;
                foreach ($appointments as $app) {
                    $servicesData = json_decode($app->services_data, true);
                    $totalRevenue += (float)($servicesData['summary']['grand_total'] ?? 0);
                }

                $allStats[] = [
                    'member' => $member,
                    'total_appointments' => $appointments->count(),
                    'total_revenue' => $totalRevenue,
                    'status_order' => $member->status == 1 ? 0 : 1, // Active first
                    'distance' => isset($member->distance) ? round($member->distance, 2) : null
                ];
            }

            // Global Sort
            usort($allStats, function ($a, $b) use ($request) {
                // If searching by address, sort primarily by distance
                if ($request->filled('address_search')) {
                    if ($a['distance'] !== $b['distance']) {
                        return $a['distance'] <=> $b['distance'];
                    }
                }

                // 1. Sort by Status (Active first)
                if ($a['status_order'] !== $b['status_order']) {
                    return $a['status_order'] <=> $b['status_order'];
                }
                
                // 2. If both are Active (status 1), sort by Revenue (High to Low)
                if ($a['member']->status == 1) {
                    return $b['total_revenue'] <=> $a['total_revenue'];
                }
                
                // 3. If both are Inactive (status 0), sort by Experience (High to Low)
                $expA = (int)($a['member']->experience_years ?? 0);
                $expB = (int)($b['member']->experience_years ?? 0);
                return $expB <=> $expA;
            });

            // Manual Pagination after global sorting
            $perPage = 50;
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $currentItems = array_slice($allStats, ($currentPage - 1) * $perPage, $perPage);
            
            $members = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                count($allStats),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );

            // Re-map the stats to match the paginated slice
            $stats = $currentItems;

            return view('admin.team.index', compact('stats', 'members', 'active_count', 'inactive_count'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function view($id)
    {
        $function_name = 'view';
        try {
            $team = TeamMember::find($id);
            if (!$team) {
                return response()->json(['error' => 'Team not found'], 404);
            }
            return response()->json(['data' => $team], 200);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function create()
    {
        $function_name = 'create';
        try {
            return view('admin.team.create');
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function edit($id)
    {
        $function_name = 'edit';
        try {
            $team = TeamMember::findOrFail(decryptId($id));
            return view('admin.team.edit', compact('team'));
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getDataTeamMembers(Request $request)
    {
        $function_name = 'getDataTeamMembers';
        try {
            if ($request->ajax()) {
                $members = DB::table('team_members')->select('team_members.*');

                if ($request->status !== null && $request->status !== '') {
                    $members->where('team_members.status', $request->status);
                }

                if ($request->popular !== null && $request->popular !== '') {
                    $members->where('team_members.is_popular', $request->popular);
                }

                if ($request->year_of_experience !== null && $request->year_of_experience !== '') {
                    if ($request->year_of_experience === '10+') {
                        $members->where('team_members.experience_years', '>', 10);
                    } else {
                        $members->where('team_members.experience_years', $request->year_of_experience);
                    }
                }

                if ($request->created_date) {
                    $members->whereDate('team_members.created_at', $request->created_date);
                }

                return DataTables::of($members)
                    ->addColumn('status', function ($members) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => $members->status
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('is_popular', function ($members) {
                        $status_array = [
                            'is_simple_active' => 1,
                            'current_status' => 3,
                            'current_is_popular_priority_status' => $members->is_popular
                        ];
                        return view('admin.render-view.datable-label', [
                            'status_array' => $status_array
                        ])->render();
                    })
                    ->addColumn('action', function ($members) {
                        $action_array = [
                            'is_simple_action' => 1,
                            'edit_route' => route('admin.team.edit', encryptId($members->id)),
                            'delete_id' => $members->id,
                            'current_status' => $members->status,
                            'current_is_popular_priority_status' => $members->is_popular,
                            'hidden_id' => $members->id,
                            'view_id' => $members->id,
                        ];
                        return view('admin.render-view.datable-action', [
                            'action_array' => $action_array
                        ])->render();
                    })
                    ->addColumn('icon', function ($members) {
                        if ($members->icon && file_exists(public_path('uploads/team-member/' . $members->icon))) {
                            $imageUrl = asset('uploads/team-member/' . $members->icon);
                            return '<img src="' . $imageUrl . '" style="max-width:100px;" alt="Team Icon" />';
                        }
                        return '';
                    })

                    ->rawColumns(['action', 'icon', 'status', 'is_popular'])
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
        $request_all = request()->all();
        try {
            $id = $request->input('edit_value', 0);

            $validateArray = [
                'name' => 'required|string|max:100',
                'role' => 'nullable|string|max:150',
                'experience_years' => 'nullable|integer|min:0',
                'specialties' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'icon' => $id == 0 ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'certifications' => 'nullable',
                'is_popular' => 'nullable|boolean',
                'status' => 'nullable|boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ];

            $validator = Validator::make($request_all, $validateArray);
            if ($validator->fails()) {
                logValidationException($this->controller_name, $function_name, $validator);
                return response()->json(['message' => $validator->errors()->first()], $this->validator_error_code);
            }


            $photoFilename = null;
            if ($request->hasFile('icon')) {
                $team = TeamMember::where('id', $id)->first();
                if ($team) {
                    $filePath = public_path('uploads/team-member/' . $team->icon);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $photoFilename = ImageUploadHelper::teamMemberImageUpload($request->file('icon'));
            } elseif ($id != 0) {
                $photoFilename = TeamMember::find($id)?->icon;
            }

            $certifications = null;

            if ($request->filled('certifications')) {
                $array = array_map('trim', explode(',', $request->certifications));

                $array = array_filter($array, fn($val) => $val !== '');

                $certifications = json_encode(array_values($array));
            }

            $specialties = null;

            if ($request->filled('specialties')) {
                $array = array_map('trim', explode(',', $request->specialties));

                $array = array_filter($array, fn($val) => $val !== '');

                $specialties = json_encode(array_values($array));
            }

            $data = [
                'name' => $request->name,
                'role' => $request->role,
                'experience_years' => $request->experience_years,
                'phone' => $request->phone,
                'specialties' => $specialties,
                'bio' => $request->bio,
                'icon' => $photoFilename,
                'certifications' => $certifications,
                'state' => $request->state,
                'city' => $request->city,
                'taluko' => $request->taluko,
                'village' => $request->village,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_popular' => (int) $request->input('is_popular', 0),
                'status' => (int) $request->input('status', 1),
            ];

            if ($id == 0) {
                TeamMember::create($data);
                $msg = 'Team member added successfully';
            } else {
                TeamMember::where('id', $id)->update($data);
                $msg = 'Team member updated successfully';
            }

            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            logger()->error("store: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changeStatus($id, $status)
    {
        try {
            TeamMember::where('id', $id)->update(['status' => (int)$status]);
            return response()->json(['message' => 'Status updated']);
        } catch (\Exception $e) {
            logger()->error("changeStatus: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function changePriorityStatus($id, $status)
    {
        try {
            TeamMember::where('id', $id)->update(['is_popular' => (int)$status]);
            return response()->json(['message' => 'Priority status updated']);
        } catch (\Exception $e) {
            logger()->error("changePriorityStatus: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function destroy($id)
    {
        try {
            $member = TeamMember::find($id);
            if ($member) {
                $filePath = public_path('uploads/team-member/' . $member->icon);

                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $member->delete();
            }
            return response()->json(['message' => 'Team member deleted successfully']);
        } catch (\Exception $e) {
            logger()->error("destroy: " . $e->getMessage());
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    public function getAppointmentsReport(Request $request, $id)
    {
        try {
            $appointments = DB::table('appointments')
                ->whereRaw("FIND_IN_SET(?, assigned_to)", [$id])
                ->where('status', 3) // Completed
                ->orderBy('appointment_date', 'DESC')
                ->orderBy('appointment_time', 'DESC')
                ->paginate(20);

            $data = [];
            foreach ($appointments as $app) {
                $servicesData = json_decode($app->services_data, true);
                $data[] = [
                    'order_number' => $app->order_number,
                    'customer_name' => ($app->first_name ?? '') . ' ' . ($app->last_name ?? ''),
                    'phone' => $app->phone,
                    'date' => $app->appointment_date,
                    'time' => $app->appointment_time,
                    'total' => '₹' . number_format((float)($servicesData['summary']['grand_total'] ?? 0), 0),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => (string) $appointments->links('pagination::bootstrap-5')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function geocode($address)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'BeautyDen-App'
            ])->get("https://nominatim.openstreetmap.org/search", [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => 'in' // Filter results for India to improve precision
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data)) {
                    return [
                        'lat' => $data[0]['lat'],
                        'lng' => $data[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            logger()->error("Geocoding error: " . $e->getMessage());
        }
        return null;
    }
}
