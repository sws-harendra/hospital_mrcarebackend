@extends('backend.admins.layouts.base')

@push('title')
    <title>Hospitals Management | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
    <style>
        .hospital-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            font-size: 0.7rem;
        }
    </style>
@endpush

@section('page-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hospitals Management</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Hospitals</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Hospitals</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('admins.hospitals.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add New Hospital
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
                                                <th>Logo</th>
                                                <th>Hospital ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th>Badges</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hospitals as $hospital)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($hospital->logo && file_exists(public_path($hospital->logo)))
                                                            <img src="{{ asset($hospital->logo) }}"
                                                                alt="{{ $hospital->name }}"
                                                                class="hospital-logo rounded-circle">
                                                        @else
                                                            <div
                                                                class="hospital-logo bg-light d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-hospital text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $hospital->hospital_id }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $hospital->name }}
                                                        @if ($hospital->hospital_type)
                                                            <br><small
                                                                class="text-muted">{{ $hospital->hospital_type }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $hospital->email }}</td>
                                                    <td>{{ $hospital->phone_number ?? 'N/A' }}</td>
                                                    <td>{{ $hospital->city ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="form-group mb-0">
                                                            <label class="custom-switch mt-2 p-0">
                                                                <input type="checkbox" name="status"
                                                                    class="custom-switch-input status-toggle"
                                                                    data-id="{{ $hospital->id }}"
                                                                    {{ $hospital->status ? 'checked' : '' }}>
                                                                <span class="custom-switch-indicator"></span>
                                                                <span
                                                                    class="custom-switch-description status-text-{{ $hospital->id }}">
                                                                    {{ $hospital->status ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @if ($hospital->is_popular)
                                                                <span class="badge badge-warning status-badge"
                                                                    title="Popular">
                                                                    <i class="fas fa-star"></i>
                                                                </span>
                                                            @endif
                                                            @if ($hospital->is_featured)
                                                                <span class="badge badge-info status-badge"
                                                                    title="Featured">
                                                                    <i class="fas fa-award"></i>
                                                                </span>
                                                            @endif
                                                            @if ($hospital->is_verified)
                                                                <span class="badge badge-success status-badge"
                                                                    title="Verified">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admins.hospitals.show', $hospital->id) }}"
                                                                class="btn btn-sm btn-info" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admins.hospitals.edit', $hospital->id) }}"
                                                                class="btn btn-sm btn-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admins.hospitals.business-hours', $hospital->id) }}"
                                                                class="btn btn-sm btn-warning" title="Business Hours">
                                                                <i class="fas fa-clock"></i>
                                                            </a>
                                                            <a href="{{ route('admins.hospitals.photos', $hospital->id) }}"
                                                                class="btn btn-sm btn-warning" title="Photos">
                                                                <i class="fas fa-images"></i>
                                                            </a>
                                                            <a href="{{ route('admins.hospitals.manage-departments', $hospital->id) }}"
                                                                class="btn btn-sm btn-success" title="Manage Departments">
                                                                <i class="fas fa-plus"></i> 
                                                            </a>
                                                            <form
                                                                action="{{ route('admins.hospitals.destroy', $hospital->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this hospital?')">
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

                                            @if ($hospitals->isEmpty())
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-4">
                                                        <i class="fas fa-hospital fa-2x mb-3"></i>
                                                        <p>No hospitals found. <a
                                                                href="{{ route('admins.hospitals.create') }}">Add the first
                                                                hospital</a></p>
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
                var hospitalId = $(this).data('id');
                var status = this.checked ? 1 : 0;
                var $switch = $(this);
                var $statusText = $('.status-text-' + hospitalId);

                $switch.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admins.hospitals.update-status', ':id') }}".replace(':id',
                        hospitalId),
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
