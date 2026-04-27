<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\CustomerReviewController;
use App\Http\Controllers\Api\HiringController;
use App\Http\Controllers\Api\HomeCounterController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ProductBrandController;
use App\Http\Controllers\Api\FaqsController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ContactSubmissionsController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\User\AuthenticationController;
use App\Http\Controllers\Api\PoliciesController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Middleware\RequestModifier;
use App\Http\Middleware\ResponseModifier;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\JWTTokenMiddleware;
use App\Http\Controllers\Api\Beautician\BeauticianController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ApplicationHomeController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ServiceMasterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



/*======================================================== Customer API ==============================================*/

Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/customer')->group(function () {
        Route::post('sendOtpOnMobileNumber', [AuthenticationController::class, 'sendOtpOnMobileNumber']);
        Route::post('verifyOtpOnMobileNumber', [AuthenticationController::class, 'verifyOtpOnMobileNumber']);
    });
});


Route::middleware([JWTTokenMiddleware::class, RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/customer')->group(function () {
        Route::post('profileUpdate', [AuthenticationController::class, 'profileUpdate']);
        Route::post('getProfile', [AuthenticationController::class, 'getProfile']);
        Route::post('getTotalBookService', [AuthenticationController::class, 'getTotalBookService']);
        Route::post('getBookServiceDetails', [AuthenticationController::class, 'getBookServiceDetails']);
        Route::post('updateFcmToken', [AuthenticationController::class, 'updateFcmToken']);
        Route::get('logout', [AuthenticationController::class, 'logout']);
        Route::post('saveUserAddress', [AuthenticationController::class, 'saveUserAddress']);
        Route::post('getUserAddresses', [AuthenticationController::class, 'getUserAddresses']);
        Route::post('deleteUserAddress', [AuthenticationController::class, 'deleteUserAddress']);
        Route::get('offers', [OfferController::class, 'getOffers']);
        Route::post('homePageData', [ApplicationHomeController::class, 'getHomePageData']);
        Route::get('listCoupons', [CouponController::class, 'listCoupons']);
        Route::post('applyCoupon', [CouponController::class, 'applyCoupon']);

        // Membership
        Route::get('membershipPlans', [MembershipController::class, 'listPlans']);
        Route::post('buyMembership', [MembershipController::class, 'buyMembership']);
        Route::post("submitReview", [ReviewApiController::class, "submitReview"]);
    });
});

Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/')->group(function () {
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('services', [ServiceController::class, 'getServices']);
        Route::get('blogCategory', [BlogController::class, 'getBlogCategory']);
        Route::post('blogs', [BlogController::class, 'getBlogs']);
        Route::post('blogView', [BlogController::class, 'blogView']);
        Route::get('teamMember', [TeamMemberController::class, 'getTeamMembers']);
        Route::post('beauticianInquiryFormSubmit', [TeamMemberController::class, 'beauticianInquiryFormSubmit']);
        Route::post('customerReview', [CustomerReviewController::class, 'getCustomerReviews']);
        Route::post('hiring', [HiringController::class, 'getHiring']);
        Route::get('homeCounter', [HomeCounterController::class, 'getHomeCounter']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('productBrand', [ProductBrandController::class, 'getProductBrand']);
        Route::get('faqs', [FaqsController::class, 'getFaqs']);
        Route::get('settings', [SettingController::class, 'getsettings']);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
        Route::post('policies', [PoliciesController::class, 'getPolicies']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);
        
        // Service Master API
        Route::post('serviceMasters', [ServiceMasterController::class, 'getServiceMasters']);
        Route::post('serviceMasterDetails', [ServiceMasterController::class, 'getServiceMasterDetails']);
    });
});

/*======================================================== Beautician API ==============================================*/

Route::middleware([SanitizeInput::class])->group(function () {
    Route::prefix('V1/beautician')->group(function () {
        Route::post('sendLoginOtp', [BeauticianController::class, 'sendLoginOtp']);
        Route::post('verifyLoginOtp', [BeauticianController::class, 'verifyLoginOtp']);
    });
});

Route::middleware([JWTTokenMiddleware::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/beautician')->group(function () {
        Route::post('dashboard', [BeauticianController::class, 'dashboard']);
        Route::post('getAppointments', [BeauticianController::class, 'getAppointments']);
        Route::post('getAppointmentDetails', [BeauticianController::class, 'getAppointmentDetails']);
        Route::post('appointmentUpdateStatus', [BeauticianController::class, 'appointmentUpdateStatus']);
        Route::post('getProfile', [BeauticianController::class, 'getProfile']);

        // Attendance / Availability
        Route::get('getAvailability', [AttendanceApiController::class, 'index']);
        Route::post('markLeave', [AttendanceApiController::class, 'store']);
        Route::post('cancelLeave', [AttendanceApiController::class, 'destroy']);
    });
});










/*======================================================== Debug API ==============================================*/

Route::middleware([])->group(function () {
    Route::prefix('Test/V1/customer')->group(function () {
        Route::post('sendOtpOnMobileNumber', [AuthenticationController::class, 'sendOtpOnMobileNumber']);
        Route::post('verifyOtpOnMobileNumber', [AuthenticationController::class, 'verifyOtpOnMobileNumber']);
    });
});

Route::middleware([JWTTokenMiddleware::class])->group(function () {
    Route::prefix('Test/V1/customer')->group(function () {
        Route::post('profileUpdate', [AuthenticationController::class, 'profileUpdate']);
        Route::post('getProfile', [AuthenticationController::class, 'getProfile']);
        Route::post('getTotalBookService', [AuthenticationController::class, 'getTotalBookService']);
        Route::post('getBookServiceDetails', [AuthenticationController::class, 'getBookServiceDetails']);
        Route::post('updateFcmToken', [AuthenticationController::class, 'updateFcmToken']);
        Route::get('logout', [AuthenticationController::class, 'logout']);
        Route::post('saveUserAddress', [AuthenticationController::class, 'saveUserAddress']);
        Route::post('getUserAddresses', [AuthenticationController::class, 'getUserAddresses']);
        Route::post('deleteUserAddress', [AuthenticationController::class, 'deleteUserAddress']);
        Route::get('offers', [OfferController::class, 'getOffers']);
        Route::post('homePageData', [ApplicationHomeController::class, 'getHomePageData']);
        Route::get('listCoupons', [CouponController::class, 'listCoupons']);
        Route::post('applyCoupon', [CouponController::class, 'applyCoupon']);

        // Membership
        Route::get('membershipPlans', [MembershipController::class, 'listPlans']);
        Route::post('buyMembership', [MembershipController::class, 'buyMembership']);
        Route::post("submitReview", [ReviewApiController::class, "submitReview"]);
    });
});

Route::middleware([])->group(function () {
    Route::prefix('Test/V1/')->group(function () {
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('services', [ServiceController::class, 'getServices']);
        Route::get('blogCategory', [BlogController::class, 'getBlogCategory']);
        Route::post('blogs', [BlogController::class, 'getBlogs']);
        Route::post('blogView', [BlogController::class, 'blogView']);
        Route::get('teamMember', [TeamMemberController::class, 'getTeamMembers']);
        Route::post('beauticianInquiryFormSubmit', [TeamMemberController::class, 'beauticianInquiryFormSubmit']);
        Route::post('customerReview', [CustomerReviewController::class, 'getCustomerReviews']);
        Route::post('hiring', [HiringController::class, 'getHiring']);
        Route::get('homeCounter', [HomeCounterController::class, 'getHomeCounter']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('productBrand', [ProductBrandController::class, 'getProductBrand']);
        Route::get('faqs', [FaqsController::class, 'getFaqs']);
        Route::get('settings', [SettingController::class, 'getsettings']);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
        Route::post('policies', [PoliciesController::class, 'getPolicies']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);
       
        // Service Master API
        Route::post('serviceMasters', [ServiceMasterController::class, 'getServiceMasters']);
        Route::post('serviceMasterDetails', [ServiceMasterController::class, 'getServiceMasterDetails']);
    });
});
