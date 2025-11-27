<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DoctorAppointment;
use App\Http\Controllers\Controller;

class DoctorBookingController extends Controller
{
    public function store(Request $request, $doctor_id)
    {
        $doctor = Doctor::where('doctor_id', $doctor_id)->firstOrFail(); // Assuming doctor_id is the route key

        // dd($doctor);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'hospital_id' => [
              
                'nullable', 
            ],
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);
        // dd($validatedData);

        DoctorAppointment::create([
            'doctor_id' => $doctor->id,
            'hospital_id' => $validatedData['hospital_id'] ?? null, 
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'appointment_date' => $validatedData['appointment_date'],
            'appointment_time' => $validatedData['appointment_time'],
            'message' => $validatedData['message'],
        ]);

        return response()->json([
            'success' => 'Appointment request submitted successfully! The clinic will contact you.',
        ], 200);
    }
}
