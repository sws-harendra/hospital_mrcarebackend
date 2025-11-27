<?php

namespace App\Http\Controllers\Api;

use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\HospitalReview;
use App\Http\Controllers\Controller;

class HospitalController extends Controller
{
    public function apiShow($hospital_id)
    {
        // 1. Fetch the main hospital data with necessary relationships
        $hospital = Hospital::with([
            'photos',
            'businessHours',
            'departments',
            'reviews',
            'doctors' => function ($q) {
                $q->with('photos', 'departments');
            }
        ])->where('hospital_id', $hospital_id)->first();

        if (!$hospital) {
            return response()->json(['message' => 'Hospital not found.'], 404);
        }

        // 2. Calculate reviews and average rating
        $averageRating = round($hospital->reviews->avg('rating'), 1);

        // 3. Prepare doctors grouped by department for API consumption (same structure as blade JS needed)
        $departmentDoctors = [];
        foreach ($hospital->departments as $department) {
            $doctors = $hospital->doctors
                ->filter(function ($doctor) use ($department) {
                    return $doctor->departments->contains($department->id);
                })
                ->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'specialization' => $doctor->specialization,
                        'experience' => $doctor->experience,
                        'profile_image' => $doctor->profile_image,
                        'doctor_id' => $doctor->doctor_id,
                    ];
                })->toArray();
                
            $departmentDoctors[$department->id] = $doctors;
        }

        // 4. Fetch Similar Hospitals Logic (Same as before)
        $city = $hospital->city;
        $state = $hospital->state;
        $hospitalType = $hospital->hospital_type;
        $departmentNames = $hospital->departments->pluck('name')->toArray();

        $similarHospitals = Hospital::where('status', 1)
            ->where('id', '!=', $hospital->id) 
            ->where(function ($query) use ($hospital, $city, $state, $hospitalType) {
                if ($hospitalType) {
                    $query->orWhere('hospital_type', $hospitalType);
                }
                if ($city) {
                    $query->orWhere('city', 'LIKE', '%' . $city . '%');
                }
                if ($state) {
                    $query->orWhere('state', 'LIKE', '%' . $state . '%');
                }
                if ($hospital->address) {
                    $addressParts = explode(',', $hospital->address);
                    $firstAddressPart = trim($addressParts[0]);
                    $query->orWhere('address', 'LIKE', '%' . $firstAddressPart . '%');
                }
                $nameParts = explode(' ', $hospital->name);
                $query->orWhere('name', 'LIKE', '%' . $nameParts[0] . '%');
            })
            ->orWhereHas('departments', function ($q) use ($departmentNames) {
                $q->whereIn('name', $departmentNames);
            })
            ->distinct()
            ->with(['photos', 'reviews']) 
            ->take(6) 
            ->get()
            ->map(function ($sim) {
                $sim->averageRating = $sim->reviews ? round($sim->reviews->avg('rating'), 1) : 0;
                $sim->unsetRelation('reviews'); // Clean up response
                return $sim;
            });

        return response()->json([
            'hospital' => $hospital,
            'average_rating' => $averageRating,
            'reviews_count' => $hospital->reviews->count(),
            'department_doctors' => $departmentDoctors,
            'similar_hospitals' => $similarHospitals,
        ]);
    }


    /**
     * API: Store a new review for a hospital. (Protected endpoint)
     */
    public function apiStoreReview(Request $request, $hospital_id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        


        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();

        // Prevent duplicate review by email (Good business logic)
        $exists = HospitalReview::where('hospital_id', $hospital->id)->where('email', $request->email)->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already submitted a review for this hospital.'], 409);
        }

        // Upload review image logic (Simplified for API response)
        $imagePath = null;
        // In a real API, you would use Storage::put, not public_path move.
        if ($request->hasFile('image')) {
            // Placeholder: Use actual storage logic here
            $imagePath = 'uploads/hospital-reviews/' . time() . '.' . $request->file('image')->extension();
            // $request->file('image')->move(public_path('uploads/hospital-reviews'), $filename); 
        }

        $review = HospitalReview::create([
            'hospital_id' => $hospital->id,
            'name' => $request->name,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Review submitted successfully!', 'review' => $review], 201);
    }
    
    /**
     * API: Get doctors associated with a specific department in a hospital.
     */
    public function apiDepartmentDoctors($hospital_id, $department_id)
    {
        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();
        $department = Department::findOrFail($department_id);

        $doctors = $hospital->doctors()
            ->whereHas('departments', function ($q) use ($department, $hospital) {
                $q->where('department_id', $department->id);
            })
            ->with('photos', 'reviews')
            ->get();
            
        // Map doctors to a simpler format for API response
        $doctorsData = $doctors->map(function($doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'specialization' => $doc->specialization,
                'profile_image' => $doc->profile_image,
                'average_rating' => $doc->reviews->avg('rating') ?? 0,
            ];
        });

        return response()->json([
            'hospital' => $hospital->name,
            'department' => $department->name,
            'doctors' => $doctorsData
        ]);
    }
}
