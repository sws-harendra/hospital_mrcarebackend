@extends('backend.admins.layouts.base')

@push('title')
    <title>Edit Home Slider | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Home Slider</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.home-slider.index') }}">Home Slider</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-8 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Slider: {{ $homeSlider->title }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admins.home-slider.update', $homeSlider->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $homeSlider->title) }}" 
                                           placeholder="Enter slider title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="subtitle">Subtitle <span class="text-danger">*</span></label>
                                    <textarea name="subtitle" id="subtitle" 
                                              class="form-control @error('subtitle') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Enter slider subtitle"
                                              required>{{ old('subtitle', $homeSlider->subtitle) }}</textarea>
                                    @error('subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image">Slider Image</label>
                                    
                                    <div class="mb-3">
                                        <label>Current Image:</label>
                                        <div class="current-image-container">
                                            @if($homeSlider->image && file_exists(public_path($homeSlider->image)))
                                                <img src="{{ asset($homeSlider->image) }}" 
                                                     alt="{{ $homeSlider->title }}" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 200px;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 150px;">
                                                    <div class="text-center text-muted">
                                                        <i class="fas fa-image fa-2x mb-2"></i>
                                                        <p>No image available</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="custom-file">
                                        <input type="file" name="image" id="image" 
                                               class="custom-file-input @error('image') is-invalid @enderror" 
                                               accept="image/*">
                                        <label class="custom-file-label" for="image">Choose new image (optional)...</label>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Leave empty to keep current image. Recommended size: 1920x800 pixels. Max file size: 2MB
                                    </small>
                                    
                                    <div class="mt-2 image-preview" id="imagePreview" style="display: none;">
                                        <label>New Image Preview:</label>
                                        <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="link">Link <span class="text-danger">*</span></label>
                                    <input type="url" name="link" id="link" 
                                           class="form-control @error('link') is-invalid @enderror" 
                                           value="{{ old('link', $homeSlider->link) }}" 
                                           placeholder="https://example.com" 
                                           required>
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="custom-switch mt-2">
                                        <input type="hidden" name="status" value="0">
                                        <input type="checkbox" name="status" 
                                               class="custom-switch-input" 
                                               value="1"
                                               {{ old('status', $homeSlider->status) ? 'checked' : '' }}>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Active Slider</span>
                                    </label>
                                    <small class="form-text text-muted">
                                        Active sliders will be displayed on the homepage
                                    </small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Update Slider
                                    </button>
                                    <a href="{{ route('admins.home-slider.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('previewImage');
        const previewContainer = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
            
            // Update file label
            const fileName = file.name;
            const label = document.querySelector('.custom-file-label');
            label.textContent = fileName;
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Bootstrap file input label update
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush