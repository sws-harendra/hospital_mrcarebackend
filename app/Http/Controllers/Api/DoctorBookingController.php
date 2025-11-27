<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DoctorAppointment;
use App\Http\Controllers\Controller;

class DoctorBookingController extends Controller
{
    public function apiStore(Request $request, $doctor_id)
    {
        // Fetch the doctor using the doctor_id (e.g., DOC12345) from the route parameter
        $doctor = Doctor::where('doctor_id', $doctor_id)->firstOrFail(); 

        // 1. Validation 
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Note: hospital_id is now nullable in DB and validated for existence only if provided.
            'hospital_id' => [
                'nullable', // Accepting null/empty for direct doctor appointments
                'integer', 
                // Ensure hospital exists IF an ID is provided
                Rule::exists('hospitals', 'id')
            ],
            
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);

        // 2. Create Appointment Record
        DoctorAppointment::create([
            'doctor_id' => $doctor->id,
            'hospital_id' => $validatedData['hospital_id'] ?? null, // Use null if optional field is empty
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'appointment_date' => $validatedData['appointment_date'],
            'appointment_time' => $validatedData['appointment_time'],
            'message' => $validatedData['message'],
        ]);

        // 3. Success Response (200 OK)
        return response()->json([
            'success' => 'Appointment request submitted successfully! The clinic will contact you.',
        ], 200);
    }
}
