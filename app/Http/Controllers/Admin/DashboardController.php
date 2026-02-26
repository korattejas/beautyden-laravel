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
            $totalCity = City::where('status', 1)->count();
            $totalProductBrand = ProductBrand::where('status', 1)->count();

            // Total Revenue from Completed Appointments
            $completedAppointments = Appointment::where('status', 3)->get();
            $totalRevenue = 0;
            foreach ($completedAppointments as $app) {
                $servicesData = $app->services_data;
                $totalRevenue += (float)($servicesData['summary']['grand_total'] ?? 0);
            }

            $todayAppointments = Appointment::whereDate('appointment_date', date('Y-m-d'))->count();

            // Completed Appointments Chart Data (Current Month Only)
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
            
            // Fill all dates of the current month
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
}
