<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\HospitalAppointment;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function store(Request $request, $hospital_id)
    {
        $hospital = Hospital::where('hospital_id', $hospital_id)->firstOrFail();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',

            // Validate department_id, ensure it exists in the hospital's departments
            'department_id' => [
                'required',
                'integer',
                Rule::exists('hospital_department', 'department_id')
                    ->where(function ($query) use ($hospital) {
                        return $query->where('hospital_id', $hospital->id);
                    })
            ],

            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
        ]);

        // Using Appointment model (mapped to hospital_appointments table)
        HospitalAppointment::create([
            'hospital_id' => $hospital->id,
            'department_id' => $validatedData['department_id'], // Save department ID
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'appointment_date' => $validatedData['appointment_date'],
            'appointment_time' => $validatedData['appointment_time'],
            'message' => $validatedData['message'],
        ]);

        return response()->json(['message' => 'Appointment request submitted successfully! We will contact you shortly.'], 200);
    }
}
