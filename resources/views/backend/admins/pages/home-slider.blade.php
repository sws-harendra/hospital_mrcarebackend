@extends('backend.admins.layouts.base')

@push('title')
    <title>Home Slider Management | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
<style>
    .status-toggle {
        cursor: pointer;
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Home Slider Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Home Slider</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Home Sliders</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admins.home-slider.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Slider
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Success/Error Messages -->
                            <div id="alert-container"></div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Subtitle</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sliders as $slider)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($slider->image && file_exists(public_path($slider->image)))
                                                    <img src="{{ asset($slider->image) }}" 
                                                         alt="{{ $slider->title }}" 
                                                         width="80" 
                                                         style="border-radius: 5px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 80px; height: 60px; border-radius: 5px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $slider->title }}</td>
                                            <td>{{ Str::limit($slider->subtitle, 50) }}</td>
                                            <td>
                                                @if($slider->link)
                                                    <a href="{{ $slider->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> View
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Link</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <label class="custom-switch mt-2 p-0">
                                                        <input type="checkbox" 
                                                               name="status" 
                                                               class="custom-switch-input status-toggle" 
                                                               data-id="{{ $slider->id }}"
                                                               {{ $slider->status ? 'checked' : '' }}>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description status-text-{{ $slider->id }}">
                                                            {{ $slider->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ $slider->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admins.home-slider.edit', $slider->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admins.home-slider.destroy', $slider->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this slider?')">
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
    $('.status-toggle').on('change', function() {
        var sliderId = $(this).data('id');
        var status = this.checked ? 1 : 0;
        var $switch = $(this);
        var $statusText = $('.status-text-' + sliderId);
        
        console.log('Toggling status for:', sliderId, 'Status:', status);
        
        // Show loading
        $switch.prop('disabled', true);
        
        // Simple AJAX call
        fetch("{{ url('admins/home-slider') }}/" + sliderId + "/status", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            
            if (data.success) {
                // Update status text
                $statusText.text(status ? 'Active' : 'Inactive');
                
                // Show success message
                showAlert('success', data.message);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert toggle
            $switch.prop('checked', !status);
            showAlert('error', 'Error updating status: ' + error.message);
        })
        .finally(() => {
            $switch.prop('disabled', false);
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
        $('#alert-container').html(alertHtml);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 3000);
    }
});
</script>

@endsection


