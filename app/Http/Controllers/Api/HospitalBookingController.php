<?php

namespace App\Http\Controllers\Api;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\HospitalAppointment;
use App\Http\Controllers\Controller;

class HospitalBookingController extends Controller
{
    //
    public function apiStore(Request $request, $hospital_id)
    {
        // Fetch the hospital using the hospital_id (e.g., HOSP12345) from the route parameter
        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();

        // 1. Validation (Laravel handles 422 JSON response automatically)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',

            // Validate department_id, ensure it exists in the hospital's departments
            'department_id' => [
                'required',
                'integer',
                // Checks existence in the hospital_department pivot table for THIS hospital
                Rule::exists('hospital_department', 'department_id')
                    ->where(function ($query) use ($hospital) {
                        return $query->where('hospital_id', $hospital->id);
                    })
            ],

            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);

        // 2. Create Appointment Record
        HospitalAppointment::create([
            'hospital_id' => $hospital->id,
            'department_id' => $validatedData['department_id'], 
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'appointment_date' => $validatedData['appointment_date'],
            'appointment_time' => $validatedData['appointment_time'],
            'message' => $validatedData['message'],
        ]);

        // 3. Success Response (200 OK or 201 Created)
        return response()->json([
            'success' => 'Appointment request submitted successfully! We will contact you shortly.',
        ], 200);
    }
}
