<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\DoctorController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\HomePageController;
use App\Http\Controllers\Frontend\HospitalController;
use App\Http\Controllers\Frontend\DoctorBookingController;
use App\Http\Controllers\Backend\Admins\Auth\AdminAuthController;
use App\Http\Controllers\Backend\Admins\Views\AdminDoctorController;
use App\Http\Controllers\Backend\Admins\Views\AdminHospitalController;
use App\Http\Controllers\Backend\Admins\Views\AdminDashboardController;
use App\Http\Controllers\Backend\Admins\Views\AdminDepartmentController;
use App\Http\Controllers\Backend\Admins\Views\AdminHomeSliderController;
use App\Http\Controllers\Backend\Admins\Views\AdminDoctorAppointmentController;
use App\Http\Controllers\Backend\Admins\Views\AdminHospitalAppointmentController;

// Route::get('/', function () {
//     return view('frontend.pages.home');
// });

//frontend routes can be added here
Route::get('/', [HomePageController::class, 'index'])->name('home');
Route::get('/doctor/{doctor_id}', [DoctorController::class, 'show'])->name('doctor.show');
Route::post('/doctor/{doctor_id}/review', [DoctorController::class, 'storeReview'])->name('doctor.review.store');
Route::post('/doctor/{doctor_id}/book', [DoctorBookingController::class, 'store'])->name('doctor.book.store');

Route::get('/hospital/{hospital_id}', [HospitalController::class, 'show'])->name('hospital.show');
Route::post('/hospital/{hospital_id}/review', [HospitalController::class, 'storeReview'])->name('hospital.review.store');
Route::get('/hospital/{hospital_id}/department/{department_id}', [HospitalController::class, 'departmentDoctors'])->name('hospital.department.doctors');
Route::post('/hospital/{hospital_id}/book', [BookingController::class, 'store'])->name('hospital.book.store');

Route::get('/search-results', [SearchController::class, 'unifiedSearch'])->name('unified.search');

// Admin Dashboard Route
Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('store-login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Home Slider Routes (Add this section)
        Route::resource('/home-slider', AdminHomeSliderController::class);
        Route::post('/home-slider/{home_slider}/status', [AdminHomeSliderController::class, 'updateStatus'])->name('home-slider.update-status');


        // Department Routes
        Route::resource('department', AdminDepartmentController::class);
        Route::post('department/{department}/status', [AdminDepartmentController::class, 'updateStatus'])
            ->name('department.update-status');


        // Doctors Routes with AdminDoctorController
        Route::resource('doctors', AdminDoctorController::class);
        Route::post('doctors/{doctor}/status', [AdminDoctorController::class, 'updateStatus'])
            ->name('doctors.update-status');
        Route::post('doctors/{doctor}/popular', [AdminDoctorController::class, 'updatePopular'])
            ->name('doctors.update-popular');

        // Business Hours Routes
        Route::get('doctors/{doctor}/business-hours', [AdminDoctorController::class, 'businessHours'])
            ->name('doctors.business-hours');
        Route::post('doctors/{doctor}/business-hours', [AdminDoctorController::class, 'updateBusinessHours'])
            ->name('doctors.update-business-hours');

        Route::prefix('doctors')->name('doctors.')->group(function () {
            Route::get('{id}/photos', [AdminDoctorController::class, 'photos'])->name('photos');
            Route::post('{id}/photos', [AdminDoctorController::class, 'storePhotos'])->name('photos.store');
            Route::post('{doctorId}/photos/{photoId}/primary', [AdminDoctorController::class, 'setPrimaryPhoto'])->name('photos.primary');
            Route::put('{doctorId}/photos/{photoId}/caption', [AdminDoctorController::class, 'updatePhotoCaption'])->name('photos.caption');
            Route::post('{doctorId}/photos/order', [AdminDoctorController::class, 'updatePhotoOrder'])->name('photos.order');
            Route::get('{doctorId}/photos/{photoId}', [AdminDoctorController::class, 'deletePhoto'])->name('photos.delete');
            Route::post('{doctorId}/photos/{photoId}/toggle', [AdminDoctorController::class, 'togglePhotoStatus'])->name('photos.toggle');
        });


        // Hospitals Routes
        Route::resource('hospitals', AdminHospitalController::class);
        Route::post('hospitals/{hospital}/status', [AdminHospitalController::class, 'updateStatus'])
            ->name('hospitals.update-status');
        Route::post('hospitals/{hospital}/popular', [AdminHospitalController::class, 'updatePopular'])
            ->name('hospitals.update-popular');
        Route::post('hospitals/{hospital}/featured', [AdminHospitalController::class, 'updateFeatured'])
            ->name('hospitals.update-featured');
        Route::post('hospitals/{hospital}/verified', [AdminHospitalController::class, 'updateVerified'])
            ->name('hospitals.update-verified');

        // Business Hours Routes
        Route::get('hospitals/{hospital}/business-hours', [AdminHospitalController::class, 'businessHours'])
            ->name('hospitals.business-hours');
        Route::post('hospitals/{hospital}/business-hours', [AdminHospitalController::class, 'updateBusinessHours'])
            ->name('hospitals.update-business-hours');

        // Hospital Photos Routes
        Route::get('hospitals/{hospital}/photos', [AdminHospitalController::class, 'photos'])
            ->name('hospitals.photos');
        Route::post('hospitals/{hospital}/photos', [AdminHospitalController::class, 'storePhotos'])
            ->name('hospitals.store-photos');
        Route::post('hospitals/{hospital}/photos/{photo}/primary', [AdminHospitalController::class, 'setPrimaryPhoto'])
            ->name('hospitals.set-primary-photo');
        Route::put('hospitals/{hospital}/photos/{photo}/caption', [AdminHospitalController::class, 'updatePhotoCaption'])
            ->name('hospitals.update-photo-caption');
        Route::post('hospitals/{hospital}/photos/order', [AdminHospitalController::class, 'updatePhotoOrder'])
            ->name('hospitals.update-photo-order');
        Route::delete('hospitals/{hospital}/photos/{photo}', [AdminHospitalController::class, 'deletePhoto'])
            ->name('hospitals.delete-photo');
        Route::post('hospitals/{hospital}/photos/{photo}/toggle-status', [AdminHospitalController::class, 'togglePhotoStatus'])
            ->name('hospitals.toggle-photo-status');

        // Hospital Department Management Routes
        Route::get('hospitals/{hospital}/departments', [AdminHospitalController::class, 'manageDepartments'])->name('hospitals.manage-departments');
        Route::post('hospitals/{hospital}/departments', [AdminHospitalController::class, 'storeDepartments'])->name('hospitals.store-departments');
        Route::post('hospitals/{hospital}/departments/{department}/doctors', [AdminHospitalController::class, 'assignDoctors'])->name('hospitals.assign-doctors');
        Route::delete('hospitals/{hospital}/departments/{department}', [AdminHospitalController::class, 'removeDepartment'])->name('hospitals.remove-department');

        // Doctor Appointments Routes
        Route::get('doctor-appointments', [AdminDoctorAppointmentController::class, 'index'])->name('doctor-appointments.index');
        Route::get('doctor-appointments/{appointment}', [AdminDoctorAppointmentController::class, 'show'])->name('doctor-appointments.show');
        Route::put('doctor-appointments/{appointment}/status', [AdminDoctorAppointmentController::class, 'updateStatus'])->name('doctor-appointments.update-status');

        //Hospital Appointments Routes
        // Index Route
        Route::get('hospital-appointments', [AdminHospitalAppointmentController::class, 'index'])->name('hospital-appointments.index');

        // Show Details (using Route Model Binding on 'HospitalAppointment' model)
        Route::get('hospital-appointments/{appointment}', [AdminHospitalAppointmentController::class, 'show'])->name('hospital-appointments.show');

        // Update Status
        Route::put('hospital-appointments/{appointment}/status', [AdminHospitalAppointmentController::class, 'updateStatus'])->name('hospital-appointments.update-status');

    });






});