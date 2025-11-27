<?php

namespace App\Http\Controllers\Backend\Admins\Views;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\DoctorAppointment;
use App\Models\HospitalAppointment;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Fetching counts from respective models
        $totalDepartments = Department::count();
        $totalHospitals = Hospital::count();
        $totalDoctors = Doctor::count();
        $totalDoctorAppointments = DoctorAppointment::count();
        $totalHospitalAppointments = HospitalAppointment::count();

        // Optional: Get pending appointments for quick action
        $pendingAppointments = DoctorAppointment::where('status', 'pending')->count() +
            HospitalAppointment::where('status', 'pending')->count();

        return view('backend.admins.pages.dashboard', compact(
            'totalDepartments',
            'totalHospitals',
            'totalDoctors',
            'totalDoctorAppointments',
            'totalHospitalAppointments',
            'pendingAppointments'
        ));
    }
}
