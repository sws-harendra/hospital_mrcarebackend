@extends('backend.admins.layouts.base')

@push('title')
    <title>Department Management | {{ env('APP_NAME') }}</title>
@endpush



@section('page-content')
<style>
    .status-toggle {
        cursor: pointer;
    }
</style>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Department Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Departments</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Departments</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admins.department.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Department
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>×</span>
                                        </button>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>×</span>
                                        </button>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif

                            <!-- AJAX Alert Container -->
                            <div id="ajax-alert-container"></div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Department Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $department->name }}</strong>
                                            </td>
                                            <td>{{ Str::limit($department->description, 80) }}</td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <label class="custom-switch mt-2 p-0">
                                                        <input type="checkbox" 
                                                               name="status" 
                                                               class="custom-switch-input status-toggle" 
                                                               data-id="{{ $department->id }}"
                                                               id="status-toggle-{{ $department->id }}"
                                                               {{ $department->status ? 'checked' : '' }}>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description" id="status-text-{{ $department->id }}">
                                                            {{ $department->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ $department->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admins.department.edit', $department->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admins.department.destroy', $department->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this department?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                        @if($departments->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-building fa-2x mb-3"></i>
                                                <p>No departments found. <a href="{{ route('admins.department.create') }}">Create the first one</a></p>
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
        var sliderId = $(this).data('id');
        var status = this.checked ? 1 : 0;
        var $switch = $(this);
        var $statusText = $('#status-text-' + sliderId);
        
        console.log('Updating status for department:', sliderId, 'Status:', status);
        
        // Show loading state
        $switch.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('admins.department.update-status', ':id') }}".replace(':id', sliderId),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function(response) {
                console.log('Response:', response);
                
                if (response.success) {
                    // Update status text based on response
                    var statusText = response.status ? 'Active' : 'Inactive';
                    $statusText.text(statusText);
                    
                    // Show success message
                    showAlert('success', response.message);
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                
                // Revert the toggle
                $switch.prop('checked', !status);
                
                // Show error message
                var errorMessage = 'Something went wrong while updating status!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert('error', errorMessage);
            },
            complete: function() {
                // Re-enable the switch
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
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 3000);
    }
});
</script>
@endsection



