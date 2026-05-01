<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\City;
use App\Models\TeamMember;
use App\Models\CustomerReview;
use App\Models\ContactSubmission;
use App\Models\Hiring;
use App\Models\ProductBrand;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\RazorpayTransaction;
use App\Models\StaffUnavailability;
use App\Models\ServiceCombo;

class DashboardController extends Controller
{
    protected $error_message, $exception_error_code, $validator_error_code, $controller_name;

    public function __construct()
    {
        $this->error_message = config('custom.common_error_message');
        $this->exception_error_code = config('custom.exception_error_code');
        $this->validator_error_code = config('custom.validator_error_code');
        $this->controller_name = "Admin/DashboardController";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $function_name = 'index';
        try {
            // Vital stats for initial load (Current Month)
            $startDate = now()->startOfMonth();
            $endDate   = now()->endOfMonth();

            // Minimal data for instant load
            $totalRevenue = Appointment::where('status', 3)->get()->sum(function($app) {
                return (float)($app->services_data['summary']['grand_total'] ?? 0);
            });
            $totalRevenue += UserSubscription::sum('price_paid');

            $activeMemberships = UserSubscription::where('status', 1)->count();
            $totalUsers = User::count();
            $todayAppointments = Appointment::whereDate('appointment_date', date('Y-m-d'))->count();

            // Initial chart data (Completed Apps)
            $chartDataRaw = Appointment::where('status', 3)
                ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->selectRaw('appointment_date, count(*) as total')
                ->groupBy('appointment_date')
                ->get();

            $chartLabels = [];
            $chartData = [];
            $totalDays = $startDate->daysInMonth;
            for ($i = 0; $i < $totalDays; $i++) {
                $curr = $startDate->copy()->addDays($i);
                $chartLabels[] = $curr->format('d M');
                $found = $chartDataRaw->firstWhere('appointment_date', $curr->format('Y-m-d'));
                $chartData[] = $found ? $found->total : 0;
            }

            return view('admin.dashboard.index', [
                'totalRevenue'      => $totalRevenue,
                'activeMemberships' => $activeMemberships,
                'totalUsers'        => $totalUsers,
                'todayAppointments' => $todayAppointments,
                'chartLabels'       => $chartLabels,
                'chartData'         => $chartData,
                'pendingReviews'    => CustomerReview::where('status', 0)->count(),
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }

    private function getTodayHourlyData()
    {
        $todayCompletions = Appointment::where('status', 3)
            ->whereDate('appointment_date', date('Y-m-d'))
            ->get();

        $hourlyData = array_fill(0, 24, 0);
        foreach ($todayCompletions as $app) {
            if ($app->appointment_time) {
                $hour = (int)date('H', strtotime($app->appointment_time));
                $hourlyData[$hour]++;
            }
        }

        return $hourlyData;
    }

    private function getReturnPerformanceData()
    {
        $activeMembers = TeamMember::where('status', 1)->get();
        $allCompletedAppointments = Appointment::where('status', 3)
            ->orderBy('phone')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $returnCredits = [];
        $lastPhone = null;
        $lastBeauticians = null;

        foreach ($allCompletedAppointments as $app) {
            if ($app->phone === $lastPhone && $lastBeauticians) {
                $beauticianIds = explode(',', $lastBeauticians);
                foreach ($beauticianIds as $bid) {
                    $bid = trim($bid);
                    if ($bid) {
                        $returnCredits[$bid] = ($returnCredits[$bid] ?? 0) + 1;
                    }
                }
            }
            $lastPhone = $app->phone;
            $lastBeauticians = $app->assigned_to;
        }

        $labels = [];
        $data = [];
        foreach ($activeMembers as $member) {
            $labels[] = $member->name;
            $data[] = $returnCredits[$member->id] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getAnalyticsData(\Illuminate\Http\Request $request)
    {
        try {
            $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : now()->startOfMonth()->format('Y-m-d');
            $endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : now()->endOfMonth()->format('Y-m-d');

            // 1. Daily Revenue
            $dailyRevenue = [];
            $revenueQuery = Appointment::where('status', 3)
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->get();

            $totalRevenue = 0;
            $revByDate = [];
            foreach ($revenueQuery as $app) {
                $servicesData = $app->services_data;
                $val = (float)($servicesData['summary']['grand_total'] ?? 0);
                $date = $app->appointment_date;
                $revByDate[$date] = ($revByDate[$date] ?? 0) + $val;
                $totalRevenue += $val;
            }
            foreach ($revByDate as $d => $v) {
                $dailyRevenue[] = ['date' => date('d/m/Y', strtotime($d)), 'revenue' => $v];
            }

            // 2. Appointments Status-wise Per Day
            $dailyStats = [];
            $allAppointmentsInRange = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->get();
            
            // Group by date and status
            $grouped = $allAppointmentsInRange->groupBy('appointment_date');
            
            $chartLabels = [];
            $completedSeries = [];
            $pendingSeries = [];
            $assignedSeries = [];
            $rejectedSeries = [];

            // Generate range of dates
            $period = new \DatePeriod(
                new \DateTime($startDate),
                new \DateInterval('P1D'),
                (new \DateTime($endDate))->modify('+1 day')
            );

            foreach ($period as $date) {
                $d = $date->format('Y-m-d');
                $chartLabels[] = $date->format('d M');
                
                $dayAppts = $grouped->get($d, collect());
                $completedSeries[] = $dayAppts->where('status', 3)->count();
                $pendingSeries[]   = $dayAppts->where('status', 1)->count();
                $assignedSeries[]  = $dayAppts->where('status', 2)->count();
                $rejectedSeries[]  = $dayAppts->where('status', 4)->count();
            }

            // 3. Top Staff by Services
            $staffServices = [];
            $staffRevenue = [];
            $allStaff = TeamMember::where('status', 1)->get();
            
            foreach($allStaff as $staff) {
                $id = $staff->id;
                $apptQuery = Appointment::where('status', 3)
                    ->whereBetween('appointment_date', [$startDate, $endDate])
                    ->whereRaw("FIND_IN_SET(?, assigned_to)", [$id])
                    ->get();
                
                $sRev = 0;
                foreach($apptQuery as $a) {
                    $sRev += (float)($a->services_data['summary']['grand_total'] ?? 0);
                }

                if ($apptQuery->count() > 0) {
                    $staffServices[] = [
                        'staff' => $staff->name,
                        'services' => $apptQuery->count(),
                        'revenue' => $sRev
                    ];
                    $staffRevenue[] = [
                        'staff' => $staff->name,
                        'revenue' => $sRev
                    ];
                }
            }

            // Global stats for the range
            $activeMemberships = UserSubscription::where('status', 1)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate . ' 23:59:59')
                ->count();
            $totalUsersInRange = User::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate . ' 23:59:59')
                ->count();
            $totalApptsInRange = $allAppointmentsInRange->count();

            // Sort and take top performers
            usort($staffServices, fn($a, $b) => $b['services'] <=> $a['services']);
            usort($staffRevenue, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

            return response()->json([
                'daily_revenue' => array_slice($dailyRevenue, 0, 5),
                'total_revenue' => $totalRevenue,
                'chart' => [
                    'labels' => $chartLabels,
                    'series' => [
                        ['name' => 'Completed', 'data' => $completedSeries],
                        ['name' => 'Pending', 'data' => $pendingSeries],
                        ['name' => 'Assigned', 'data' => $assignedSeries],
                        ['name' => 'Rejected', 'data' => $rejectedSeries],
                    ]
                ],
                'stats' => [
                    'total_revenue' => number_format($totalRevenue, 0),
                    'active_plans' => $activeMemberships,
                    'total_users' => $totalUsersInRange,
                    'total_appts' => $totalApptsInRange
                ],
                'top_staff_services' => array_slice($staffServices, 0, 5),
                'top_staff_revenue' => array_slice($staffRevenue, 0, 5),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportAnalytics(\Illuminate\Http\Request $request)
    {
        $type = $request->type ?? 'revenue';
        $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : now()->endOfMonth()->format('Y-m-d');

        $fileName = "report_{$type}_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            if ($type === 'revenue') {
                fputcsv($file, ['Date', 'Revenue (INR)']);
                $apps = Appointment::where('status', 3)->whereBetween('appointment_date', [$startDate, $endDate])->get();
                $revByDate = [];
                foreach ($apps as $app) {
                    $revByDate[$app->appointment_date] = ($revByDate[$app->appointment_date] ?? 0) + (float)($app->services_data['summary']['grand_total'] ?? 0);
                }
                foreach ($revByDate as $date => $val) {
                    fputcsv($file, [$date, $val]);
                }
            } elseif ($type === 'appointments') {
                fputcsv($file, ['Date', 'Total Appointments']);
                $data = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
                    ->selectRaw('appointment_date, count(*) as total')
                    ->groupBy('appointment_date')
                    ->get();
                foreach ($data as $row) {
                    fputcsv($file, [$row->appointment_date, $row->total]);
                }
            } else {
                fputcsv($file, ['Staff Name', 'Total Services', 'Total Revenue']);
                $staff = TeamMember::where('status', 1)->get();
                foreach ($staff as $s) {
                    $apps = Appointment::where('status', 3)
                        ->whereBetween('appointment_date', [$startDate, $endDate])
                        ->whereRaw("FIND_IN_SET(?, assigned_to)", [$s->id])
                        ->get();
                    $rev = 0;
                    foreach ($apps as $a) { $rev += (float)($a->services_data['summary']['grand_total'] ?? 0); }
                    if ($apps->count() > 0) {
                        fputcsv($file, [$s->name, $apps->count(), $rev]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getManagementCounts()
    {
        try {
            $counts = [
                'appointments' => Appointment::count(),
                'team' => TeamMember::where('status', 1)->count(),
                'attendance' => \App\Models\StaffUnavailability::count(),
                'users' => User::count(),
                'services' => Service::where('status', 1)->count(),
                'advanced_catalog' => \App\Models\ServiceMaster::where('status', 1)->count(),
                'essentials' => \App\Models\ServiceEssential::where('status', 1)->count(),
                'categories' => \App\Models\ServiceCategory::where('status', 1)->count(),
                'subcategories' => \App\Models\ServiceSubcategory::where('status', 1)->count(),
                'cities' => \App\Models\City::where('status', 1)->count(),
                'pricing_web' => \App\Models\ServiceCityPrice::count(),
                'pricing_app' => \App\Models\ServiceCityMaster::count(),
                'offers' => \App\Models\Offer::where('status', 1)->count(),
                'coupons' => \App\Models\CouponCode::where('status', 1)->count(),
                'memberships' => \App\Models\MembershipPlan::where('status', 1)->count(),
                'combos' => \App\Models\ServiceCombo::where('status', 1)->count(),
                'transactions' => \App\Models\RazorpayTransaction::where('status', 'captured')->count(),
                'inquiries' => \App\Models\ContactSubmission::count(),
                'notifications' => \App\Models\PushNotification::count(),
                'reviews' => \App\Models\CustomerReview::where('status', 1)->count(),
                'portfolio' => \App\Models\Portfolio::where('status', 1)->count(),
                'blogs' => \App\Models\Blog::where('status', 1)->count(),
            ];

            return response()->json($counts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
