@extends('backend.admins.layouts.base')

@push('title')
    <title>Add Doctor | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
<style>
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border: 2px dashed #ddd;
        border-radius: 5px;
        padding: 5px;
    }
    .service-input-group {
        margin-bottom: 10px;
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Doctor</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.doctors.index') }}">Doctors</a></div>
                <div class="breadcrumb-item">Add New</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create New Doctor</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admins.doctors.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-user-md"></i> Basic Information</h6>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Enter doctor's full name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Enter email address"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doctor_registration_number">Registration Number</label>
                                            <input type="text" name="doctor_registration_number" id="doctor_registration_number" 
                                                   class="form-control @error('doctor_registration_number') is-invalid @enderror" 
                                                   value="{{ old('doctor_registration_number') }}" 
                                                   placeholder="Enter registration number">
                                            @error('doctor_registration_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialization">Specialization</label>
                                            <input type="text" name="specialization" id="specialization" 
                                                   class="form-control @error('specialization') is-invalid @enderror" 
                                                   value="{{ old('specialization') }}" 
                                                   placeholder="e.g., Cardiologist, Dermatologist">
                                            @error('specialization')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_fee">Consultation Fee</label>
                                            <input type="number" step="0.01" name="consultation_fee" id="consultation_fee" 
                                                   class="form-control @error('consultation_fee') is-invalid @enderror" 
                                                   value="{{ old('consultation_fee') }}" 
                                                   placeholder="e.g., 50.00">
                                            @error('consultation_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Professional Details -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-graduation-cap"></i> Professional Details</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="qualification">Qualification</label>
                                            <input type="text" name="qualification" id="qualification" 
                                                   class="form-control @error('qualification') is-invalid @enderror" 
                                                   value="{{ old('qualification') }}" 
                                                   placeholder="e.g., MBBS, MD, MS">
                                            @error('qualification')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="experience">Experience</label>
                                            <input type="text" name="experience" id="experience" 
                                                   class="form-control @error('experience') is-invalid @enderror" 
                                                   value="{{ old('experience') }}" 
                                                   placeholder="e.g., 10 years">
                                            @error('experience')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="education">Education</label>
                                            <textarea name="education" id="education" 
                                                      class="form-control @error('education') is-invalid @enderror" 
                                                      rows="3" 
                                                      placeholder="Educational background and degrees">{{ old('education') }}</textarea>
                                            @error('education')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="services">Services</label>
                                            <div id="services-container">
                                                <div class="service-input-group input-group">
                                                    <input type="text" name="services[]" class="form-control" placeholder="Enter service">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success add-service" title="Add more">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Add multiple services provided by the doctor</small>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_of_birth">Date of Birth</label>
                                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                   value="{{ old('date_of_birth') }}">
                                            @error('date_of_birth')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="age">Age</label>
                                            <input type="text" name="age" id="age" 
                                                   class="form-control @error('age') is-invalid @enderror" 
                                                   value="{{ old('age') }}" 
                                                   placeholder="e.g., 35">
                                            @error('age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile_number">Mobile Number</label>
                                            <input type="text" name="mobile_number" id="mobile_number" 
                                                   class="form-control @error('mobile_number') is-invalid @enderror" 
                                                   value="{{ old('mobile_number') }}" 
                                                   placeholder="Enter mobile number">
                                            @error('mobile_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="whatsapp_number">WhatsApp Number</label>
                                            <input type="text" name="whatsapp_number" id="whatsapp_number" 
                                                   class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                                   value="{{ old('whatsapp_number') }}" 
                                                   placeholder="Enter WhatsApp number">
                                            @error('whatsapp_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea name="address" id="address" 
                                                      class="form-control @error('address') is-invalid @enderror" 
                                                      rows="3" 
                                                      placeholder="Enter full address">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" name="city" id="city" 
                                                   class="form-control @error('city') is-invalid @enderror" 
                                                   value="{{ old('city') }}" 
                                                   placeholder="Enter city">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" name="state" id="state" 
                                                   class="form-control @error('state') is-invalid @enderror" 
                                                   value="{{ old('state') }}" 
                                                   placeholder="Enter state">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country">Country</label>
                                            <input type="text" name="country" id="country" 
                                                   class="form-control @error('country') is-invalid @enderror" 
                                                   value="{{ old('country') }}" 
                                                   placeholder="Enter country">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" name="postal_code" id="postal_code" 
                                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                                   value="{{ old('postal_code') }}" 
                                                   placeholder="Enter postal code">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profile_image">Profile Image</label>
                                            <input type="file" name="profile_image" id="profile_image" 
                                                   class="form-control-file @error('profile_image') is-invalid @enderror" 
                                                   accept="image/*">
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Recommended size: 500x500 pixels. Max file size: 2MB</small>
                                            
                                            <div class="mt-2 image-preview" id="imagePreview" style="display: none;">
                                                <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Social Media & Additional Info -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-share-alt"></i> Social Media & Additional Information</h6>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="bio">Bio/Description</label>
                                            <textarea name="bio" id="bio" 
                                                      class="form-control @error('bio') is-invalid @enderror" 
                                                      rows="4" 
                                                      placeholder="Write about the doctor's experience, achievements, etc.">{{ old('bio') }}</textarea>
                                            @error('bio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website">Website</label>
                                            <input type="url" name="website" id="website" 
                                                   class="form-control @error('website') is-invalid @enderror" 
                                                   value="{{ old('website') }}" 
                                                   placeholder="https://example.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="google_map">Google Map Link</label>
                                            <input type="url" name="google_map" id="google_map" 
                                                   class="form-control @error('google_map') is-invalid @enderror" 
                                                   value="{{ old('google_map') }}" 
                                                   placeholder="https://maps.google.com/...">
                                            @error('google_map')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="text" name="latitude" id="latitude" 
                                                   class="form-control @error('latitude') is-invalid @enderror" 
                                                   value="{{ old('latitude') }}" 
                                                   placeholder="e.g., 28.6139">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="text" name="longitude" id="longitude" 
                                                   class="form-control @error('longitude') is-invalid @enderror" 
                                                   value="{{ old('longitude') }}" 
                                                   placeholder="e.g., 77.2090">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Social Media Links -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="facebook">Facebook</label>
                                            <input type="url" name="facebook" id="facebook" 
                                                   class="form-control @error('facebook') is-invalid @enderror" 
                                                   value="{{ old('facebook') }}" 
                                                   placeholder="https://facebook.com/username">
                                            @error('facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="twitter">Twitter</label>
                                            <input type="url" name="twitter" id="twitter" 
                                                   class="form-control @error('twitter') is-invalid @enderror" 
                                                   value="{{ old('twitter') }}" 
                                                   placeholder="https://twitter.com/username">
                                            @error('twitter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="linkedin">LinkedIn</label>
                                            <input type="url" name="linkedin" id="linkedin" 
                                                   class="form-control @error('linkedin') is-invalid @enderror" 
                                                   value="{{ old('linkedin') }}" 
                                                   placeholder="https://linkedin.com/in/username">
                                            @error('linkedin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instagram">Instagram</label>
                                            <input type="url" name="instagram" id="instagram" 
                                                   class="form-control @error('instagram') is-invalid @enderror" 
                                                   value="{{ old('instagram') }}" 
                                                   placeholder="https://instagram.com/username">
                                            @error('instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="youtube">YouTube</label>
                                            <input type="url" name="youtube" id="youtube" 
                                                   class="form-control @error('youtube') is-invalid @enderror" 
                                                   value="{{ old('youtube') }}" 
                                                   placeholder="https://youtube.com/username">
                                            @error('youtube')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status Settings -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-cog"></i> Status Settings</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="status" value="0">
                                                <input type="checkbox" name="status" class="custom-switch-input" value="1" checked>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Active Doctor</span>
                                            </label>
                                            <small class="form-text text-muted">Active doctors will be visible on the website</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="is_popular" value="0">
                                                <input type="checkbox" name="is_popular" class="custom-switch-input" value="1">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Mark as Popular</span>
                                            </label>
                                            <small class="form-text text-muted">Popular doctors will be featured prominently</small>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Create Doctor
                                            </button>
                                            <a href="{{ route('admins.doctors.index') }}" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-arrow-left"></i> Back to List
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    // Image preview functionality
    document.getElementById('profile_image').addEventListener('change', function(e) {
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
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Add more services functionality
    $(document).ready(function() {
        let serviceCount = 1;
        
        $(document).on('click', '.add-service', function() {
            serviceCount++;
            const newService = `
                <div class="service-input-group input-group">
                    <input type="text" name="services[]" class="form-control" placeholder="Enter service">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-service" title="Remove">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#services-container').append(newService);
        });

        $(document).on('click', '.remove-service', function() {
            if (serviceCount > 1) {
                $(this).closest('.service-input-group').remove();
                serviceCount--;
            }
        });

        // Auto-calculate age from date of birth
        $('#date_of_birth').change(function() {
            const dob = new Date($(this).val());
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            if (age > 0) {
                $('#age').val(age);
            }
        });
    });
</script>
@endsection

