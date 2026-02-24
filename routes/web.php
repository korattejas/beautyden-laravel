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
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\ContractSignedController;
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

        /* Contact Submissions Route */
        Route::get('contact-submissions', [ContactSubmissionsController::class, 'index'])->name('admin.contact-submissions.index');
        Route::get('getDataContactSubmissions', [ContactSubmissionsController::class, 'getDataContactSubmissions'])->name('getDataContactSubmissions');
        Route::get('contact-submissions/status/{id}/{status}', [ContactSubmissionsController::class, 'changeStatus']);
        Route::delete('contact-submissions/{id}', [ContactSubmissionsController::class, 'destroy']);
        Route::get('contact-submissions-view/{id}', [ContactSubmissionsController::class, 'view']);

        /* Appointments Route */
        Route::get('appointments', [AppointmentsController::class, 'index'])->name('admin.appointments.index');
        Route::get('appointments/create', [AppointmentsController::class, 'create'])->name('admin.appointments.create');
        Route::post('appointments/store', [AppointmentsController::class, 'store']);
        Route::get('appointments/edit/{id}', [AppointmentsController::class, 'edit'])->name('admin.appointments.edit');
        Route::get('getDataAppointments', [AppointmentsController::class, 'getDataAppointments'])->name('getDataAppointments');
        Route::get('appointments/status/{id}/{status}', [AppointmentsController::class, 'changeStatus']);
        Route::delete('appointments/{id}', [AppointmentsController::class, 'destroy']);
        Route::post('appointments/assign_member', [AppointmentsController::class, 'AssignMember'])->name('assign.members');
        Route::get('appointments-view/{id}', [AppointmentsController::class, 'view']);
        Route::get('appointments/get-appoinmentSubcategories/{categoryId}', [AppointmentsController::class, 'getSubcategories']);
        Route::get('appointments/{id}/pdf', [AppointmentsController::class, 'downloadPdf'])
            ->name('admin.appointments.pdf');
        Route::get('get-city-services/{cityId}', [AppointmentsController::class, 'getCityServices']);



        /* Policies Route */
        Route::get('policies', [PoliciesController::class, 'createOrUpdate'])->name('admin.policies.index');
        Route::post('policies/store', [PoliciesController::class, 'store']);
    });
});
