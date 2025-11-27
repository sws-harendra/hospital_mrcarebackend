<?php
// App\Http\Controllers\Backend\Admins\Views\AdminDoctorAppointmentController.php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Http\Controllers\Controller;
use App\Models\DoctorAppointment; // Make sure this model is correctly named and located
use Illuminate\Http\Request;

class AdminDoctorAppointmentController extends Controller
{
    /**
     * Display a listing of doctor appointments for admin.
     */
    public function index()
    {
        // Eager load related doctor and hospital data
        $appointments = DoctorAppointment::with(['doctor', 'hospital'])
            ->latest()
            ->paginate(15); 
            
        return view('backend.admins.pages.doctor-appointments', compact('appointments'));
    }

    /**
     * Show details of a specific appointment.
     */
    public function show(DoctorAppointment $appointment)
    {
        // $appointment is retrieved automatically via Route Model Binding
        $appointment->load(['doctor', 'hospital']);
        return view('backend.admins.pages.doctor-appointments-show', compact('appointment'));
    }

    /**
     * Update the status of an appointment (e.g., Confirmed/Cancelled).
     */
    public function updateStatus(Request $request, DoctorAppointment $appointment)
    {
        // dd('here');
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $appointment->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Appointment status updated successfully.']);
        }
        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }
}