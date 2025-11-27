{{-- resources/views/backend/admins/pages/dashboard.blade.php --}}

@extends('backend.admins.layouts.base')

@push('title')
    <title>Admin Dashboard | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard Overview</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    
                    {{-- 1. Total Doctors --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Doctors</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalDoctors }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Total Hospitals --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-hospital-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Hospitals</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalHospitals }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Total Departments --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Departments</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalDepartments }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Total Doctor Appointments --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Doctor Appts.</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalDoctorAppointments }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 5. Total Hospital Appointments --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Hospital Appts.</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalHospitalAppointments }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- 6. Pending Appointments (Optional) --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-secondary">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Appts.</h4>
                                </div>
                                <div class="card-body">
                                    {{ $pendingAppointments }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                {{-- Additional dashboard content goes here --}}
                
            </div>
        </section>
    </div>
@endsection