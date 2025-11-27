@extends('backend.admins.layouts.base')

@push('title')
    <title>Doctors Management | {{ env('APP_NAME') }}</title>
@endpush


@section('page-content')
  <style>
        .doctor-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .status-badge {
            font-size: 0.8rem;
        }
         /* Fix for right section scrolling */
    .main-content {
        padding-right: 30px; /* Add right padding */
        overflow-x: hidden; /* Hide horizontal overflow */
    }
    </style>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Doctors Management</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Doctors</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Doctors</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('admins.doctors.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add New Doctor
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Success/Error Messages -->
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible show fade">
                                        <div class="alert-body">
                                            <button class="close" data-dismiss="alert">
                                                <span>×</span>
                                            </button>
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible show fade">
                                        <div class="alert-body">
                                            <button class="close" data-dismiss="alert">
                                                <span>×</span>
                                            </button>
                                            {{ session('error') }}
                                        </div>
                                    </div>
                                @endif

                                <div id="ajax-alert-container"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Doctor ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Specialization</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th>Popular</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($doctors as $doctor)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($doctor->profile_image && file_exists(public_path($doctor->profile_image)))
                                                            <img src="{{ asset($doctor->profile_image) }}"
                                                                alt="{{ $doctor->name }}"
                                                                class="doctor-image rounded-circle ">
                                                        @else
                                                            <div
                                                                class="doctor-image bg-light d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-user-md text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $doctor->doctor_id }}</strong>
                                                        @if ($doctor->doctor_registration_number)
                                                            <br><small
                                                                class="text-muted">{{ $doctor->doctor_registration_number }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $doctor->name }}
                                                        @if ($doctor->qualification)
                                                            <br><small
                                                                class="text-muted">{{ $doctor->qualification }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $doctor->email }}</td>
                                                    <td>{{ $doctor->specialization ?? 'N/A' }}</td>
                                                    <td>{{ $doctor->mobile_number ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <label class="custom-switch mt-2 p-0">
                                                                <input type="checkbox" name="status"
                                                                    class="custom-switch-input status-toggle"
                                                                    data-id="{{ $doctor->id }}"
                                                                    {{ $doctor->status ? 'checked' : '' }}>
                                                                <span class="custom-switch-indicator"></span>
                                                                <span
                                                                    class="custom-switch-description status-text-{{ $doctor->id }}">
                                                                    {{ $doctor->status ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <label class="custom-switch mt-2 p-0">
                                                                <input type="checkbox" name="is_popular"
                                                                    class="custom-switch-input popular-toggle"
                                                                    data-id="{{ $doctor->id }}"
                                                                    {{ $doctor->is_popular ? 'checked' : '' }}>
                                                                <span class="custom-switch-indicator"></span>
                                                                <span
                                                                    class="custom-switch-description popular-text-{{ $doctor->id }}">
                                                                    {{ $doctor->is_popular ? 'Yes' : 'No' }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admins.doctors.show', $doctor->id) }}"
                                                                class="btn btn-sm btn-info" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admins.doctors.edit', $doctor->id) }}"
                                                                class="btn btn-sm btn-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admins.doctors.business-hours', $doctor->id) }}"
                                                                class="btn btn-sm btn-warning" title="Business Hours">
                                                                <i class="fas fa-clock"></i>
                                                            </a>
                                                            
                                                            <a href="{{ route('admins.doctors.photos', $doctor->id) }}"
                                                                class="btn btn-sm btn-success" title="Photos">
                                                                <i class="fas fa-images"></i>
                                                            </a>
                                                            <form
                                                                action="{{ route('admins.doctors.destroy', $doctor->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this doctor?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($doctors->isEmpty())
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">
                                                        <i class="fas fa-user-md fa-2x mb-3"></i>
                                                        <p>No doctors found. <a
                                                                href="{{ route('admins.doctors.create') }}">Add the first
                                                                doctor</a></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).ready(function() {
            // Status toggle with AJAX
            $(document).on('change', '.status-toggle', function() {
                var doctorId = $(this).data('id');
                var status = this.checked ? 1 : 0;
                var $switch = $(this);
                var $statusText = $('.status-text-' + doctorId);

                $switch.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admins.doctors.update-status', ':id') }}".replace(':id',
                        doctorId),
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            var statusText = response.status ? 'Active' : 'Inactive';
                            $statusText.text(statusText);
                            showAlert('success', response.message);
                        } else {
                            throw new Error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $switch.prop('checked', !status);
                        var errorMessage = 'Something went wrong while updating status!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('error', errorMessage);
                    },
                    complete: function() {
                        $switch.prop('disabled', false);
                    }
                });
            });

            // Popular toggle with AJAX
            $(document).on('change', '.popular-toggle', function() {
                var doctorId = $(this).data('id');
                var isPopular = this.checked ? 1 : 0;
                var $switch = $(this);
                var $popularText = $('.popular-text-' + doctorId);

                $switch.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admins.doctors.update-popular', ':id') }}".replace(':id',
                        doctorId),
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        is_popular: isPopular
                    },
                    success: function(response) {
                        if (response.success) {
                            var popularText = response.is_popular ? 'Yes' : 'No';
                            $popularText.text(popularText);
                            showAlert('success', response.message);
                        } else {
                            throw new Error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $switch.prop('checked', !isPopular);
                        var errorMessage =
                        'Something went wrong while updating popular status!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('error', errorMessage);
                    },
                    complete: function() {
                        $switch.prop('disabled', false);
                    }
                });
            });

            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>×</span>
                    </button>
                    ${message}
                </div>
            </div>
        `;
                $('#ajax-alert-container').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            }
        });
    </script>
@endsection
