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
            $totalAppoinmentSuccess = Appointment::where('status', 1)->count();
            $totalAppoinmentPending = Appointment::where('status', 0)->count();
            $totalContacts = ContactSubmission::count();
            $totalServiceCategory = ServiceCategory::where('status', 1)->count();
            $totalServices = Service::where('status', 1)->count();
            $totalBlogs = Blog::where('status', 1)->count();
            $totalBlogCategory = BlogCategory::where('status', 1)->count();
            $totalTeamMember = TeamMember::where('status', 1)->count();
            $totalHirings = Hiring::where('status', 1)->count();
            $totalCustomerReviews = CustomerReview::where('status', 1)->count();
            $totalCity = City::where('status', 1)->count();

            return view('admin.dashboard.index', [
                'totalAppointments'      => $totalAppointments,
                'totalAppoinmentSuccess' => $totalAppoinmentSuccess,
                'totalAppoinmentPending' => $totalAppoinmentPending,
                'totalContacts'          => $totalContacts,
                'totalServiceCategory'   => $totalServiceCategory,
                'totalServices'          => $totalServices,
                'totalBlogs'             => $totalBlogs,
                'totalBlogCategory'      => $totalBlogCategory,
                'totalTeamMember'        => $totalTeamMember,
                'totalHirings'           => $totalHirings,
                'totalCustomerReviews'   => $totalCustomerReviews,
                'totalCity'              => $totalCity,
            ]);
        } catch (\Exception $e) {
            logCatchException($e, $this->controller_name, $function_name);
            return response()->json(['error' => $this->error_message], $this->exception_error_code);
        }
    }
}
