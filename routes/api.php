<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\DoctorBookingController;
use App\Http\Controllers\Api\HospitalBookingController;


// Public/Open Routes (e.g., fetching a public list that doesn't need high security)
Route::get('/public/list', function () {
    return ['status' => 'ok'];
});

// Protected Routes (Doctor/Hospital Data)
// Route::middleware('api.key')->group(function () {
    // Example: API to fetch all doctors
    Route::get('/v1/home', [HomeController::class, 'index']);
    Route::get('/v1/doctors/{doctor_id}', [DoctorController::class, 'show']);
    Route::post('/v1/doctor/{doctor_id}/review', [DoctorController::class, 'apiStoreReview']);
    Route::post('/v1/doctor/{doctor_id}/book', [DoctorBookingController::class, 'apiStore']);
  
    // 1. HOSPITAL DETAILS (GET)
    // Fetches all details for a specific hospital by its hospital_id
    Route::get('/v1/hospital/{hospital_id}', [HospitalController::class, 'apiShow']);
    
    // 2. HOSPITAL REVIEWS (POST)
    // Submits a new review for a hospital
    Route::post('/v1/hospital/{hospital_id}/review', [HospitalController::class, 'apiStoreReview']);
    // 3. DOCTORS BY DEPARTMENT (GET)
   
    Route::get('/v1/hospital/{hospital_id}/department/{department_id}/doctors', [HospitalController::class, 'apiDepartmentDoctors']);


    Route::post('/v1/hospital/{hospital_id}/book', [HospitalBookingController::class, 'apiStore']);

    Route::get('/v1/search', [SearchController::class, 'apiUnifiedSearch']);
// });