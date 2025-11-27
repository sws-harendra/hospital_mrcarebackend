{{-- resources/views/backend/appointments/doctor_appointments_index.blade.php --}}

@extends('backend.admins.layouts.base')

@push('title')
    <title>Doctor Appointments | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <style>
        .patient-info {
            font-size: 0.9rem;
        }
        .doctor-name-col {
            min-width: 150px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3em 0.6em;
            border-radius: 0.5rem;
            display: inline-block;
        }
        .bg-pending { background-color: #ffe0b2; color: #e65100; }
        .bg-confirmed { background-color: #c8e6c9; color: #2e7d32; }
        .bg-cancelled { background-color: #ffcdd2; color: #c62828; }

        /* General Admin Layout Fixes */
        .main-content {
            padding-right: 30px; 
            overflow-x: hidden;
        }
        .table-responsive {
            overflow-x: auto;
        }
        /* Overriding base table styles */
        .table th, .table td {
            vertical-align: middle;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Doctor Appointments</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') ?? '#' }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Appointments</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Doctor Appointments ({{ $appointments->total() }})</h4>
                                <div class="card-header-action">
                                    {{-- Optional: Add Filter or Export Buttons here --}}
                                </div>
                            </div>
                            <div class="card-body">
                                
                                {{-- Success/Error Messages --}}
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible show fade">
                                        <div class="alert-body">
                                            <button class="close" data-dismiss="alert"><span>×</span></button>
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Doctor</th>
                                                <th>Patient Details</th>
                                                <th>Appointment Date/Time</th>
                                                <th>Hospital/Clinic</th>
                                                <th>Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($appointments as $appointment)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="doctor-name-col">
                                                        <strong>{{ $appointment->doctor->name ?? 'N/A' }}</strong>
                                                        <br><small class="text-muted">{{ $appointment->doctor->specialization ?? 'Specialist' }}</small>
                                                    </td>
                                                    <td>
                                                        <p class="text-gray-900 font-semibold patient-info mb-0">{{ $appointment->name }}</p>
                                                        <p class="text-gray-600 text-xs mt-0">{{ $appointment->phone_number }} / {{ $appointment->email }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                                                        <small class="text-primary">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        <p class="text-fuchsia-700 mb-0">
                                                            {{ $appointment->hospital->name ?? 'Direct Appointment' }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusMap = [
                                                                'pending' => 'bg-pending',
                                                                'confirmed' => 'bg-confirmed',
                                                                'cancelled' => 'bg-cancelled',
                                                            ];
                                                            $statusClass = $statusMap[$appointment->status] ?? 'bg-gray-200 text-gray-800';
                                                        @endphp
                                                        <span class="status-badge {{ $statusClass }}">
                                                            {{ ucfirst($appointment->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            {{-- View Details/Message --}}
                                                            <a href="{{ route('admins.doctor-appointments.show', $appointment->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            {{-- Status Update Dropdown (Placeholder for AJAX/Form) --}}
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-sm btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Change Status
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="updateAppointmentStatus({{ $appointment->id }}, 'confirmed')">Confirm</a>
                                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="updateAppointmentStatus({{ $appointment->id }}, 'cancelled')">Cancel</a>
                                                                    {{-- Add pending option if needed --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                                        <p>No new doctor appointments found.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{-- Pagination --}}
                                <div class="card-footer text-right">
                                    {{ $appointments->links() }}
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Placeholder for AJAX Status Update Script --}}
    <script>
        // NOTE: This assumes you have jQuery available in your admin layout.
        function updateAppointmentStatus(appointmentId, newStatus) {
            if (!confirm(`Are you sure you want to change the status to ${newStatus.toUpperCase()}?`)) {
                return;
            }

            $.ajax({
                url: `/admins/doctor-appointments/${appointmentId}/status`, // Assuming the PUT route is correct
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    // Refresh the page or update the specific row using JS if preferred
                    location.reload(); 
                },
                error: function(xhr) {
                    alert('Failed to update status. Please check server logs.');
                    console.error('Update Error:', xhr.responseText);
                }
            });
        }
    </script>
@endsection