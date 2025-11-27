<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HospitalPhoto;
use App\Http\Controllers\Controller;
use App\Models\HospitalBusinessHour;
use Illuminate\Support\Facades\File;

class AdminHospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hospitals = Hospital::latest()->get();
        return view('backend.admins.pages.hospitals', compact('hospitals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admins.pages.hospital-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hospitals,email',
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'emergency_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'google_map' => 'nullable|url',
            'hospital_registration_number' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
            'established_date' => 'nullable|date',
            'number_of_beds' => 'nullable|string|max:10',
            'number_of_doctors' => 'nullable|string|max:10',
            'number_of_nurses' => 'nullable|string|max:10',
            'number_of_departments' => 'nullable|string|max:10',
            'hospital_type' => 'nullable|string|max:100',
            'ownership_type' => 'nullable|string|max:100',
            'accreditations' => 'nullable|string|max:500',
            'status' => 'boolean',
            'is_popular' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean'
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $uploadPath = public_path('uploads/hospitals');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . Str::slug($request->name) . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);
            $logoPath = 'uploads/hospitals/' . $logoName;
        }

        Hospital::create([
            'hospital_id' => 'HOSP' . date('Ymd') . str_pad(Hospital::count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'whatsapp_number' => $request->whatsapp_number,
            'emergency_number' => $request->emergency_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'website' => $request->website,
            'description' => $request->description,
            'logo' => $logoPath,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'google_map' => $request->google_map,
            'hospital_registration_number' => $request->hospital_registration_number,
            'license_number' => $request->license_number,
            'established_date' => $request->established_date,
            'number_of_beds' => $request->number_of_beds,
            'number_of_doctors' => $request->number_of_doctors,
            'number_of_nurses' => $request->number_of_nurses,
            'number_of_departments' => $request->number_of_departments,
            'hospital_type' => $request->hospital_type,
            'ownership_type' => $request->ownership_type,
            'accreditations' => $request->accreditations,
            'status' => $request->status ?? 1,
            'is_popular' => $request->is_popular ?? 0,
            'is_featured' => $request->is_featured ?? 0,
            'is_verified' => $request->is_verified ?? 0
        ]);

        return redirect()->route('admins.hospitals.index')
            ->with('success', 'Hospital created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hospital = Hospital::findOrFail($id);
        // dd($hospital->isOpenNow());

        return view('backend.admins.pages.hospital-show', compact('hospital'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hospital = Hospital::findOrFail($id);
        return view('backend.admins.pages.hospital-edit', compact('hospital'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hospital = Hospital::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hospitals,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'emergency_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'google_map' => 'nullable|url',
            'hospital_registration_number' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
            'established_date' => 'nullable|date',
            'number_of_beds' => 'nullable|string|max:10',
            'number_of_doctors' => 'nullable|string|max:10',
            'number_of_nurses' => 'nullable|string|max:10',
            'number_of_departments' => 'nullable|string|max:10',
            'hospital_type' => 'nullable|string|max:100',
            'ownership_type' => 'nullable|string|max:100',
            'accreditations' => 'nullable|string|max:500',
            'status' => 'boolean',
            'is_popular' => 'boolean',
            'is_featured' => 'boolean',
            'is_verified' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'whatsapp_number' => $request->whatsapp_number,
            'emergency_number' => $request->emergency_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'website' => $request->website,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'google_map' => $request->google_map,
            'hospital_registration_number' => $request->hospital_registration_number,
            'license_number' => $request->license_number,
            'established_date' => $request->established_date,
            'number_of_beds' => $request->number_of_beds,
            'number_of_doctors' => $request->number_of_doctors,
            'number_of_nurses' => $request->number_of_nurses,
            'number_of_departments' => $request->number_of_departments,
            'hospital_type' => $request->hospital_type,
            'ownership_type' => $request->ownership_type,
            'accreditations' => $request->accreditations,
            'status' => $request->status ?? 1,
            'is_popular' => $request->is_popular ?? 0,
            'is_featured' => $request->is_featured ?? 0,
            'is_verified' => $request->is_verified ?? 0
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($hospital->logo && File::exists(public_path($hospital->logo))) {
                File::delete(public_path($hospital->logo));
            }

            $uploadPath = public_path('uploads/hospitals');
            $logo = $request->file('logo');
            $logoName = time() . '_' . Str::slug($request->name) . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadPath, $logoName);
            $data['logo'] = 'uploads/hospitals/' . $logoName;
        }

        $hospital->update($data);

        return redirect()->route('admins.hospitals.index')
            ->with('success', 'Hospital updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hospital = Hospital::findOrFail($id);

        // Delete logo
        if ($hospital->logo && File::exists(public_path($hospital->logo))) {
            File::delete(public_path($hospital->logo));
        }

        $hospital->delete();

        return redirect()->route('admins.hospitals.index')
            ->with('success', 'Hospital deleted successfully.');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            $status = $request->status == '1' || $request->status === 1 || $request->status === true;

            $hospital->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update popular status via AJAX
     */
    public function updatePopular(Request $request, string $id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            $isPopular = $request->is_popular == '1' || $request->is_popular === 1 || $request->is_popular === true;

            $hospital->update(['is_popular' => $isPopular]);

            return response()->json([
                'success' => true,
                'message' => 'Popular status updated successfully.',
                'is_popular' => $isPopular
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating popular status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update featured status via AJAX
     */
    public function updateFeatured(Request $request, string $id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            $isFeatured = $request->is_featured == '1' || $request->is_featured === 1 || $request->is_featured === true;

            $hospital->update(['is_featured' => $isFeatured]);

            return response()->json([
                'success' => true,
                'message' => 'Featured status updated successfully.',
                'is_featured' => $isFeatured
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating featured status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update verified status via AJAX
     */
    public function updateVerified(Request $request, string $id)
    {
        try {
            $hospital = Hospital::findOrFail($id);
            $isVerified = $request->is_verified == '1' || $request->is_verified === 1 || $request->is_verified === true;

            $hospital->update(['is_verified' => $isVerified]);

            return response()->json([
                'success' => true,
                'message' => 'Verified status updated successfully.',
                'is_verified' => $isVerified
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating verified status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function businessHours(string $id)
    {
        $hospital = Hospital::with('businessHours')->findOrFail($id);

        // Default business hours agar nahi hain toh create karein
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (!$hospital->businessHours->where('day', $day)->first()) {
                HospitalBusinessHour::create([
                    'hospital_id' => $hospital->id,
                    'day' => $day,
                    'open_time' => '09:00',
                    'close_time' => '17:00',
                    'is_closed' => ($day === 'sunday'),
                    'is_emergency_24_7' => false
                ]);
            }
        }

        // Refresh with new business hours
        $hospital->load('businessHours');

        return view('backend.admins.pages.hospital-business-hours', compact('hospital'));
    }

    /**
     * Update business hours
     */
    public function updateBusinessHours(Request $request, string $id)
    {
        $hospital = Hospital::findOrFail($id);

        $request->validate([
            'business_hours' => 'required|array',
            'business_hours.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'business_hours.*.open_time' => 'nullable|date_format:H:i',
            'business_hours.*.close_time' => 'nullable|date_format:H:i',
            'business_hours.*.is_closed' => 'boolean',
            'business_hours.*.is_emergency_24_7' => 'boolean'
        ]);

        foreach ($request->business_hours as $businessHourData) {
            // Agar closed ya emergency hai toh open_time aur close_time null karen
            $openTime = null;
            $closeTime = null;

            if (!($businessHourData['is_closed'] ?? false) && !($businessHourData['is_emergency_24_7'] ?? false)) {
                $openTime = $businessHourData['open_time'] ?? null;
                $closeTime = $businessHourData['close_time'] ?? null;
            }

            HospitalBusinessHour::updateOrCreate(
                [
                    'hospital_id' => $hospital->id,
                    'day' => $businessHourData['day']
                ],
                [
                    'open_time' => $openTime,
                    'close_time' => $closeTime,
                    'is_closed' => $businessHourData['is_closed'] ?? false,
                    'is_emergency_24_7' => $businessHourData['is_emergency_24_7'] ?? false
                ]
            );
        }

        return redirect()->route('admins.hospitals.business-hours', $hospital->id)
            ->with('success', 'Business hours updated successfully.');
    }


    public function photos(string $id)
    {
        $hospital = Hospital::with([
            'photos' => function ($query) {
                $query->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return view('backend.admins.pages.hospital-photos', compact('hospital'));
    }

    /**
     * Store photos
     */
    public function storePhotos(Request $request, string $id)
    {
        $hospital = Hospital::findOrFail($id);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255'
        ]);

        $uploadPath = public_path('uploads/hospitals/gallery');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            $photoName = time() . '_' . Str::slug($hospital->name) . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move($uploadPath, $photoName);
            $photoPath = 'uploads/hospitals/gallery/' . $photoName;

            $caption = $request->captions[$index] ?? null;

            $hospitalPhoto = HospitalPhoto::create([
                'hospital_id' => $hospital->id,
                'photo_path' => $photoPath,
                'caption' => $caption,
                'sort_order' => HospitalPhoto::where('hospital_id', $hospital->id)->count(),
                'is_primary' => false, // Default false, manually set karna hoga
                'status' => true
            ]);

            $uploadedPhotos[] = $hospitalPhoto;
        }

        // Agar pehli photo upload ho rahi hai toh use primary bana dein
        if ($hospital->photos()->count() === count($uploadedPhotos)) {
            $uploadedPhotos[0]->setAsPrimary();
        }

        return redirect()->route('admins.hospitals.photos', $hospital->id)
            ->with('success', 'Photos uploaded successfully.');
    }

    /**
     * Set photo as primary
     */
    public function setPrimaryPhoto(Request $request, string $hospitalId, string $photoId)
    {
        $hospital = Hospital::findOrFail($hospitalId);
        $photo = HospitalPhoto::where('hospital_id', $hospitalId)->findOrFail($photoId);

        $photo->setAsPrimary();

        return response()->json([
            'success' => true,
            'message' => 'Photo set as primary successfully.'
        ]);
    }

    /**
     * Update photo caption
     */
    public function updatePhotoCaption(Request $request, string $hospitalId, string $photoId)
    {
        $hospital = Hospital::findOrFail($hospitalId);
        $photo = HospitalPhoto::where('hospital_id', $hospitalId)->findOrFail($photoId);

        $request->validate([
            'caption' => 'nullable|string|max:255'
        ]);

        $photo->update([
            'caption' => $request->caption
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caption updated successfully.'
        ]);
    }

    /**
     * Update photo sort order
     */
    public function updatePhotoOrder(Request $request, string $hospitalId)
    {
        $hospital = Hospital::findOrFail($hospitalId);

        $request->validate([
            'photo_order' => 'required|array',
            'photo_order.*' => 'exists:hospital_photos,id'
        ]);

        foreach ($request->photo_order as $order => $photoId) {
            HospitalPhoto::where('hospital_id', $hospitalId)
                ->where('id', $photoId)
                ->update(['sort_order' => $order]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo order updated successfully.'
        ]);
    }

    /**
     * Delete photo
     */
    public function deletePhoto(string $hospitalId, string $photoId)
    {
        $hospital = Hospital::findOrFail($hospitalId);
        $photo = HospitalPhoto::where('hospital_id', $hospitalId)->findOrFail($photoId);

        // Photo file delete karein
        if (File::exists(public_path($photo->photo_path))) {
            File::delete(public_path($photo->photo_path));
        }

        $wasPrimary = $photo->is_primary;
        $photo->delete();

        // Agar primary photo delete ki hai toh naya primary set karein
        if ($wasPrimary) {
            $newPrimary = HospitalPhoto::where('hospital_id', $hospitalId)
                ->active()
                ->first();
            if ($newPrimary) {
                $newPrimary->setAsPrimary();
            }
        }

        return redirect()->route('admins.hospitals.photos', $hospital->id)
            ->with('success', 'Photo deleted successfully.');
    }

    /**
     * Toggle photo status
     */
    public function togglePhotoStatus(Request $request, string $hospitalId, string $photoId)
    {
        $hospital = Hospital::findOrFail($hospitalId);
        $photo = HospitalPhoto::where('hospital_id', $hospitalId)->findOrFail($photoId);

        $photo->update([
            'status' => !$photo->status
        ]);

        // Agar primary photo disable ki hai toh naya primary set karein
        if ($photo->is_primary && !$photo->status) {
            $newPrimary = HospitalPhoto::where('hospital_id', $hospitalId)
                ->active()
                ->first();
            if ($newPrimary) {
                $newPrimary->setAsPrimary();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo status updated successfully.',
            'status' => $photo->status
        ]);
    }


    public function manageDepartments(Hospital $hospital)
    {
        $allDepartments = Department::where('status', true)->get();
        $hospitalDepartments = $hospital->departments()->with([
            'doctors' => function ($query) use ($hospital) {
                $query->wherePivot('hospital_id', $hospital->id);
            }
        ])->get();

        $availableDoctors = Doctor::where('status', true)->get();

        return view('backend.admins.pages.hospitals-manage-departments', compact(
            'hospital',
            'allDepartments',
            'hospitalDepartments',
            'availableDoctors'
        ));
    }

    /**
     * Store departments to hospital
     */
    public function storeDepartments(Request $request, Hospital $hospital)
    {
        $request->validate([
            'departments' => 'required|array',
            'departments.*' => 'exists:department,id'
        ]);

        // dd($request->all());

        try {
            $hospital->departments()->syncWithoutDetaching($request->departments);

            return response()->json([
                'success' => true,
                'message' => 'Departments added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding departments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign doctors to department in hospital
     */
    public function assignDoctors(Request $request, Hospital $hospital, Department $department)
    {
        $request->validate([
            'doctors' => 'required|array',
            'doctors.*' => 'exists:doctors,id'
        ]);

        try {
            // Attach doctors with hospital and department
            foreach ($request->doctors as $doctorId) {
                $hospital->doctors()->syncWithoutDetaching([
                    $doctorId => ['department_id' => $department->id]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Doctors assigned successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning doctors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove department from hospital
     */
    public function removeDepartment(Hospital $hospital, Department $department)
    {
        try {
            $hospital->departments()->detach($department->id);

            return response()->json([
                'success' => true,
                'message' => 'Department removed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing department: ' . $e->getMessage()
            ], 500);
        }
    }

}
