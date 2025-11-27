<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use Illuminate\Http\Request;
use App\Models\HospitalAppointment;
use App\Http\Controllers\Controller;

class AdminHospitalAppointmentController extends Controller
{
     /**
     * Display a listing of doctor appointments for admin.
     */
    public function index()
    {
        // Eager load related doctor and hospital data
      // Eager load related hospital and department data
        $appointments = HospitalAppointment::with(['hospital', 'department'])
            ->latest()
            ->paginate(15);
            // dd($appointments);
            
        return view('backend.admins.pages.hospital-appointments', compact('appointments'));
    }

    /**
     * Show details of a specific appointment.
     */
    public function show(HospitalAppointment $appointment)
    {
        // $appointment is retrieved automatically via Route Model Binding
       $appointment->load(['hospital', 'department']);
        return view('backend.admins.pages.hospital-appointments-show', compact('appointment'));
    }

    /**
     * Update the status of an appointment (e.g., Confirmed/Cancelled).
     */
    public function updateStatus(Request $request, HospitalAppointment $appointment)
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
