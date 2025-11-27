@extends('backend.admins.layouts.base')

@push('title')
    <title>Manage Departments | {{ $hospital->name }} | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
    <style>
        .department-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1rem;
        }
        .department-header {
            background-color: #f8f9fc;
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }
        .doctors-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .badge-custom {
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('page-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manage Departments - {{ $hospital->name }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('admins.hospitals.index') }}">Hospitals</a></div>
                    <div class="breadcrumb-item">Manage Departments</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
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

                        <div id="ajax-alert-container"></div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Add Departments to Hospital</h4>
                            </div>
                            <div class="card-body">
                                <form id="addDepartmentsForm">
                                    @csrf
                                    <div class="form-group">
                                        <label>Select Departments</label>
                                        <select class="form-control select2" multiple="multiple" name="departments[]" id="departmentsSelect">
                                            @foreach($allDepartments as $department)
                                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Selected Departments
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Current Hospital Departments -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Current Departments ({{ $hospitalDepartments->count() }})</h4>
                            </div>
                            <div class="card-body">
                                @if($hospitalDepartments->count() > 0)
                                    @foreach($hospitalDepartments as $department)
                                        <div class="department-card">
                                            <div class="department-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0">{{ $department->name }}</h5>
                                                    <small class="text-muted">{{ $department->description }}</small>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-primary assign-doctors-btn" 
                                                            data-department-id="{{ $department->id }}"
                                                            data-department-name="{{ $department->name }}">
                                                        <i class="fas fa-user-md"></i> Assign Doctors
                                                    </button>
                                                    <button class="btn btn-sm btn-danger remove-department-btn" 
                                                            data-department-id="{{ $department->id }}"
                                                            data-department-name="{{ $department->name }}">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <h6>Assigned Doctors:</h6>
                                                <div class="doctors-list">
                                                    @if($department->doctors->count() > 0)
                                                        <div class="row">
                                                            @foreach($department->doctors as $doctor)
                                                                <div class="col-md-4 mb-2">
                                                                    <div class="d-flex align-items-center p-2 border rounded">
                                                                        @if($doctor->profile_image)
                                                                            <img src="{{ asset($doctor->profile_image) }}" 
                                                                                 alt="{{ $doctor->name }}" 
                                                                                 class="rounded-circle mr-2" 
                                                                                 width="30" height="30">
                                                                        @else
                                                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2" 
                                                                                 style="width: 30px; height: 30px;">
                                                                                <i class="fas fa-user-md text-muted"></i>
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <strong>{{ $doctor->name }}</strong>
                                                                            <br>
                                                                            <small class="text-muted">{{ $doctor->specialization }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-muted">No doctors assigned to this department.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                                        <p>No departments added yet. Add departments using the form above.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('admins.hospitals.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Hospitals
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Assign Doctors Modal -->
    <div class="modal fade" id="assignDoctorsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Doctors to <span id="modalDepartmentName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="assignDoctorsForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="department_id" id="modalDepartmentId">
                        <div class="form-group">
                            <label>Select Doctors</label>
                            <select class="form-control select2" multiple="multiple" name="doctors[]" id="doctorsSelect" style="width: 100%;">
                                @foreach($availableDoctors as $doctor)
                                    <option value="{{ $doctor->id }}">
                                        {{ $doctor->name }} - {{ $doctor->specialization }} ({{ $doctor->doctor_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Selected Doctors</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
       <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Add Departments Form
            $('#addDepartmentsForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: "{{ route('admins.hospitals.store-departments', $hospital->id) }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('error', errorMessage);
                    }
                });
            });

            // Remove Department
            $(document).on('click', '.remove-department-btn', function() {
                var departmentId = $(this).data('department-id');
                var departmentName = $(this).data('department-name');
                
                if (confirm('Are you sure you want to remove ' + departmentName + ' from this hospital?')) {
                    $.ajax({
                        url: "{{ route('admins.hospitals.remove-department', [$hospital->id, ':departmentId']) }}".replace(':departmentId', departmentId),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showAlert('error', response.message);
                            }
                        },
                        error: function(xhr) {
                            showAlert('error', 'Error removing department');
                        }
                    });
                }
            });

            // Assign Doctors Modal
            $(document).on('click', '.assign-doctors-btn', function() {
                var departmentId = $(this).data('department-id');
                var departmentName = $(this).data('department-name');
                
                $('#modalDepartmentId').val(departmentId);
                $('#modalDepartmentName').text(departmentName);
                $('#doctorsSelect').val(null).trigger('change');
                
                $('#assignDoctorsModal').modal('show');
            });

            // Assign Doctors Form
            $('#assignDoctorsForm').on('submit', function(e) {
                e.preventDefault();
                
                var departmentId = $('#modalDepartmentId').val();
                
                $.ajax({
                    url: "{{ route('admins.hospitals.assign-doctors', [$hospital->id, ':departmentId']) }}".replace(':departmentId', departmentId),
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            $('#assignDoctorsModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert('error', errorMessage);
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
                }, 5000);
            }
        });
    </script>
@endsection
