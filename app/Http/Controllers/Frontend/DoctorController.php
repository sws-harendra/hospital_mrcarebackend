<?php

namespace App\Http\Controllers\frontend;

use App\Models\Doctor;
use App\Models\DoctorReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DoctorController extends Controller
{
    public function show($doctor_id)
    {
        $doctor = Doctor::where('id', $doctor_id)
            ->with(['reviews', 'photos', 'businessHours', 'hospitals.departments', 'departments'])
            ->firstOrFail();
        // dd($doctor);

        // Decode services JSON
        if (is_array($doctor->services)) {
            $doctor->decoded_services = $doctor->services;
        } elseif (is_string($doctor->services)) {
            $decoded = json_decode($doctor->services, true);
            $doctor->decoded_services = is_array($decoded) ? $decoded : [];
        } else {
            $doctor->decoded_services = [];
        }

        // Average rating
        $averageRating = round($doctor->reviews->avg('rating'), 1);

        // Similar doctors: prefer same department(s), fallback to same hospital(s)
        $similarDoctors = collect();

        $deptIds = $doctor->departments ? $doctor->departments->pluck('id')->filter()->values() : collect();
        // dd($deptIds);
        $hospitalIds = $doctor->hospitals ? $doctor->hospitals->pluck('id')->filter()->values() : collect();

        // Get the specialization of the current doctor
        $specialization = $doctor->specialization; // Assuming 'specialization' is a field in the Doctor model

        if ($specialization) {
            $similarDoctors = Doctor::where('doctors.id', '!=', $doctor->id)
                ->where('specialization', 'LIKE', '%' . $specialization . '%')
                ->with('photos')
                ->leftJoin('doctor_reviews', 'doctors.id', '=', 'doctor_reviews.doctor_id')
                ->select(
                    'doctors.id',
                    'doctors.name',
                    'doctors.experience',
                    'doctors.profile_image',
                    \DB::raw('MIN(doctors.address) as address'),
                    \DB::raw('MIN(doctors.city) as city'),
                    \DB::raw('MIN(doctors.state) as state'),
                    \DB::raw('AVG(doctor_reviews.rating) as avg_rating')
                )
                ->groupBy(
                    'doctors.id',
                    'doctors.name',
                    'doctors.experience',
                    'doctors.profile_image'
                )
                ->orderByDesc('experience')
                ->orderByDesc('avg_rating')
                ->limit(6)
                ->get();
        }



        if ($similarDoctors->isEmpty() && $hospitalIds->isNotEmpty()) {
            $similarDoctors = Doctor::where('id', '!=', $doctor->id)
                ->whereHas('hospitals', function ($q) use ($hospitalIds) {
                    $q->whereIn('hospitals.id', $hospitalIds);
                })
                ->with('photos')
                ->limit(6)
                ->get();
        }

        $departmentDoctors = [];

        // ensure we have at most 6 items
        $similarDoctors = $similarDoctors->take(6);
        // dd($similarDoctors);

        return view('frontend.pages.doctors-show', compact('doctor', 'averageRating', 'similarDoctors','departmentDoctors'));  
      }

    public function storeReview(Request $request, $doctor_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $doctor = Doctor::where('doctor_id', $doctor_id)->firstOrFail();

        // Check if email already reviewed this doctor
        $exists = DoctorReview::where('doctor_id', $doctor->id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted a review for this doctor.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
            $destinationPath = public_path('uploads/reviews');

            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $filename);
            $imagePath = 'uploads/reviews/' . $filename;
        }


        DoctorReview::create([
            'doctor_id' => $doctor->id,
            'name' => $request->name,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
}
