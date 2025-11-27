<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HomeSlider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = HomeSlider::where('status', 1)->get();

        // Eager load reviews for Doctors and map rating
        $doctors = Doctor::where('status', 1)->where('is_popular', 1)
            ->with('reviews')
            ->get()
            ->map(function ($doctor) {
                $doctor->avg_rating = $doctor->reviews->avg('rating') ?? 0;
                return $doctor;
            });

        // Eager load reviews and photos for Hospitals and map rating
        $hospitals = Hospital::where('status', 1)->where('is_popular', 1)
            ->with('reviews', 'photos')
            ->get()
            ->map(function ($hospital) {
                $hospital->avg_rating = $hospital->reviews->avg('rating') ?? 0;
                // Get the main image path (using model attributes directly)
                $hospital->main_image = $hospital->logo ?? ($hospital->photos->first()->photo_path ?? 'img/hospital-default.jpg');
                return $hospital;
            });

        return response()->json([
            'sliders' => $sliders,
            'doctors' => $doctors,
            'hospitals' => $hospitals,
            'success' => true
        ]);
    }
}
