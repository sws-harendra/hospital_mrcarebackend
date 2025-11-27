{{-- resources/views/backend/admins/pages/hospital-appointments.blade.php --}}

@extends('backend.admins.layouts.base')

@push('title')
    <title>Hospital Appointments | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <style>
        .main-content {
            padding-right: 30px;
            overflow-x: hidden;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .detail-info {
            font-size: 0.9rem;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3em 0.6em;
            border-radius: 0.5rem;
            display: inline-block;
            font-weight: 600;
            /* Added margin-top for better alignment with table rows */
            margin-top: 2px;
        }
        /* Tailwind-like status colors (adjust if using pure Bootstrap) */
        .bg-pending { background-color: #ffe0b2; color: #e65100; border: 1px solid #e65100; }
        .bg-confirmed { background-color: #c8e6c9; color: #2e7d32; border: 1px solid #2e7d32; }
        .bg-cancelled { background-color: #ffcdd2; color: #c62828; border: 1px solid #c62828; }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hospital Appointments</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') ?? '#' }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Hospital Appointments</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Hospital Appointments ({{ $appointments->total() }})</h4>
                            </div>
                            <div class="card-body">
                                
                                {{-- Success/Error Messages (Session based, e.g., after updateStatus) --}}
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
                                            <tr class="bg-fuchsia-600 text-white">
                                                <th class="text-center">#</th>
                                                <th>Patient Details</th>
                                                <th>Appointment Date/Time</th>
                                                <th>Hospital / Department</th>
                                                <th>Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($appointments as $appointment)
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    
                                                    {{-- Patient Details --}}
                                                    <td>
                                                        <p class="text-gray-900 font-semibold detail-info mb-0">{{ $appointment->name }}</p>
                                                        <p class="text-gray-600 text-xs mt-0">{{ $appointment->phone_number }}</p>
                                                        <p class="text-gray-600 text-xs mt-0">{{ $appointment->email ?? 'N/A' }}</p>
                                                    </td>
                                                    
                                                    {{-- Date / Time --}}
                                                    <td>
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                                                        <small class="text-fuchsia-700">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                                                    </td>
                                                    
                                                    {{-- Hospital / Department --}}
                                                    <td>
                                                        <p class="text-fuchsia-700 mb-0 font-medium">{{ $appointment->hospital->name ?? 'N/A' }}</p>
                                                        {{-- Ensure department relationship is accessed safely --}}
                                                        <small class="text-muted">{{ $appointment->department->name ?? 'General' }}</small>
                                                    </td>
                                                    
                                                    {{-- Status --}}
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
                                                    
                                                    {{-- Actions --}}
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <a href="{{ route('admins.hospital-appointments.show', $appointment->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                            
                                                            {{-- Status Update Dropdown --}}
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-sm btn-warning dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Change Status">
                                                                    Status
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('status-form-{{ $appointment->id }}-confirm').submit()">Confirm</a>
                                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('status-form-{{ $appointment->id }}-cancel').submit()">Cancel</a>
                                                                </div>
                                                            </div>

                                                            {{-- Hidden Forms for quick status change --}}
                                                            <form id="status-form-{{ $appointment->id }}-confirm" action="{{ route('admins.hospital-appointments.update-status', $appointment) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="confirmed">
                                                            </form>
                                                            <form id="status-form-{{ $appointment->id }}-cancel" action="{{ route('admins.hospital-appointments.update-status', $appointment) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="cancelled">
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                                        <p>No hospital appointments found.</p>
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
@endsection