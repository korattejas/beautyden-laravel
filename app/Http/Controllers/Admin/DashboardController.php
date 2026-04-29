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
            // Existing stats
            $totalAppointments      = Appointment::count();
            $totalAppointmentsPending   = Appointment::where('status', 1)->count(); 
            $totalAppointmentsAssigned  = Appointment::where('status', 2)->count(); 
            $totalAppointmentsCompleted = Appointment::where('status', 3)->count();
            $totalAppointmentsRejected  = Appointment::where('status', 4)->count();
            $totalContacts = ContactSubmission::count();
            $totalServiceCategory = ServiceCategory::where('status', 1)->count();
            $totalServices = Service::where('status', 1)->count();
            $totalBlogs = Blog::where('status', 1)->count();
            $totalBlogCategory = BlogCategory::where('status', 1)->count();
            $totalTeamMember = TeamMember::where('status', 1)->count();
            $totalHirings = Hiring::where('status', 1)->count();
            $totalCustomerReviews = CustomerReview::where('status', 1)->count();
            
            // New Module Stats
            $pendingReviews = CustomerReview::where('status', 0)->count();
            $activeMemberships = UserSubscription::where('status', 1)
                ->where('end_date', '>=', now())
                ->count();
            $totalRazorpayRevenue = RazorpayTransaction::where('status', 'captured')->sum('amount');
            $newUsersToday = User::whereDate('created_at', now())->count();
            $totalUsers = User::count();
            $activeCombos = ServiceCombo::where('status', 1)->count();

            // Staff on leave today
            $onLeaveToday = StaffUnavailability::with('beautician')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->where('status', 1) // 1 = Active
                ->get();

            $totalCity = City::where('status', 1)->count();
            $totalProductBrand = ProductBrand::where('status', 1)->count();

            // Total Revenue (Hybrid: Services + Memberships)
            $totalRevenue = 0;
            $completedAppointments = Appointment::where('status', 3)->get();
            foreach ($completedAppointments as $app) {
                $servicesData = $app->services_data;
                $totalRevenue += (float)($servicesData['summary']['grand_total'] ?? 0);
            }
            $totalRevenue += UserSubscription::sum('price_paid');

            $todayAppointments = Appointment::whereDate('appointment_date', date('Y-m-d'))->count();

            // Charts
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $totalDays = $startDate->daysInMonth;

            $completedAppointmentData = Appointment::where('status', 3)
                ->whereBetween('appointment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->selectRaw('appointment_date, count(*) as total')
                ->groupBy('appointment_date')
                ->orderBy('appointment_date', 'ASC')
                ->get();

            $chartLabels = [];
            $chartData = [];
            
            for ($i = 0; $i < $totalDays; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                $dateString = $currentDate->format('Y-m-d');
                $chartLabels[] = $currentDate->format('d M');
                $found = $completedAppointmentData->firstWhere('appointment_date', $dateString);
                $chartData[] = $found ? $found->total : 0;
            }

            return view('admin.dashboard.index', [
                'totalAppointments'      => $totalAppointments,
                'totalAppointmentsPending' => $totalAppointmentsPending,
                'totalAppointmentsAssigned' => $totalAppointmentsAssigned,
                'totalAppointmentsCompleted' => $totalAppointmentsCompleted,
                'totalAppointmentsRejected' => $totalAppointmentsRejected,
                'totalContacts'          => $totalContacts,
                'totalServiceCategory'   => $totalServiceCategory,
                'totalServices'          => $totalServices,
                'totalBlogs'             => $totalBlogs,
                'totalBlogCategory'      => $totalBlogCategory,
                'totalTeamMember'        => $totalTeamMember,
                'totalHirings'           => $totalHirings,
                'totalCustomerReviews'   => $totalCustomerReviews,
                'totalCity'              => $totalCity,
                'totalProductBrand'      => $totalProductBrand,
                'totalRevenue'           => $totalRevenue,
                'todayAppointments'      => $todayAppointments,
                'chartLabels'            => $chartLabels,
                'chartData'              => $chartData,
                
                // New Module Data pass
                'pendingReviews'         => $pendingReviews,
                'activeMemberships'      => $activeMemberships,
                'totalRazorpayRevenue'   => $totalRazorpayRevenue,
                'newUsersToday'          => $newUsersToday,
                'totalUsers'             => $totalUsers,
                'activeCombos'           => $activeCombos,
                'onLeaveToday'           => $onLeaveToday,

                'returnPerformance'      => $this->getReturnPerformanceData(),
                'todayHourlyData'        => $this->getTodayHourlyData(),
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
}
