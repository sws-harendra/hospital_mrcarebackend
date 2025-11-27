<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\HospitalReview;
use App\Http\Controllers\Controller;

class HospitalController extends Controller
{
   public function show($hospital_id)
    {
        // 1. Fetch the main hospital data with necessary relationships (including reviews)
        $hospital = Hospital::with([
            'photos',
            'businessHours',
            'departments',
            'reviews', // FIX: Ensure reviews are loaded for the main hospital
            'doctors' => function ($q) {
                $q->with('photos', 'departments'); // Load departments on doctors too
            }
        ])->where('hospital_id', $hospital_id)->firstOrFail();

        // 2. Calculate reviews and average rating
        $reviews = $hospital->reviews()->latest()->get();
        $averageRating = round($reviews->avg('rating'), 1);

        // 3. Prepare doctors grouped by department (FIXED LOGIC)
        $departmentDoctors = [];
        // Iterate through departments and filter the doctors collection loaded above
        foreach ($hospital->departments as $department) {
            $doctors = $hospital->doctors
                ->filter(function ($doctor) use ($department) {
                    // Check the doctor_department pivot table for the association
                    // Assuming the Doctor model has a departments() relationship
                    return $doctor->departments->contains($department->id);
                })
                ->map(function ($doctor) {
                    // Prepare minimal data for JS
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

        // 4. Fetch Similar Hospitals Logic
        $city = $hospital->city;
        $state = $hospital->state;
        $hospitalType = $hospital->hospital_type;
        $departmentNames = $hospital->departments->pluck('name')->toArray();

        $similarHospitals = Hospital::where('status', 1)
            ->where('id', '!=', $hospital->id) 
            
            // Start broader matching (using OR for flexibility)
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
            // Match based on shared departments
            ->orWhereHas('departments', function ($q) use ($departmentNames) {
                $q->whereIn('name', $departmentNames);
            })
            
            ->distinct()
            
            // Eager load photos and reviews for the similar hospitals
            ->with(['photos', 'reviews']) 
            ->take(6) 
            ->get()
            // Calculate and map the average rating safely
            ->map(function ($sim) {
                // $sim->reviews is guaranteed to be a Collection (or null before the fix, now it's Collection)
                $sim->averageRating = $sim->reviews ? round($sim->reviews->avg('rating'), 1) : 0;
                return $sim;
            });

            // dd($departmentDoctors);
        return view('frontend.pages.hospital-show', compact('hospital', 'reviews', 'averageRating', 'departmentDoctors', 'departmentDoctors', 'similarHospitals'));
    }


    public function storeReview(Request $request, $hospital_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();

        // Prevent duplicate review by email
        $exists = HospitalReview::where('hospital_id', $hospital->id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted a review for this hospital.');
        }

        // Upload review image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
            $destinationPath = public_path('uploads/hospital-reviews');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $image->move($destinationPath, $filename);
            $imagePath = 'uploads/hospital-reviews/' . $filename;
        }

        HospitalReview::create([
            'hospital_id' => $hospital->id,
            'name' => $request->name,
            'email' => $request->email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Thank you for reviewing this hospital!');
    }

    public function departmentDoctors($hospital_id, $department_id)
    {
        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();

        $department = Department::findOrFail($department_id);

        $doctors = Doctor::whereHas('departments', function ($q) use ($department, $hospital) {
            $q->where('department_id', $department->id)
                ->where('hospital_id', $hospital->id);
        })
            ->with('photos')
            ->get();

        return view('frontend.hospital.department-doctors', compact('hospital', 'department', 'doctors'));
    }
}
