{{-- resources/views/backend/appointments/hospital_appointments_show.blade.php --}}

@extends('backend.admins.layouts.base')

@push('title')
    <title>{{ $appointment->id }} Details | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <style>
        .main-content {
            padding-right: 30px; 
            overflow-x: hidden;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d; 
            margin-bottom: 0.25rem;
            display: block;
        }
        .detail-value {
            font-size: 1rem;
            color: #343a40; 
            margin-bottom: 1rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.4em 0.8em;
            border-radius: 0.5rem;
            display: inline-block;
        }
        /* Status colors should match your admin theme or Tailwind classes */
        .bg-pending { background-color: #ffe0b2; color: #e65100; border: 1px solid #e65100; }
        .bg-confirmed { background-color: #c8e6c9; color: #2e7d32; border: 1px solid #2e7d32; }
        .bg-cancelled { background-color: #ffcdd2; color: #c62828; border: 1px solid #c62828; }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hospital Appointment Details</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') ?? '#' }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('admins.hospital-appointments.index') ?? '#' }}">Hospital Appointments</a></div>
                    <div class="breadcrumb-item">#{{ $appointment->id }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-8"> {{-- Main details section --}}
                        <div class="card">
                            <div class="card-header">
                                <h4>Appointment ID: #{{ $appointment->id }}</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('admins.hospital-appointments.index') ?? '#' }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Hospital/Department Information --}}
                                    <div class="col-md-6 mb-4">
                                        <h5 class="mb-3 text-fuchsia-600">Hospital Details</h5>
                                        <p class="detail-label">Hospital Name:</p>
                                        <p class="detail-value">
                                            {{ $appointment->hospital->name ?? 'N/A' }}
                                            <br><small class="text-muted">{{ $appointment->hospital->hospital_id ?? '' }}</small>
                                        </p>
                                        
                                        <p class="detail-label">Department:</p>
                                        <p class="detail-value">{{ $appointment->department->name ?? 'General / Not Specified' }}</p>

                                        <p class="detail-label">Location:</p>
                                        <p class="detail-value">{{ $appointment->hospital->city ?? 'N/A' }}, {{ $appointment->hospital->state ?? 'N/A' }}</p>
                                    </div>

                                    {{-- Patient Information --}}
                                    <div class="col-md-6 mb-4">
                                        <h5 class="mb-3 text-success">Patient Information</h5>
                                        <p class="detail-label">Name:</p>
                                        <p class="detail-value">{{ $appointment->name }}</p>
                                        
                                        <p class="detail-label">Phone Number:</p>
                                        <p class="detail-value"><a href="tel:{{ $appointment->phone_number }}">{{ $appointment->phone_number }}</a></p>
                                        
                                        <p class="detail-label">Email:</p>
                                        <p class="detail-value">{{ $appointment->email ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    {{-- Appointment Details --}}
                                    <div class="col-md-6 mb-4">
                                        <h5 class="mb-3 text-info">Appointment Schedule</h5>
                                        <p class="detail-label">Date:</p>
                                        <p class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</p>
                                        
                                        <p class="detail-label">Time:</p>
                                        <p class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                                        
                                        <p class="detail-label">Submission Time:</p>
                                        <p class="detail-value">{{ $appointment->created_at->format('F d, Y h:i A') }}</p>
                                    </div>

                                    {{-- Message --}}
                                    <div class="col-md-6 mb-4">
                                        <h5 class="mb-3 text-dark">Patient Message / Reason</h5>
                                        <p class="detail-value p-3 bg-light rounded border border-gray-200">
                                            {{ $appointment->message ?? 'No specific reason provided by the patient.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4"> {{-- Status Update Section --}}
                        <div class="card">
                            <div class="card-header">
                                <h4>Update Status</h4>
                            </div>
                            <div class="card-body">
                                
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible show fade">
                                        <div class="alert-body"><button class="close" data-dismiss="alert"><span>×</span></button>{{ session('success') }}</div>
                                    </div>
                                @endif
                                
                                <p class="detail-label">Current Status:</p>
                                @php
                                    $statusMap = [
                                        'pending' => 'bg-pending',
                                        'confirmed' => 'bg-confirmed',
                                        'cancelled' => 'bg-cancelled',
                                    ];
                                    $statusClass = $statusMap[$appointment->status] ?? 'badge-secondary';
                                @endphp
                                <p class="detail-value">
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </p>

                                <h5 class="mb-3 mt-4">Change Status</h5>
                                <form action="{{ route('admins.hospital-appointments.update-status', $appointment) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="status">Select New Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection