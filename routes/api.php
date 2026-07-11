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
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FaqsController;

use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ContactSubmissionsController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\User\AuthenticationController;
use App\Http\Controllers\Api\PoliciesController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\CategoryLookbookController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\Beautician\AttendanceApiController;
use App\Http\Middleware\RequestModifier;
use App\Http\Middleware\ResponseModifier;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\JWTTokenMiddleware;
use App\Http\Controllers\Api\Beautician\BeauticianController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ApplicationHomeController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ServiceMasterController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CartController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Customer Routes
Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/customer')->group(function () {
        Route::post('sendOtpOnMobileNumber', [AuthenticationController::class, 'sendOtpOnMobileNumber']);
        Route::post('verifyOtpOnMobileNumber', [AuthenticationController::class, 'verifyOtpOnMobileNumber']);
        Route::post('sendTestNotification', [NotificationController::class, 'sendTestNotification']);
        Route::post('homePageData', [ApplicationHomeController::class, 'getHomePageData']);
        Route::post('trendingServices', [ApplicationHomeController::class, 'getTrendingServices']);
        Route::post('serviceCombos', [ApplicationHomeController::class, 'getServiceCombos']);
        Route::post('getServiceSearchData', [ApplicationHomeController::class, 'getServiceSearchData']);
        Route::get('offerBanner', [OfferController::class, 'getOffers']);
        Route::get('listCoupons', [CouponController::class, 'listCoupons']);
        Route::get('membershipPlans', [MembershipController::class, 'listPlans']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('serviceType', [ServiceController::class, 'getServiceType']);
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('serviceMasters', [ServiceMasterController::class, 'getServiceMasters']);
        Route::post('serviceMasterDetails', [ServiceMasterController::class, 'getServiceMasterDetails']);
        Route::post('serviceVariantDetails', [ServiceMasterController::class, 'getServiceVariantDetails']);
        Route::post('categoryReviews', [ReviewApiController::class, 'getCategoryReviews']);
        Route::get('appSettings', [AppSettingController::class, 'getAppSettings']);
        Route::post('testFast2Sms', [AuthenticationController::class, 'testFast2Sms']);
        Route::get('categoryLookbook', [CategoryLookbookController::class, 'getCategoryLookbooks']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);
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
        Route::post('deleteAccount', [AuthenticationController::class, 'deleteAccount']);
        Route::post('saveUserAddress', [AuthenticationController::class, 'saveUserAddress']);
        Route::post('getUserAddresses', [AuthenticationController::class, 'getUserAddresses']);
        Route::post('deleteUserAddress', [AuthenticationController::class, 'deleteUserAddress']);
        Route::post('applyCoupon', [CouponController::class, 'applyCoupon']);
        Route::post('buyMembership', [MembershipController::class, 'buyMembership']);
        Route::post("submitReview", [ReviewApiController::class, "submitReview"]);
        Route::post("getAppointmentReview", [ReviewApiController::class, "getAppointmentReview"]);
        Route::post("getAppointmentSummaryForReview", [ReviewApiController::class, "getAppointmentSummaryForReview"]);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('bookAppointmentForApp', [AppointmentsController::class, 'bookAppointmentForApp']);
        Route::post('cart/add', [CartController::class, 'addToCart']);
        Route::post('cart/get', [CartController::class, 'getCart']);
        Route::post('cart/update', [CartController::class, 'updateCartItem']);
    });

});


// Web Routes
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
        Route::post('bookAppointmentForApp', [AppointmentsController::class, 'bookAppointmentForApp']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
        Route::post('policies', [PoliciesController::class, 'getPolicies']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);
       
    });
});

//Beautician Routes
Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/beautician')->group(function () {
        Route::post('sendLoginOtp', [BeauticianController::class, 'sendLoginOtp']);
        Route::post('verifyLoginOtp', [BeauticianController::class, 'verifyLoginOtp']);
    });
});

Route::middleware([JWTTokenMiddleware::class, RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/beautician')->group(function () {
        Route::get('logout', [BeauticianController::class, 'logout']);
        Route::post('dashboard', [BeauticianController::class, 'dashboard']);
        Route::post('getAppointments', [BeauticianController::class, 'getAppointments']);
        Route::post('exportAppointments', [BeauticianController::class, 'exportAppointments']);
        Route::post('getAppointmentDetails', [BeauticianController::class, 'getAppointmentDetails']);
        Route::post('exportAppointmentDetails', [BeauticianController::class, 'exportAppointmentDetails']);
        Route::post('getRepeatCustomers', [BeauticianController::class, 'getRepeatCustomers']);
        Route::post('appointmentUpdateStatus', [BeauticianController::class, 'appointmentUpdateStatus']);
        Route::post('getProfile', [BeauticianController::class, 'getProfile']);
        Route::post('updateProfile', [BeauticianController::class, 'updateProfile']);
        Route::post('deleteAccount', [BeauticianController::class, 'deleteAccount']);
        Route::get('getAvailability', [AttendanceApiController::class, 'index']);
        Route::post('markLeave', [AttendanceApiController::class, 'store']);
        Route::post('cancelLeave', [AttendanceApiController::class, 'destroy']);
        Route::get('productCategory', [ProductController::class, 'getProductCategories']);
        Route::post('products', [ProductController::class, 'getProducts']);
        Route::get('productDetails/{id}', [ProductController::class, 'getProductDetails']);
        Route::post('placeOrder', [ProductController::class, 'placeOrder']);
        Route::post('getMyOrders', [ProductController::class, 'getMyOrders']);
        Route::post('getOrderDetails', [ProductController::class, 'getOrderDetails']);
        Route::post('exportOrderInvoice', [ProductController::class, 'exportOrderInvoice']);
    });
});



/*======================================================== Debug API ==============================================*/

//Customer Routes
Route::middleware([])->group(function () {
    Route::prefix('Test/V1/customer')->group(function () {
        Route::post('sendOtpOnMobileNumber', [AuthenticationController::class, 'sendOtpOnMobileNumber']);
        Route::post('verifyOtpOnMobileNumber', [AuthenticationController::class, 'verifyOtpOnMobileNumber']);
        Route::post('sendTestNotification', [NotificationController::class, 'sendTestNotification']);
        Route::post('homePageData', [ApplicationHomeController::class, 'getHomePageData']);
        Route::post('trendingServices', [ApplicationHomeController::class, 'getTrendingServices']);
        Route::post('serviceCombos', [ApplicationHomeController::class, 'getServiceCombos']);
        Route::post('getServiceSearchData', [ApplicationHomeController::class, 'getServiceSearchData']);
        Route::get('offerBanner', [OfferController::class, 'getOffers']);
        Route::get('listCoupons', [CouponController::class, 'listCoupons']);
        Route::get('membershipPlans', [MembershipController::class, 'listPlans']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('serviceType', [ServiceController::class, 'getServiceType']);
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('serviceMasters', [ServiceMasterController::class, 'getServiceMasters']);
        Route::post('serviceMasterDetails', [ServiceMasterController::class, 'getServiceMasterDetails']);
        Route::post('serviceVariantDetails', [ServiceMasterController::class, 'getServiceVariantDetails']);
        Route::post('categoryReviews', [ReviewApiController::class, 'getCategoryReviews']);
        Route::get('appSettings', [AppSettingController::class, 'getAppSettings']);
        Route::post('testFast2Sms', [AuthenticationController::class, 'testFast2Sms']);
        Route::get('categoryLookbook', [CategoryLookbookController::class, 'getCategoryLookbooks']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);
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
        Route::post('deleteAccount', [AuthenticationController::class, 'deleteAccount']);
        Route::post('saveUserAddress', [AuthenticationController::class, 'saveUserAddress']);
        Route::post('getUserAddresses', [AuthenticationController::class, 'getUserAddresses']);
        Route::post('deleteUserAddress', [AuthenticationController::class, 'deleteUserAddress']);
        Route::post('applyCoupon', [CouponController::class, 'applyCoupon']);
        Route::post('buyMembership', [MembershipController::class, 'buyMembership']);
        Route::post("submitReview", [ReviewApiController::class, "submitReview"]);
        Route::post("getAppointmentReview", [ReviewApiController::class, "getAppointmentReview"]);
        Route::post("getAppointmentSummaryForReview", [ReviewApiController::class, "getAppointmentSummaryForReview"]);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('bookAppointmentForApp', [AppointmentsController::class, 'bookAppointmentForApp']);
        Route::post('cart/add', [CartController::class, 'addToCart']);
        Route::post('cart/get', [CartController::class, 'getCart']);
        Route::post('cart/update', [CartController::class, 'updateCartItem']);
    });
});


//Web Routes
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
        Route::post('bookAppointmentForApp', [AppointmentsController::class, 'bookAppointmentForApp']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
        Route::post('policies', [PoliciesController::class, 'getPolicies']);
        Route::get('portfolio', [PortfolioController::class, 'getPortfolio']);

    });
});




//Beautician Routes
Route::middleware([SanitizeInput::class])->group(function () {
    Route::prefix('Test/V1/beautician')->group(function () {
        Route::post('sendLoginOtp', [BeauticianController::class, 'sendLoginOtp']);
        Route::post('verifyLoginOtp', [BeauticianController::class, 'verifyLoginOtp']);
    });
});

Route::middleware([JWTTokenMiddleware::class, SanitizeInput::class])->group(function () {
    Route::prefix('Test/V1/beautician')->group(function () {
        Route::get('logout', [BeauticianController::class, 'logout']);
        Route::post('dashboard', [BeauticianController::class, 'dashboard']);
        Route::post('getAppointments', [BeauticianController::class, 'getAppointments']);
        Route::post('exportAppointments', [BeauticianController::class, 'exportAppointments']);
        Route::post('getAppointmentDetails', [BeauticianController::class, 'getAppointmentDetails']);
        Route::post('exportAppointmentDetails', [BeauticianController::class, 'exportAppointmentDetails']);
        Route::post('getRepeatCustomers', [BeauticianController::class, 'getRepeatCustomers']);
        Route::post('appointmentUpdateStatus', [BeauticianController::class, 'appointmentUpdateStatus']);
        Route::post('getProfile', [BeauticianController::class, 'getProfile']);
        Route::post('updateProfile', [BeauticianController::class, 'updateProfile']);
        Route::post('deleteAccount', [BeauticianController::class, 'deleteAccount']);
        Route::get('getAvailability', [AttendanceApiController::class, 'index']);
        Route::post('markLeave', [AttendanceApiController::class, 'store']);
        Route::post('cancelLeave', [AttendanceApiController::class, 'destroy']);
        Route::get('productCategory', [ProductController::class, 'getProductCategories']);
        Route::post('products', [ProductController::class, 'getProducts']);
        Route::get('productDetails/{id}', [ProductController::class, 'getProductDetails']);
        Route::post('placeOrder', [ProductController::class, 'placeOrder']);
        Route::post('getMyOrders', [ProductController::class, 'getMyOrders']);
        Route::post('getOrderDetails', [ProductController::class, 'getOrderDetails']);
        Route::post('exportOrderInvoice', [ProductController::class, 'exportOrderInvoice']);
    });
});

