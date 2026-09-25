<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertyDetailsController;
use App\Http\Controllers\PropertyInquiryController;
use App\Http\Controllers\PropertyListingController;
use App\Http\Controllers\OurTeamController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;

        

Route::get('/', [PropertyListingController::class, 'indexwelcome'])->name('home');
Route::get('/search', [PropertyListingController::class, 'search'])->name('property.search');
Route::post('/properties/{property}/inquiry', [PropertyInquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('property.inquiry.store');
Route::get('/property/{id}', [PropertyDetailsController::class, 'index'])->name('property.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PropertyInquiryController::class, 'storeContact'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/join-us', [PageController::class, 'joinus'])->name('joinus');
Route::post('/join-us', [PropertyInquiryController::class, 'storeJoinUs'])
    ->middleware('throttle:5,1')
    ->name('joinus.store');
Route::get('/associates-us', [PageController::class, 'associatewithus'])->name('associatewithus');
Route::post('/associates-us', [PropertyInquiryController::class, 'storeAssociate'])
    ->middleware('throttle:5,1')
    ->name('associatewithus.store');
Route::get('/about-us', [PageController::class, 'aboutus'])->name('aboutus');
Route::get('/our-team', [PageController::class, 'ourteam'])->name('ourteam');

// Admin routes
Route::group(['prefix' => 'admin'], function () {
    // Routes for guests (login)
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('login', [AdminController::class, 'index'])->name('admin.login');
        Route::post('login', [AdminController::class, 'authenticate'])
            ->middleware('throttle:10,1')
            ->name('admin.authenticate');
    });

    // Routes for authenticated admins
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');

        Route::get('site-settings', [SiteSettingController::class, 'edit'])->name('admin.settings.edit');
        Route::post('site-settings', [SiteSettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('site-settings', [SiteSettingController::class, 'reset'])->name('admin.settings.reset');

        // Add new property
        Route::middleware('check.permission:add_now')->group(function () {
            Route::get('propertylisting', [PropertyListingController::class, 'index'])->name('admin.propertylisting');
            Route::post('propertylisting/store', [PropertyListingController::class, 'store'])->name('admin.propertylisting.store');
        });

        // All properties
        Route::middleware('check.permission:all_property')->group(function () {
            Route::get('properties', [PropertyListingController::class, 'list'])->name('admin.properties.list');
        });

        // Featured properties
        Route::middleware('check.permission:featured_image')->group(function () {
            Route::get('propertiesfeatured', [PropertyListingController::class, 'indexfeatured'])->name('admin.properties.indexfeatured');
            Route::get('propertiesfetured', [PropertyListingController::class, 'indexfetured'])->name('admin.properties.indexfetured');
        });

        // Edit / update / delete a property (reachable from both property lists)
        Route::middleware('check.permission:all_property,featured_image')->group(function () {
            Route::get('properties/{property}/edit', [PropertyListingController::class, 'edit'])->name('admin.properties.edit');
            Route::put('properties/{property}/toggle', [PropertyListingController::class, 'toggleStatus'])->name('admin.properties.toggleStatus');
            Route::put('properties/{property}', [PropertyListingController::class, 'update'])->name('admin.properties.update');
            Route::delete('properties/images/{image}', [PropertyListingController::class, 'deleteImage'])->name('admin.properties.deleteImage');
            Route::delete('properties/{property}', [PropertyListingController::class, 'destroy'])->name('admin.properties.destroy');
        });

        // Inquiries
        Route::middleware('check.permission:property_image')->group(function () {
            Route::get('inquiryformlist', [PropertyInquiryController::class, 'inquiryForm'])->name('admin.inquiryformlist');
            Route::get('enquiryformlist', [PropertyInquiryController::class, 'enquiryForm'])->name('admin.enquiryformlist');
        });

        // User permissions
        Route::middleware('check.permission:manage_users')->group(function () {
            Route::get('/user-permission', [UserPermissionController::class, 'index'])->name('user_permission.index');
            Route::get('/user-permission/create', [UserPermissionController::class, 'create'])->name('user_permission.create');
            Route::post('/user-permission/store', [UserPermissionController::class, 'store'])->name('user_permission.store');
            Route::get('/user-permission/{user}/edit', [UserPermissionController::class, 'edit'])->name('user_permission.edit');
            Route::put('/user-permission/{user}', [UserPermissionController::class, 'update'])->name('user_permission.update');
        });

        // Our team
        Route::middleware('check.permission:our_team')->group(function () {
            Route::get('/our_team', [OurTeamController::class, 'index'])->name('our_team.index');
            Route::get('/our_team/create', [OurTeamController::class, 'create'])->name('our_team.create');
            Route::post('/our_team', [OurTeamController::class, 'store'])->name('our_team.store');
            Route::get('/our_team/{our_team}/edit', [OurTeamController::class, 'edit'])->name('our_team.edit');
            Route::put('/our_team/{our_team}', [OurTeamController::class, 'update'])->name('our_team.update');
            Route::delete('/our_team/{our_team}', [OurTeamController::class, 'destroy'])->name('our_team.destroy');
        });
    });
});
