<?php

use App\Http\Controllers\Admin\AppointmentsController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactSubmissionsController;
use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Admin\HomeCounterController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PoliciesController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceSubcategoryController;
use App\Http\Controllers\Admin\ServiceCityPriceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\ContractSignedController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ServiceEssentialController;
use App\Http\Controllers\Admin\ServiceMasterController;
use App\Http\Controllers\Admin\CouponCodeController;
use App\Http\Controllers\Admin\CouponUsageController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\MembershipPlanController;
use App\Http\Controllers\Admin\ServiceComboController;
use App\Http\Controllers\Admin\ServiceCityMasterController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RazorpayTransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContractController;
use App\Http\Middleware\AdminCheck;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('logs/BeautyDen@admin.com/8998', [LogViewerController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});


Route::get('/beautician-contracts', [ContractController::class, 'showAgreements']);
Route::post('/contracts/verify', [ContractController::class, 'verifyProvider'])->name('contracts.verify');
Route::get('/contracts/sign', function () {
    return view('contracts.sign');
})->name('contracts.sign');
Route::post('/contracts/save', [ContractController::class, 'saveSignature'])->name('contracts.save');
Route::get('/contracts/success', [ContractController::class, 'success'])->name('contracts.success');


/* Admin Route */
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [LoginController::class, 'index'])->name('admin.login');
    Route::post('login-check', [LoginController::class, 'loginCheck'])->name('admin.login-check');

    Route::group(['middleware' => [AdminCheck::class]], function () {
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('get-analytics-data', [DashboardController::class, 'getAnalyticsData'])->name('admin.dashboard.analytics');
        Route::get('export-analytics', [DashboardController::class, 'exportAnalytics'])->name('admin.dashboard.export-analytics');
        Route::get('get-management-counts', [DashboardController::class, 'getManagementCounts'])->name('admin.dashboard.management-counts');

        /* Contract Signed Route */
        Route::get('contract-signed', [ContractSignedController::class, 'index'])->name('admin.contract-signed.index');
        Route::get('getDataContracts', [ContractSignedController::class, 'getDataContracts'])->name('getDataContracts');
        Route::delete('contract-signed/{id}', [ContractSignedController::class, 'destroy']);
        Route::get('contract-signed/status/{id}/{status}', [ContractSignedController::class, 'changeStatus']);


        /* Services Category Route */
        Route::get('service-category', [ServiceCategoryController::class, 'index'])->name('admin.service-category.index');
        Route::get('service-category/create', [ServiceCategoryController::class, 'create'])->name('admin.service-category.create');
        Route::post('service-category/store', [ServiceCategoryController::class, 'store']);
        Route::delete('service-category/{id}', [ServiceCategoryController::class, 'destroy']);
        Route::get('service-category/edit/{id}', [ServiceCategoryController::class, 'edit'])->name('admin.service-category.edit');
        Route::get('getDataServiceCategory', [ServiceCategoryController::class, 'getDataServiceCategory'])->name('getDataServiceCategory');
        Route::get('service-category/status/{id}/{status}', [ServiceCategoryController::class, 'changeStatus']);
        Route::get('service-category/priority-status/{id}/{status}', [ServiceCategoryController::class, 'changePriorityStatus']);

        /* Services Subcategory Route */
        Route::get('service-subcategory', [ServiceSubcategoryController::class, 'index'])->name('admin.service-subcategory.index');
        Route::get('service-subcategory/create', [ServiceSubcategoryController::class, 'create'])->name('admin.service-subcategory.create');
        Route::post('service-subcategory/store', [ServiceSubcategoryController::class, 'store']);
        Route::delete('service-subcategory/{id}', [ServiceSubcategoryController::class, 'destroy']);
        Route::get('service-subcategory/edit/{id}', [ServiceSubcategoryController::class, 'edit'])->name('admin.service-subcategory.edit');
        Route::get('getDataServiceSubcategory', [ServiceSubcategoryController::class, 'getDataServiceSubcategory'])->name('getDataServiceSubcategory');
        Route::get('service-subcategory/status/{id}/{status}', [ServiceSubcategoryController::class, 'changeStatus']);
        Route::get('service-subcategory/priority-status/{id}/{status}', [ServiceSubcategoryController::class, 'changePriorityStatus']);

        /* product brand */
        Route::get('product-brand', [ProductBrandController::class, 'index'])->name('admin.product-brand.index');
        Route::get('product-brand/create', [ProductBrandController::class, 'create'])->name('admin.product-brand.create');
        Route::post('product-brand/store', [ProductBrandController::class, 'store']);
        Route::delete('product-brand/{id}', [ProductBrandController::class, 'destroy']);
        Route::get('product-brand/edit/{id}', [ProductBrandController::class, 'edit'])->name('admin.product-brand.edit');
        Route::get('getDataProductBrand', [ProductBrandController::class, 'getDataProductBrand'])->name('getDataProductBrand');
        Route::get('product-brand/status/{id}/{status}', [ProductBrandController::class, 'changeStatus']);

         /* Portfoio */
        Route::get('portfolio', [PortfolioController::class, 'index'])->name('admin.portfolio.index');
        Route::get('portfolio/create', [PortfolioController::class, 'create'])->name('admin.portfolio.create');
        Route::post('portfolio/store', [PortfolioController::class, 'store'])->name('admin.portfolio.store');
        Route::get('portfolio/edit/{id}', [PortfolioController::class, 'edit'])->name('admin.portfolio.edit');
        Route::delete('portfolio/{id}', [PortfolioController::class, 'destroy'])->name('admin.portfolio.destroy');
        Route::get('getDataPortfolio', [PortfolioController::class, 'getDataPortfolio'])->name('getDataPortfolio');
        Route::get('portfolio/status/{id}/{status}', [PortfolioController::class, 'changeStatus'])->name('admin.portfolio.changeStatus');
        Route::post('portfolio/remove-image', [PortfolioController::class, 'removeImage'])->name('admin.portfolio.removeImage');

        /* Users */
        Route::get('users', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('users/show/{id}', [UserController::class, 'show'])->name('admin.user.show');
        Route::get('getDataUser', [UserController::class, 'getDataUser'])->name('admin.user.getDataUser');

        /* Offers */
        Route::get('offers', [OfferController::class, 'index'])->name('admin.offers.index');
        Route::get('offers/create', [OfferController::class, 'create'])->name('admin.offers.create');
        Route::post('offers/store', [OfferController::class, 'store'])->name('admin.offers.store');
        Route::get('offers/edit/{id}', [OfferController::class, 'edit'])->name('admin.offers.edit');
        Route::get('getDataOffers', [OfferController::class, 'getDataOffers'])->name('admin.offers.getDataOffers');
        Route::delete('offers/{id}', [OfferController::class, 'destroy'])->name('admin.offers.destroy');
        Route::get('offers/status/{id}/{status}', [OfferController::class, 'changeStatus'])->name('admin.offers.changeStatus');
        Route::post('offers/remove-media', [OfferController::class, 'removeMedia'])->name('admin.offers.removeMedia');



    

        /* Service Essential Route */
        Route::get('service-essential', [ServiceEssentialController::class, 'index'])->name('admin.service-essential.index');
        Route::get('service-essential/create', [ServiceEssentialController::class, 'create'])->name('admin.service-essential.create');
        Route::post('service-essential/store', [ServiceEssentialController::class, 'store']);
        Route::delete('service-essential/{id}', [ServiceEssentialController::class, 'destroy']);
        Route::get('service-essential/edit/{id}', [ServiceEssentialController::class, 'edit'])->name('admin.service-essential.edit');
        Route::get('getDataEssential', [ServiceEssentialController::class, 'getDataEssential'])->name('getDataEssential');
        Route::get('service-essential/status/{id}/{status}', [ServiceEssentialController::class, 'changeStatus']);

        /* Service Master Route */
        Route::get('service-master', [ServiceMasterController::class, 'index'])->name('admin.service-master.index');
        Route::get('service-master/create', [ServiceMasterController::class, 'create'])->name('admin.service-master.create');
        Route::post('service-master/store', [ServiceMasterController::class, 'store']);
        Route::delete('service-master/{id}', [ServiceMasterController::class, 'destroy']);
        Route::get('service-master/edit/{id}', [ServiceMasterController::class, 'edit'])->name('admin.service-master.edit');
        Route::get('service-master/show/{id}', [ServiceMasterController::class, 'show'])->name('admin.service-master.show');
        Route::get('getDataServiceMaster', [ServiceMasterController::class, 'getDataServiceMaster'])->name('getDataServiceMaster');
        Route::get('service-master/status/{id}/{status}', [ServiceMasterController::class, 'changeStatus']);
        Route::get('service-master/get-subcategories/{categoryId}', [ServiceMasterController::class, 'getSubcategories']);

        /* Blog Category Route */
        Route::get('blog-category', [BlogCategoryController::class, 'index'])->name('admin.blog-category.index');
        Route::get('blog-category/create', [BlogCategoryController::class, 'create'])->name('admin.blog-category.create');
        Route::post('blog-category/store', [BlogCategoryController::class, 'store']);
        Route::delete('blog-category/{id}', [BlogCategoryController::class, 'destroy']);
        Route::get('blog-category/edit/{id}', [BlogCategoryController::class, 'edit'])->name('admin.blog-category.edit');
        Route::get('getDataBlogCategory', [BlogCategoryController::class, 'getDataBlogCategory'])->name('getDataBlogCategory');
        Route::get('blog-category/status/{id}/{status}', [BlogCategoryController::class, 'changeStatus']);
        Route::get('blog-category/priority-status/{id}/{status}', [BlogCategoryController::class, 'changePriorityStatus']);

        /* Services Route */
        Route::get('service', [ServiceController::class, 'index'])->name('admin.service.index');
        Route::get('service/create', [ServiceController::class, 'create'])->name('admin.service.create');
        Route::post('service/store', [ServiceController::class, 'store']);
        Route::delete('service/{id}', [ServiceController::class, 'destroy']);
        Route::get('service/edit/{id}', [ServiceController::class, 'edit'])->name('admin.service.edit');
        Route::get('getDataService', [ServiceController::class, 'getDataService'])->name('getDataService');
        Route::get('service/status/{id}/{status}', [ServiceController::class, 'changeStatus']);
        Route::get('service/priority-status/{id}/{status}', [ServiceController::class, 'changePriorityStatus']);
        Route::get('service-view/{id}', [ServiceController::class, 'view']);
        Route::get('service/export-pdf', [ServiceController::class, 'exportPdf'])->name('admin.service.export.pdf');
        Route::get('service/export-excel', [ServiceController::class, 'exportExcel'])->name('admin.service.export.excel');
        Route::get('service/get-subcategories/{categoryId}', [ServiceController::class, 'getSubcategories']);


        /* Services City Price Route */
        Route::get('service-city-price', [ServiceCityPriceController::class, 'index'])->name('admin.service-city-price.index');
        Route::get('service-city-price/create', [ServiceCityPriceController::class, 'create'])->name('admin.service-city-price.create');
        Route::post('service-city-price/store', [ServiceCityPriceController::class, 'store']);
        Route::delete('service-city-price/{id}', [ServiceCityPriceController::class, 'destroy']);
        Route::get('service-city-price/edit/{id}', [ServiceCityPriceController::class, 'edit'])->name('admin.service-city-price.edit');
        Route::get('getDataServiceCityPrice', [ServiceCityPriceController::class, 'getDataServiceCityPrice'])->name('getDataServiceCityPrice');
        Route::get('service-city-price/status/{id}/{status}', [ServiceCityPriceController::class, 'changeStatus']);
        Route::get('service-city-price/priority-status/{id}/{status}', [ServiceCityPriceController::class, 'changePriorityStatus']);
        Route::get('service-city-price-view/{id}', [ServiceCityPriceController::class, 'view']);
        Route::get('services-by-category', [ServiceCityPriceController::class, 'getServicesByCategory'])
            ->name('admin.services.by-category');
        Route::get('service-city-price/export-pdf', [ServiceCityPriceController::class, 'exportPdf'])->name('admin.service-city-price.export.pdf');
        Route::get('service-city-price/export-excel', [ServiceCityPriceController::class, 'exportExcel'])->name('admin.service-city-price.export.excel');
        Route::get('service-city-price/get-serviceCityPriceSubCategories/{categoryId}', [ServiceCityPriceController::class, 'getSubcategories']);

        // Team Members
        Route::get('team', [TeamMemberController::class, 'index'])->name('admin.team.index');
        Route::get('team/create', [TeamMemberController::class, 'create'])->name('admin.team.create');
        Route::post('team/store', [TeamMemberController::class, 'store'])->name('admin.team.store');
        Route::get('team/edit/{id}', [TeamMemberController::class, 'edit'])->name('admin.team.edit');
        Route::delete('team/{id}', [TeamMemberController::class, 'destroy'])->name('admin.team.destroy');
        Route::get('getDataTeamMembers', [TeamMemberController::class, 'getDataTeamMembers'])->name('getDataTeamMembers');
        Route::get('team/status/{id}/{status}', [TeamMemberController::class, 'changeStatus'])->name('admin.team.changeStatus');
        Route::get('team/priority-status/{id}/{status}', [TeamMemberController::class, 'changePriorityStatus'])->name('admin.team.changePriorityStatus');
        Route::get('team-view/{id}', [TeamMemberController::class, 'view']);
        Route::get('team/appointments-report/{id}', [TeamMemberController::class, 'getAppointmentsReport'])->name('admin.team.appointmentsReport');
        Route::get('team/appointments-report-download/{id}', [TeamMemberController::class, 'downloadAppointmentsReport'])->name('admin.team.appointmentsReportDownload');
        Route::get('team/return-customers-report/{id}', [TeamMemberController::class, 'getReturnCustomersReport'])->name('admin.team.returnCustomersReport');

        // Customer Reviews
        Route::get('reviews', [CustomerReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('reviews/create', [CustomerReviewController::class, 'create'])->name('admin.reviews.create');
        Route::post('reviews/store', [CustomerReviewController::class, 'store'])->name('admin.reviews.store');
        Route::get('reviews/edit/{id}', [CustomerReviewController::class, 'edit'])->name('admin.reviews.edit');
        Route::delete('reviews/{id}', [CustomerReviewController::class, 'destroy'])->name('admin.reviews.destroy');
        Route::get('getDataReviews', [CustomerReviewController::class, 'getDataReviews'])->name('getDataReviews');
        Route::get('reviews/status/{id}/{status}', [CustomerReviewController::class, 'changeStatus'])->name('admin.reviews.changeStatus');
        Route::get('reviews/priority-status/{id}/{status}', [CustomerReviewController::class, 'changePopularStatus'])->name('admin.reviews.changePopularStatus');
        Route::get('reviews-view/{id}', [CustomerReviewController::class, 'view']);

        // Blogs
        Route::get('blogs', [BlogController::class, 'index'])->name('admin.blogs.index');
        Route::get('blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create');
        Route::get('blogs/edit/{id}', [BlogController::class, 'edit'])->name('admin.blogs.edit');
        Route::post('blogs/store', [BlogController::class, 'store'])->name('admin.blogs.store');
        Route::get('getDataBlogs', [BlogController::class, 'getDataBlogs'])->name('admin.blogs.getDataBlogs');
        Route::delete('blogs/{id}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy');
        Route::get('blogs/status/{id}/{status}', [BlogController::class, 'changeStatus'])->name('admin.blogs.changeStatus');
        Route::get('blogs/priority-status/{id}/{status}', [BlogController::class, 'changeFeaturedStatus'])->name('admin.blogs.changeFeaturedStatus');
        Route::get('blogs-view/{id}', [BlogController::class, 'view']);

        // Hirings
        Route::get('hirings', [HiringController::class, 'index'])->name('admin.hirings.index');
        Route::get('hirings/create', [HiringController::class, 'create'])->name('admin.hirings.create');
        Route::post('hirings/store', [HiringController::class, 'store'])->name('admin.hirings.store');
        Route::get('hirings/edit/{id}', [HiringController::class, 'edit'])->name('admin.hirings.edit');
        Route::delete('hirings/{id}', [HiringController::class, 'destroy'])->name('admin.hirings.destroy');
        Route::get('getDataHirings', [HiringController::class, 'getDataHirings'])->name('getDataHirings');
        Route::get('hirings/status/{id}/{status}', [HiringController::class, 'changeStatus'])->name('admin.hirings.changeStatus');
        Route::get('hirings/priority-status/{id}/{status}', [HiringController::class, 'changePopularStatus'])->name('admin.hirings.changePopularStatus');
        Route::get('hirings-view/{id}', [HiringController::class, 'view']);

        /* Home Counters Routes */
        Route::get('home-counters', [HomeCounterController::class, 'index'])->name('admin.home-counters.index');
        Route::get('home-counters/create', [HomeCounterController::class, 'create'])->name('admin.home-counters.create');
        Route::post('home-counters/store', [HomeCounterController::class, 'store'])->name('admin.home-counters.store');
        Route::delete('home-counters/{id}', [HomeCounterController::class, 'destroy'])->name('admin.home-counters.destroy');
        Route::get('home-counters/edit/{id}', [HomeCounterController::class, 'edit'])->name('admin.home-counters.edit');
        Route::get('getDataHomeCounters', [HomeCounterController::class, 'getDataHomeCounters'])->name('admin.home-counters.data');
        Route::get('home-counters/status/{id}/{status}', [HomeCounterController::class, 'changeStatus'])->name('admin.home-counters.status');
        Route::get('home-counters/priority-status/{id}/{status}', [HomeCounterController::class, 'changePriorityStatus'])->name('admin.home-counters.priority-status');

        /* FAQs Routes */
        Route::get('faqs', [FaqController::class, 'index'])->name('admin.faqs.index');
        Route::get('faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
        Route::post('faqs/store', [FaqController::class, 'store'])->name('admin.faqs.store');
        Route::get('faqs/edit/{id}', [FaqController::class, 'edit'])->name('admin.faqs.edit');
        Route::post('faqs/update/{id}', [FaqController::class, 'update'])->name('admin.faqs.update');
        Route::delete('faqs/{id}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');
        Route::get('getDataFaqs', [FaqController::class, 'getDataFaqs'])->name('admin.faqs.data');
        Route::get('faqs/status/{id}/{status}', [FaqController::class, 'changeStatus'])->name('admin.faqs.status');

        /* city Route */
        Route::get('city', [CityController::class, 'index'])->name('admin.city.index');
        Route::get('city/create', [CityController::class, 'create'])->name('admin.city.create');
        Route::post('city/store', [CityController::class, 'store']);
        Route::delete('city/{id}', [CityController::class, 'destroy']);
        Route::get('city/edit/{id}', [CityController::class, 'edit'])->name('admin.city.edit');
        Route::get('getDataCity', [CityController::class, 'getDataCity'])->name('getDataCity');
        Route::get('city/status/{id}/{status}', [CityController::class, 'changeStatus']);
        Route::get('city/priority-status/{id}/{status}', [CityController::class, 'changePriorityStatus'])->name('admin.city.priority-status');

        /* Setting Route */
        Route::get('setting', [SettingController::class, 'index'])->name('admin.setting.index');
        Route::get('setting/create', [SettingController::class, 'create'])->name('admin.setting.create');
        Route::post('setting/store', [SettingController::class, 'store']);
        Route::delete('setting/{id}', [SettingController::class, 'destroy']);
        Route::get('setting/edit/{id}', [SettingController::class, 'edit'])->name('admin.setting.edit');
        Route::get('getDataSetting', [SettingController::class, 'getDataSetting'])->name('getDataSetting');
        Route::get('setting/status/{id}/{status}', [SettingController::class, 'changeStatus']);

        /* App Setting Route */
        Route::get('app-setting', [AppSettingController::class, 'index'])->name('admin.app-setting.index');
        Route::get('app-setting/create', [AppSettingController::class, 'create'])->name('admin.app-setting.create');
        Route::post('app-setting/store', [AppSettingController::class, 'store']);
        Route::delete('app-setting/{id}', [AppSettingController::class, 'destroy']);
        Route::get('app-setting/edit/{id}', [AppSettingController::class, 'edit'])->name('admin.app-setting.edit');
        Route::get('getDataAppSetting', [AppSettingController::class, 'getDataSetting'])->name('getDataAppSetting');
        Route::get('app-setting/status/{id}/{status}', [AppSettingController::class, 'changeStatus']);

        /* Contact Submissions Route */
        Route::get('contact-submissions', [ContactSubmissionsController::class, 'index'])->name('admin.contact-submissions.index');
        Route::get('getDataContactSubmissions', [ContactSubmissionsController::class, 'getDataContactSubmissions'])->name('getDataContactSubmissions');
        Route::get('contact-submissions/status/{id}/{status}', [ContactSubmissionsController::class, 'changeStatus']);
        Route::delete('contact-submissions/{id}', [ContactSubmissionsController::class, 'destroy']);
        Route::get('contact-submissions-view/{id}', [ContactSubmissionsController::class, 'view']);

        /* Appointments Route */
        Route::get('appointments', [AppointmentsController::class, 'index'])->name('admin.appointments.index');
        Route::get('appointments/export', [AppointmentsController::class, 'export'])->name('admin.appointments.export');
        Route::get('appointments/create', [AppointmentsController::class, 'create'])->name('admin.appointments.create');
        Route::post('appointments/store', [AppointmentsController::class, 'store']);
        Route::get('appointments/edit/{id}', [AppointmentsController::class, 'edit'])->name('admin.appointments.edit');
        Route::get('getDataAppointments', [AppointmentsController::class, 'getDataAppointments'])->name('getDataAppointments');
        Route::get('appointments/status/{id}/{status}', [AppointmentsController::class, 'changeStatus']);
        Route::delete('appointments/{id}', [AppointmentsController::class, 'destroy']);
        Route::post('appointments/assign_member', [AppointmentsController::class, 'AssignMember'])->name('assign.members');
        Route::post('appointments/update-amount', [AppointmentsController::class, 'updateAmount'])->name('admin.appointments.updateAmount');
        Route::post('appointments/update-payment-type', [AppointmentsController::class, 'updatePaymentType'])->name('admin.appointments.updatePaymentType');
        Route::get('appointments-view/{id}', [AppointmentsController::class, 'view']);
        Route::get('appointments/get-appoinmentSubcategories/{categoryId}', [AppointmentsController::class, 'getSubcategories']);
        Route::get('appointments/{id}/pdf', [AppointmentsController::class, 'downloadPdf'])
            ->name('admin.appointments.pdf');
        Route::get('get-city-services/{cityId}', [AppointmentsController::class, 'getCityServices']);



        /* Policies Route */
        Route::get('policies', [PoliciesController::class, 'createOrUpdate'])->name('admin.policies.index');
        Route::post('policies/store', [PoliciesController::class, 'store']);

        /* Coupon Codes Route */
        Route::get('coupon-codes', [CouponCodeController::class, 'index'])->name('admin.coupon-codes.index');
        Route::get('coupon-codes/create', [CouponCodeController::class, 'create'])->name('admin.coupon-codes.create');
        Route::post('coupon-codes/store', [CouponCodeController::class, 'store'])->name('admin.coupon-codes.store');
        Route::get('coupon-codes/edit/{id}', [CouponCodeController::class, 'edit'])->name('admin.coupon-codes.edit');
        Route::get('getDataCouponCodes', [CouponCodeController::class, 'getDataCouponCodes'])->name('getDataCouponCodes');
        Route::get('coupon-codes/status/{id}/{status}', [CouponCodeController::class, 'changeStatus']);
        Route::delete('coupon-codes/{id}', [CouponCodeController::class, 'destroy']);
        Route::get('coupon-codes-view/{id}', [CouponCodeController::class, 'show']);

        /* Coupon Usage Route */
        Route::get('coupon-usage', [CouponUsageController::class, 'index'])->name('admin.coupon-usage.index');
        Route::get('getDataCouponUsages', [CouponUsageController::class, 'getDataCouponUsages'])->name('getDataCouponUsages');
        Route::delete('coupon-usage/{id}', [CouponUsageController::class, 'destroy']);

        /* Attendance/Availability Route */
        Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
        Route::post('attendance/store', [AttendanceController::class, 'store'])->name('admin.attendance.store');
        Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');

        /* Membership Routes */
        Route::get('membership', [MembershipPlanController::class, 'index'])->name('admin.membership.index');
        Route::get('membership/create', [MembershipPlanController::class, 'create'])->name('admin.membership.create');
        Route::get('membership/edit/{id}', [MembershipPlanController::class, 'edit'])->name('admin.membership.edit');
        Route::post('membership/store', [MembershipPlanController::class, 'store'])->name('admin.membership.store');
        Route::get('getDataMembership', [MembershipPlanController::class, 'getData'])->name('getDataMembership');
        Route::get('membership/status/{id}/{status}', [MembershipPlanController::class, 'changeStatus'])->name('admin.membership.changeStatus');
        Route::delete('membership/{id}', [MembershipPlanController::class, 'destroy'])->name('admin.membership.destroy');

        /* Combo Routes */
        Route::get('combo', [ServiceComboController::class, 'index'])->name('admin.combo.index');
        Route::get('combo/create', [ServiceComboController::class, 'create'])->name('admin.combo.create');
        Route::get('combo/edit/{id}', [ServiceComboController::class, 'edit'])->name('admin.combo.edit');
        Route::post('combo/store', [ServiceComboController::class, 'store'])->name('admin.combo.store');
        Route::get('getDataCombo', [ServiceComboController::class, 'getData'])->name('getDataCombo');
        Route::delete('combo/{id}', [ServiceComboController::class, 'destroy'])->name('admin.combo.destroy');

        /* App Service City Master Routes */
        Route::get('service-city-master', [ServiceCityMasterController::class, 'index'])->name('admin.service-city-master.index');
        Route::get('service-city-master/create', [ServiceCityMasterController::class, 'create'])->name('admin.service-city-master.create');
        Route::get('service-city-master/edit/{id}', [ServiceCityMasterController::class, 'edit'])->name('admin.service-city-master.edit');
        Route::post('service-city-master/store', [ServiceCityMasterController::class, 'store'])->name('admin.service-city-master.store');
        Route::get('getDataServiceCityMaster', [ServiceCityMasterController::class, 'getData'])->name('getDataServiceCityMaster');
        Route::delete('service-city-master/{id}', [ServiceCityMasterController::class, 'destroy'])->name('admin.service-city-master.destroy');
        Route::get('service-city-master/subcategories/{categoryId}', [ServiceCityMasterController::class, 'getSubcategories']);
        Route::get('service-city-master/get-services-by-category', [ServiceCityMasterController::class, 'getServiceMastersByCategory'])->name('admin.service-city-master.services-by-category');

        /* Notification Routes */
        Route::get('notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
        Route::post('notifications/store', [NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::get('getDataNotifications', [NotificationController::class, 'getData'])->name('getDataNotifications');

        /* Razorpay Transaction Routes */
        Route::get('razorpay', [RazorpayTransactionController::class, 'index'])->name('admin.razorpay.index');
        Route::get('getDataRazorpay', [RazorpayTransactionController::class, 'getData'])->name('getDataRazorpay');
        Route::get('razorpay/{id}', [RazorpayTransactionController::class, 'show'])->name('admin.razorpay.show');
    });
});
