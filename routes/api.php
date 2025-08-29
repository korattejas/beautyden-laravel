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
use App\Http\Controllers\Api\FaqsController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ContactSubmissionsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AppointmentsController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\PoseImagesController;
use App\Http\Controllers\Api\User\AuthenticationController;
use App\Http\Controllers\Api\Photographer\AuthenticationController as PhotographerAuthenticationController;
use App\Http\Controllers\Api\Photographer\PhotographerController;
use App\Http\Middleware\RequestModifier;
use App\Http\Middleware\ResponseModifier;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\JWTTokenMiddleware;
use App\Http\Middleware\JWTTokenPhotographer;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



/*======================================================== User API ==============================================*/

Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/user')->group(function () {
        Route::post('signUp', [AuthenticationController::class, 'registrations']);
        Route::post('signIn', [AuthenticationController::class, 'login']);
        Route::post('sendOtp', [AuthenticationController::class, 'sendResendMobileOrForgotPasswordOtp']);
        Route::post('verifyMobileOtpRegister', [AuthenticationController::class, 'verifyMobileOtpRegister']);
    });
});

Route::middleware([JWTTokenMiddleware::class, RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/user')->group(function () {
        Route::get('logout', [AuthenticationController::class, 'logout']);
        Route::get('settingData', [PoseImagesController::class, 'settingData']);
        Route::get('filterData', [PoseImagesController::class, 'filterData']);
        Route::get('flashScreenImage', [PoseImagesController::class, 'flashScreenImage']);
        Route::get('getHomePageCategory', [CategoryController::class, 'getHomePageCategory']);
        Route::post('categoryWiseSubCategoryGet', [SubCategoryController::class, 'categoryWiseSubCategoryGet']);
        Route::get('getCategory', [CategoryController::class, 'getCategory']);
        Route::get('getSubCategory', [SubCategoryController::class, 'getSubCategory']);
        Route::get('getAllPoseImage', [PoseImagesController::class, 'getAllPoseImage']);
        Route::post('getPoseImage', [PoseImagesController::class, 'getPoseImage']);
        Route::post('viewPoseImage', [PoseImagesController::class, 'viewPoseImage']);
    });
});

/*======================================================== Photographer API ==============================================*/

Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/photographer')->group(function () {
        Route::post('signUp', [PhotographerAuthenticationController::class, 'registrations']);
        Route::post('signIn', [PhotographerAuthenticationController::class, 'login']);
        Route::post('sendOtp', [PhotographerAuthenticationController::class, 'sendResendMobileOrForgotPasswordOtp']);
        Route::post('verifyMobileOtpRegister', [PhotographerAuthenticationController::class, 'verifyMobileOtpRegister']);
    });
});




















Route::middleware([RequestModifier::class, ResponseModifier::class, SanitizeInput::class])->group(function () {
    Route::prefix('V1/')->group(function () {
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('services', [ServiceController::class, 'getServices']);
        Route::get('blogCategory', [BlogController::class, 'getBlogCategory']);
        Route::post('blogs', [BlogController::class, 'getBlogs']);
        Route::get('teamMember', [TeamMemberController::class, 'getTeamMembers']);
        Route::post('customerReview', [CustomerReviewController::class, 'getCustomerReviews']);
        Route::post('hiring', [HiringController::class, 'getHiring']);
        Route::get('homeCounter', [HomeCounterController::class, 'getHomeCounter']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('faqs', [FaqsController::class, 'getFaqs']);
        Route::get('settings', [SettingController::class, 'getsettings']);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
    });
});

/*======================================================== Debug API ==============================================*/

Route::middleware([])->group(function () {
    Route::prefix('Test/V1/')->group(function () {
        Route::get('serviceCategory', [ServiceController::class, 'getServiceCategory']);
        Route::post('services', [ServiceController::class, 'getServices']);
        Route::get('blogCategory', [BlogController::class, 'getBlogCategory']);
        Route::post('blogs', [BlogController::class, 'getBlogs']);
        Route::get('teamMember', [TeamMemberController::class, 'getTeamMembers']);
        Route::post('customerReview', [CustomerReviewController::class, 'getCustomerReviews']);
        Route::post('hiring', [HiringController::class, 'getHiring']);
        Route::get('homeCounter', [HomeCounterController::class, 'getHomeCounter']);
        Route::get('cities', [CityController::class, 'getCities']);
        Route::get('faqs', [FaqsController::class, 'getFaqs']);
        Route::get('settings', [SettingController::class, 'getsettings']);
        Route::post('bookAppointment', [AppointmentsController::class, 'bookAppointment']);
        Route::post('contactFormSubmit', [ContactSubmissionsController::class, 'contactFormSubmit']);
    });
});
