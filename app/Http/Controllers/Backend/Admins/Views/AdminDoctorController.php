<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Models\Doctor;
use App\Models\DoctorPhoto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DoctorBusinessHour;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class AdminDoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::latest()->get();
        return view('backend.admins.pages.doctors', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admins.pages.doctor-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'doctor_registration_number' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:100',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'services' => 'nullable|array',
            'education' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'google_map' => 'nullable|url',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'is_popular' => 'boolean',
            'consultation_fee' => 'nullable|numeric|min:0'
        ]);

        // Handle profile image upload
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $uploadPath = public_path('uploads/doctors');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $profileImagePath = 'uploads/doctors/' . $imageName;
        }

        // Handle services array
        $services = $request->services ? array_filter($request->services) : null;

        Doctor::create([
            'doctor_id' => 'DOC' . date('Ymd') . str_pad(Doctor::count() + 1, 4, '0', STR_PAD_LEFT),
            'doctor_registration_number' => $request->doctor_registration_number,
            'name' => $request->name,
            'email' => $request->email,
            'specialization' => $request->specialization,
            'qualification' => $request->qualification,
            'experience' => $request->experience,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'age' => $request->age,
            'mobile_number' => $request->mobile_number,
            'whatsapp_number' => $request->whatsapp_number,
            'services' => $services,
            'education' => $request->education,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'bio' => $request->bio,
            'website' => $request->website,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'google_map' => $request->google_map,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'profile_image' => $profileImagePath,
            'status' => $request->status ?? 1,
            'is_popular' => $request->is_popular ?? 1,
            'consultation_fee' => $request->consultation_fee
        ]);

        return redirect()->route('admins.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        return view('backend.admins.pages.doctor-show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        return view('backend.admins.pages.doctor-edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $id,
            'doctor_registration_number' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:100',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'services' => 'nullable|array',
            'education' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'youtube' => 'nullable|url',
            'google_map' => 'nullable|url',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean',
            'is_popular' => 'boolean',
            'consultation_fee' => 'nullable|numeric|min:0'
        ]);

        $data = [
            'doctor_registration_number' => $request->doctor_registration_number,
            'name' => $request->name,
            'email' => $request->email,
            'specialization' => $request->specialization,
            'qualification' => $request->qualification,
            'experience' => $request->experience,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'age' => $request->age,
            'mobile_number' => $request->mobile_number,
            'whatsapp_number' => $request->whatsapp_number,
            'services' => $request->services ? array_filter($request->services) : null,
            'education' => $request->education,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'bio' => $request->bio,
            'website' => $request->website,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'google_map' => $request->google_map,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status ?? 1,
            'is_popular' => $request->is_popular ?? 1,
            'consultation_fee' => $request->consultation_fee
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($doctor->profile_image && File::exists(public_path($doctor->profile_image))) {
                File::delete(public_path($doctor->profile_image));
            }

            $uploadPath = public_path('uploads/doctors');
            $image = $request->file('profile_image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $data['profile_image'] = 'uploads/doctors/' . $imageName;
        }

        $doctor->update($data);

        return redirect()->route('admins.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = Doctor::findOrFail($id);

        // Delete profile image
        if ($doctor->profile_image && File::exists(public_path($doctor->profile_image))) {
            File::delete(public_path($doctor->profile_image));
        }

        $doctor->delete();

        return redirect()->route('admins.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            $status = $request->status == '1' || $request->status === 1 || $request->status === true;

            $doctor->update(['status' => $status]);

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
            $doctor = Doctor::findOrFail($id);
            $isPopular = $request->is_popular == '1' || $request->is_popular === 1 || $request->is_popular === true;

            $doctor->update(['is_popular' => $isPopular]);

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


    public function businessHours(string $id)
    {
        $doctor = Doctor::with('businessHours')->findOrFail($id);

        // Default business hours agar nahi hain toh create karein
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (!$doctor->businessHours->where('day', $day)->first()) {
                DoctorBusinessHour::create([
                    'doctor_id' => $doctor->id,
                    'day' => $day,
                    'open_time' => '09:00',
                    'close_time' => '17:00',
                    'is_closed' => ($day === 'sunday')
                ]);
            }
        }

        // Refresh with new business hours
        $doctor->load('businessHours');

        return view('backend.admins.pages.doctor-business-hours', compact('doctor'));
    }

    /**
     * Update business hours
     */
    public function updateBusinessHours(Request $request, string $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'business_hours' => 'required|array',
            'business_hours.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'business_hours.*.open_time' => 'nullable|date_format:H:i',
            'business_hours.*.close_time' => 'nullable|date_format:H:i',
            'business_hours.*.is_closed' => 'boolean'
        ]);

        foreach ($request->business_hours as $businessHourData) {
            DoctorBusinessHour::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day' => $businessHourData['day']
                ],
                [
                    'open_time' => $businessHourData['is_closed'] ? null : $businessHourData['open_time'],
                    'close_time' => $businessHourData['is_closed'] ? null : $businessHourData['close_time'],
                    'is_closed' => $businessHourData['is_closed'] ?? false
                ]
            );
        }

        return redirect()->route('admins.doctors.business-hours', $doctor->id)
            ->with('success', 'Business hours updated successfully.');
    }


    // Add doctor photo gallery methods (similar to hospitals)
    public function photos(string $id)
    {
        $doctor = Doctor::with([
            'photos' => function ($query) {
                $query->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return view('backend.admins.pages.doctor-photos', compact('doctor'));
    }

    /**
     * Store photos for doctor
     */
    public function storePhotos(Request $request, string $id)
    {
        $doctor = Doctor::findOrFail($id);

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:255'
        ]);

        $uploadPath = public_path('uploads/doctors/gallery');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            $photoName = time() . '_' . Str::slug($doctor->name) . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $photo->move($uploadPath, $photoName);
            $photoPath = 'uploads/doctors/gallery/' . $photoName;

            $caption = $request->captions[$index] ?? null;

            $doctorPhoto = DoctorPhoto::create([
                'doctor_id' => $doctor->id,
                'photo_path' => $photoPath,
                'caption' => $caption,
                'sort_order' => DoctorPhoto::where('doctor_id', $doctor->id)->count(),
                'is_primary' => false,
                'status' => true
            ]);

            $uploadedPhotos[] = $doctorPhoto;
        }

        // If these are the first photos for the doctor, make first uploaded primary
        if ($doctor->photos()->count() === count($uploadedPhotos)) {
            $uploadedPhotos[0]->setAsPrimary();
        }

        return redirect()->route('admins.doctors.photos', $doctor->id)
            ->with('success', 'Photos uploaded successfully.');
    }

    /**
     * Set a doctor photo as primary
     */
    public function setPrimaryPhoto(Request $request, string $doctorId, string $photoId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $photo = DoctorPhoto::where('doctor_id', $doctorId)->findOrFail($photoId);

        $photo->setAsPrimary();

        return response()->json([
            'success' => true,
            'message' => 'Photo set as primary successfully.'
        ]);
    }

    /**
     * Update doctor photo caption
     */
    public function updatePhotoCaption(Request $request, string $doctorId, string $photoId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $photo = DoctorPhoto::where('doctor_id', $doctorId)->findOrFail($photoId);

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
     * Update doctor photo sort order
     */
    public function updatePhotoOrder(Request $request, string $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);

        $request->validate([
            'photo_order' => 'required|array',
            'photo_order.*' => 'exists:doctor_photos,id'
        ]);

        foreach ($request->photo_order as $order => $photoId) {
            DoctorPhoto::where('doctor_id', $doctorId)
                ->where('id', $photoId)
                ->update(['sort_order' => $order]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo order updated successfully.'
        ]);
    }

    /**
     * Delete a doctor photo
     */
    public function deletePhoto(string $doctorId, string $photoId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $photo = DoctorPhoto::where('doctor_id', $doctorId)->findOrFail($photoId);

        if (File::exists(public_path($photo->photo_path))) {
            File::delete(public_path($photo->photo_path));
        }

        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary) {
            $newPrimary = DoctorPhoto::where('doctor_id', $doctorId)
                ->active()
                ->first();
            if ($newPrimary) {
                $newPrimary->setAsPrimary();
            }
        }

        return redirect()->route('admins.doctors.photos', $doctor->id)
            ->with('success', 'Photo deleted successfully.');
    }

    /**
     * Toggle doctor photo status (active/inactive)
     */
    public function togglePhotoStatus(Request $request, string $doctorId, string $photoId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $photo = DoctorPhoto::where('doctor_id', $doctorId)->findOrFail($photoId);

        $photo->update([
            'status' => !$photo->status
        ]);

        if ($photo->is_primary && !$photo->status) {
            $newPrimary = DoctorPhoto::where('doctor_id', $doctorId)
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




}
