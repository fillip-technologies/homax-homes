<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertyDetailsController;
use App\Http\Controllers\PropertyInquiryController;
use App\Http\Controllers\PropertyListingController;
use App\Http\Controllers\OurTeamController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;

        

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::get('/propertydetails', [
    PropertyDetailsController::class,
    'indexdummy'
])->name('propertydetails.index');

Route::get('/searchs', [
    PropertyDetailsController::class,
    'search'
])->name('propertydetails.search');

Route::get('/', [PropertyListingController::class, 'indexwelcome'])->name('home');
Route::get('/search', [PropertyListingController::class, 'search'])->name('property.search');
Route::post('/properties/{property}/inquiry', [PropertyInquiryController::class, 'store'])
    ->name('property.inquiry.store');
Route::get('/property/{id}', [PropertyDetailsController::class, 'index'])->name('property.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/join-us', [PageController::class, 'joinus'])->name('joinus');
Route::get('/associates-us', [PageController::class, 'assosiatewithus'])->name('assosiatewithus');
Route::get('/about-us', [PageController::class, 'aboutus'])->name('aboutus');
Route::get('/our-team', [PageController::class, 'ourteam'])->name('ourteam');

// Admin routes
Route::group(['prefix' => 'admin'], function () {
    // Routes for guests (e.g., login, register)
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('login', [AdminController::class, 'index'])->name('admin.login');
        Route::post('login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
        Route::get('register', [AdminController::class, 'register'])->name('admin.register');
    });

    // Routes for authenticated admins
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('form', [AdminController::class, 'form'])->name('admin.form');
        Route::get('table', [AdminController::class, 'table'])->name('admin.table');
        Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('propertylisting', [PropertyListingController::class, 'index'])->name('admin.propertylisting');
        Route::post('propertylisting/store', [PropertyListingController::class, 'store'])->name('admin.propertylisting.store');
        // Route::get('listofproperties', [PropertyListingController::class, 'list'])->name('admin.listofproperties');

        Route::get('properties', [PropertyListingController::class, 'list'])->name('admin.properties.list');
        Route::get('propertiesfeatured', [PropertyListingController::class, 'indexfetured'])->name('admin.properties.indexfetured');

      
        Route::get('properties/{property}/edit', [PropertyListingController::class, 'edit'])->name('admin.properties.edit');
        Route::put('properties/{property}/toggle', [PropertyListingController::class, 'toggleStatus'])->name('admin.properties.toggleStatus');
        Route::delete('properties/{property}', [PropertyListingController::class, 'destroy'])->name('admin.properties.destroy');

        Route::get('enquiryformlist', [PropertyInquiryController::class, 'enquiryForm'])->name('admin.enquiryformlist');

        // Route::get('/property/{slug}', [PropertyDetailsController::class, 'index'])->name('admin.propertydetails.index');

        Route::put('properties/{property}', [PropertyListingController::class, 'update'])
            ->name('admin.properties.update');

        Route::delete('properties/images/{image}', [PropertyListingController::class, 'deleteImage'])
            ->name('admin.properties.deleteImage');

        Route::get('/user-permission', [UserPermissionController::class, 'index'])->name('user_permission.index');
        Route::get('/user-permission/create', [UserPermissionController::class, 'create'])->name('user_permission.create');
        Route::post('/user-permission/store', [UserPermissionController::class, 'store'])->name('user_permission.store');
        Route::get('/user-permission/{user}/edit', [UserPermissionController::class, 'edit'])->name('user_permission.edit');
        Route::put('/user-permission/{user}', [UserPermissionController::class, 'update'])->name('user_permission.update');


        // Index - List all team members
        Route::get('/our_team', [OurTeamController::class, 'index'])->name('our_team.index');
        // Create - Show form to add a new member
        Route::get('/our_team/create', [OurTeamController::class, 'create'])->name('our_team.create');
        // Store - Save new member
        Route::post('/our_team', [OurTeamController::class, 'store'])->name('our_team.store');
        // Show - View a single member
        Route::get('/our_team/{our_team}', [OurTeamController::class, 'show'])->name('our_team.show');
        // Edit - Show form to edit an existing member
        Route::get('/our_team/{our_team}/edit', [OurTeamController::class, 'edit'])->name('our_team.edit');
        // Update - Save updated member info
        Route::put('/our_team/{our_team}', [OurTeamController::class, 'update'])->name('our_team.update');
        // Destroy - Delete a member
        Route::delete('/our_team/{our_team}', [OurTeamController::class, 'destroy'])->name('our_team.destroy');
    
    });
});
