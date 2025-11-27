@extends('backend.admins.layouts.base')

@push('title')
    <title>Add Hospital | {{ env('APP_NAME') }}</title>
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
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Add Hospital</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.hospitals.index') }}">Hospitals</a></div>
                <div class="breadcrumb-item">Add New</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create New Hospital</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admins.hospitals.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-hospital"></i> Basic Information</h6>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Hospital Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Enter hospital name"
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

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number</label>
                                            <input type="text" name="phone_number" id="phone_number" 
                                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                                   value="{{ old('phone_number') }}" 
                                                   placeholder="Enter phone number">
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
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

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_number">Emergency Number</label>
                                            <input type="text" name="emergency_number" id="emergency_number" 
                                                   class="form-control @error('emergency_number') is-invalid @enderror" 
                                                   value="{{ old('emergency_number') }}" 
                                                   placeholder="Enter emergency number">
                                            @error('emergency_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hospital Details -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Hospital Details</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hospital_type">Hospital Type</label>
                                            <select name="hospital_type" id="hospital_type" class="form-control @error('hospital_type') is-invalid @enderror">
                                                <option value="">Select Type</option>
                                                <option value="Government" {{ old('hospital_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                                <option value="Private" {{ old('hospital_type') == 'Private' ? 'selected' : '' }}>Private</option>
                                                <option value="Trust" {{ old('hospital_type') == 'Trust' ? 'selected' : '' }}>Trust</option>
                                                <option value="Charity" {{ old('hospital_type') == 'Charity' ? 'selected' : '' }}>Charity</option>
                                                <option value="Corporate" {{ old('hospital_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                            </select>
                                            @error('hospital_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ownership_type">Ownership Type</label>
                                            <select name="ownership_type" id="ownership_type" class="form-control @error('ownership_type') is-invalid @enderror">
                                                <option value="">Select Ownership</option>
                                                <option value="Sole Proprietorship" {{ old('ownership_type') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                                <option value="Partnership" {{ old('ownership_type') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                                <option value="Private Limited" {{ old('ownership_type') == 'Private Limited' ? 'selected' : '' }}>Private Limited</option>
                                                <option value="Public Limited" {{ old('ownership_type') == 'Public Limited' ? 'selected' : '' }}>Public Limited</option>
                                                <option value="Government" {{ old('ownership_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                            </select>
                                            @error('ownership_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hospital_registration_number">Registration Number</label>
                                            <input type="text" name="hospital_registration_number" id="hospital_registration_number" 
                                                   class="form-control @error('hospital_registration_number') is-invalid @enderror" 
                                                   value="{{ old('hospital_registration_number') }}" 
                                                   placeholder="Enter registration number">
                                            @error('hospital_registration_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="license_number">License Number</label>
                                            <input type="text" name="license_number" id="license_number" 
                                                   class="form-control @error('license_number') is-invalid @enderror" 
                                                   value="{{ old('license_number') }}" 
                                                   placeholder="Enter license number">
                                            @error('license_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="established_date">Established Date</label>
                                            <input type="date" name="established_date" id="established_date" 
                                                   class="form-control @error('established_date') is-invalid @enderror" 
                                                   value="{{ old('established_date') }}">
                                            @error('established_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="accreditations">Accreditations</label>
                                            <input type="text" name="accreditations" id="accreditations" 
                                                   class="form-control @error('accreditations') is-invalid @enderror" 
                                                   value="{{ old('accreditations') }}" 
                                                   placeholder="e.g., NABH, JCI, ISO">
                                            @error('accreditations')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Statistics -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-chart-bar"></i> Hospital Statistics</h6>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="number_of_beds">Number of Beds</label>
                                            <input type="number" name="number_of_beds" id="number_of_beds" 
                                                   class="form-control @error('number_of_beds') is-invalid @enderror" 
                                                   value="{{ old('number_of_beds') }}" 
                                                   placeholder="e.g., 100" min="0">
                                            @error('number_of_beds')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="number_of_doctors">Number of Doctors</label>
                                            <input type="number" name="number_of_doctors" id="number_of_doctors" 
                                                   class="form-control @error('number_of_doctors') is-invalid @enderror" 
                                                   value="{{ old('number_of_doctors') }}" 
                                                   placeholder="e.g., 50" min="0">
                                            @error('number_of_doctors')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="number_of_nurses">Number of Nurses</label>
                                            <input type="number" name="number_of_nurses" id="number_of_nurses" 
                                                   class="form-control @error('number_of_nurses') is-invalid @enderror" 
                                                   value="{{ old('number_of_nurses') }}" 
                                                   placeholder="e.g., 100" min="0">
                                            @error('number_of_nurses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="number_of_departments">Number of Departments</label>
                                            <input type="number" name="number_of_departments" id="number_of_departments" 
                                                   class="form-control @error('number_of_departments') is-invalid @enderror" 
                                                   value="{{ old('number_of_departments') }}" 
                                                   placeholder="e.g., 20" min="0">
                                            @error('number_of_departments')
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
                                            <label for="logo">Hospital Logo</label>
                                            <input type="file" name="logo" id="logo" 
                                                   class="form-control-file @error('logo') is-invalid @enderror" 
                                                   accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Recommended size: 200x200 pixels. Max file size: 2MB</small>
                                            
                                            <div class="mt-2 image-preview" id="imagePreview" style="display: none;">
                                                <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Coordinates -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-map"></i> Location Coordinates</h6>
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

                                    <!-- Social Media & Additional Info -->
                                    <div class="col-12 mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-share-alt"></i> Social Media & Additional Information</h6>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" 
                                                      class="form-control @error('description') is-invalid @enderror" 
                                                      rows="4" 
                                                      placeholder="Write about the hospital's facilities, specialties, etc.">{{ old('description') }}</textarea>
                                            @error('description')
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

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="status" value="0">
                                                <input type="checkbox" name="status" class="custom-switch-input" value="1" checked>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Active</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="is_popular" value="0">
                                                <input type="checkbox" name="is_popular" class="custom-switch-input" value="1">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Popular</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="is_featured" value="0">
                                                <input type="checkbox" name="is_featured" class="custom-switch-input" value="1">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Featured</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="custom-switch mt-2">
                                                <input type="hidden" name="is_verified" value="0">
                                                <input type="checkbox" name="is_verified" class="custom-switch-input" value="1">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Verified</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Create Hospital
                                            </button>
                                            <a href="{{ route('admins.hospitals.index') }}" class="btn btn-secondary btn-lg">
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
    document.getElementById('logo').addEventListener('change', function(e) {
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
</script>
@endsection
