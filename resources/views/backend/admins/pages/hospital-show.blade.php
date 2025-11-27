@extends('backend.admins.layouts.base')

@push('title')
    <title>Hospital Details | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
    <style>
        .hospital-logo-large {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #e3e6f0;
        }

        .detail-card {
            border-left: 4px solid #4e73df;
        }

        .stat-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
        }

        .badge-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
@endpush

@section('page-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hospital Details</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('admins.hospitals.index') }}">Hospitals</a></div>
                    <div class="breadcrumb-item">Details</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Hospital Information</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('admins.hospitals.edit', $hospital->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Hospital
                                    </a>
                                    <a href="{{ route('admins.hospitals.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Logo & Basic Info -->
                                    <div class="col-md-4 text-center">
                                        @if ($hospital->logo && file_exists(public_path($hospital->logo)))
                                            <img src="{{ asset($hospital->logo) }}" alt="{{ $hospital->name }}"
                                                class="hospital-logo-large mb-3" style="max-width: 200px">
                                        @else
                                            <div
                                                class="hospital-logo-large bg-light d-flex align-items-center justify-content-center mx-auto mb-3">
                                                <i class="fas fa-hospital fa-3x text-muted"></i>
                                            </div>
                                        @endif

                                        <h3>{{ $hospital->name }}</h3>
                                        <p class="text-muted">{{ $hospital->hospital_type ?? 'General Hospital' }}</p>

                                        <!-- Status Badges -->
                                        <div class="badge-container mb-3">
                                            <span class="badge badge-{{ $hospital->status ? 'success' : 'danger' }}">
                                                {{ $hospital->status ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if ($hospital->is_popular)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-star"></i> Popular
                                                </span>
                                            @endif
                                            @if ($hospital->is_featured)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-award"></i> Featured
                                                </span>
                                            @endif
                                            @if ($hospital->is_verified)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            <strong>Hospital ID:</strong> {{ $hospital->hospital_id }}<br>
                                            @if ($hospital->established_date)
                                                <strong>Established:</strong>
                                                {{ $hospital->established_date->format('Y') }}<br>
                                            @endif
                                            @if ($hospital->accreditations)
                                                <strong>Accreditations:</strong> {{ $hospital->accreditations }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Contact & Basic Details -->
                                    <div class="col-md-8">
                                        <div class="row">
                                            <!-- Contact Information -->
                                            <div class="col-12">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-phone text-success"></i>
                                                            Contact Information</h5>
                                                        <div class="row mt-3">
                                                            @if ($hospital->email)
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Email:</strong><br>
                                                                    {{ $hospital->email }}
                                                                </div>
                                                            @endif
                                                            @if ($hospital->phone_number)
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Phone:</strong><br>
                                                                    {{ $hospital->phone_number }}
                                                                </div>
                                                            @endif
                                                            @if ($hospital->whatsapp_number)
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>WhatsApp:</strong><br>
                                                                    {{ $hospital->whatsapp_number }}
                                                                </div>
                                                            @endif
                                                            @if ($hospital->emergency_number)
                                                                <div class="col-md-6 mb-2">
                                                                    <strong>Emergency:</strong><br>
                                                                    {{ $hospital->emergency_number }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Hospital Statistics -->
                                            <div class="col-12">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-chart-bar text-primary"></i>
                                                            Hospital Statistics</h5>
                                                        <div class="row mt-3 text-center">
                                                            @if ($hospital->number_of_beds)
                                                                <div class="col-3">
                                                                    <div class="stat-card">
                                                                        <h3>{{ $hospital->number_of_beds }}</h3>
                                                                        <small>Beds</small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if ($hospital->number_of_doctors)
                                                                <div class="col-3">
                                                                    <div class="stat-card"
                                                                        style="background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);">
                                                                        <h3>{{ $hospital->number_of_doctors }}</h3>
                                                                        <small>Doctors</small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if ($hospital->number_of_nurses)
                                                                <div class="col-3">
                                                                    <div class="stat-card"
                                                                        style="background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);">
                                                                        <h3>{{ $hospital->number_of_nurses }}</h3>
                                                                        <small>Nurses</small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if ($hospital->number_of_departments)
                                                                <div class="col-3">
                                                                    <div class="stat-card"
                                                                        style="background: linear-gradient(45deg, #43e97b 0%, #38f9d7 100%);">
                                                                        <h3>{{ $hospital->number_of_departments }}</h3>
                                                                        <small>Departments</small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Hospital Details -->
                                            <div class="col-md-6">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-info-circle text-info"></i>
                                                            Hospital Details</h5>
                                                        <div class="mt-3">
                                                            @if ($hospital->hospital_type)
                                                                <p><strong>Type:</strong> {{ $hospital->hospital_type }}
                                                                </p>
                                                            @endif
                                                            @if ($hospital->ownership_type)
                                                                <p><strong>Ownership:</strong>
                                                                    {{ $hospital->ownership_type }}</p>
                                                            @endif
                                                            @if ($hospital->hospital_registration_number)
                                                                <p><strong>Registration No:</strong>
                                                                    {{ $hospital->hospital_registration_number }}</p>
                                                            @endif
                                                            @if ($hospital->license_number)
                                                                <p><strong>License No:</strong>
                                                                    {{ $hospital->license_number }}</p>
                                                            @endif
                                                            @if ($hospital->established_date)
                                                                <p><strong>Established:</strong>
                                                                    {{ $hospital->established_date->format('d M Y') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Address Information -->
                                            <div class="col-md-6">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i
                                                                class="fas fa-map-marker-alt text-warning"></i> Address
                                                            Information</h5>
                                                        <div class="mt-3">
                                                            @if ($hospital->address)
                                                                <p><strong>Address:</strong><br>{{ $hospital->address }}
                                                                </p>
                                                            @endif
                                                            <div class="row">
                                                                @if ($hospital->city)
                                                                    <div class="col-6"><strong>City:</strong>
                                                                        {{ $hospital->city }}</div>
                                                                @endif
                                                                @if ($hospital->state)
                                                                    <div class="col-6"><strong>State:</strong>
                                                                        {{ $hospital->state }}</div>
                                                                @endif
                                                                @if ($hospital->country)
                                                                    <div class="col-6"><strong>Country:</strong>
                                                                        {{ $hospital->country }}</div>
                                                                @endif
                                                                @if ($hospital->postal_code)
                                                                    <div class="col-6"><strong>Postal Code:</strong>
                                                                        {{ $hospital->postal_code }}</div>
                                                                @endif
                                                            </div>
                                                            @if ($hospital->latitude && $hospital->longitude)
                                                                <div class="mt-2">
                                                                    <strong>Coordinates:</strong><br>
                                                                    Lat: {{ $hospital->latitude }}, Lng:
                                                                    {{ $hospital->longitude }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            @if ($hospital->description)
                                                <div class="col-12">
                                                    <div class="card detail-card mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><i
                                                                    class="fas fa-file-alt text-primary"></i> Description
                                                            </h5>
                                                            <div class="mt-3">
                                                                {{ $hospital->description }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Social Media & Links -->
                                            <div class="col-12">
                                                <div class="card detail-card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-share-alt text-success"></i>
                                                            Social Media & Links</h5>
                                                        <div class="mt-3">
                                                            <div class="row">
                                                                @if ($hospital->website)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->website }}"
                                                                            target="_blank" class="text-primary">
                                                                            <i class="fas fa-globe mr-2"></i> Website
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->google_map)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->google_map }}"
                                                                            target="_blank" class="text-success">
                                                                            <i class="fas fa-map-marked-alt mr-2"></i>
                                                                            Google Map
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->facebook)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->facebook }}"
                                                                            target="_blank" class="text-primary">
                                                                            <i class="fab fa-facebook mr-2"></i> Facebook
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->twitter)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->twitter }}"
                                                                            target="_blank" class="text-info">
                                                                            <i class="fab fa-twitter mr-2"></i> Twitter
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->linkedin)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->linkedin }}"
                                                                            target="_blank" class="text-primary">
                                                                            <i class="fab fa-linkedin mr-2"></i> LinkedIn
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->instagram)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->instagram }}"
                                                                            target="_blank" class="text-danger">
                                                                            <i class="fab fa-instagram mr-2"></i> Instagram
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($hospital->youtube)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $hospital->youtube }}"
                                                                            target="_blank" class="text-danger">
                                                                            <i class="fab fa-youtube mr-2"></i> YouTube
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timestamps -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row text-center">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Created At</small><br>
                                                        <strong>{{ $hospital->created_at->format('d M Y, h:i A') }}</strong>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Last Updated</small><br>
                                                        <strong>{{ $hospital->updated_at->format('d M Y, h:i A') }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Business Hours Section - Add this to show.blade.php -->
                    <div class="col-12">
                        <div class="card detail-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-clock text-warning"></i> Business Hours
                                    <a href="{{ route('admins.hospitals.business-hours', $hospital->id) }}"
                                        class="btn btn-sm btn-primary float-right">
                                        <i class="fas fa-edit"></i> Edit Hours
                                    </a>
                                </h5>
                                <div class="mt-3">
                                    <div class="row">
                                        @foreach ($hospital->businessHours->sortBy(function ($item) {
            return array_search($item->day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
        }) as $businessHour)
                                            <div class="col-md-6 mb-2">
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <div>
                                                        <strong class="text-capitalize">{{ $businessHour->day }}</strong>
                                                        @if ($businessHour->is_emergency_24_7)
                                                            <span class="badge badge-danger ml-2">24/7 Emergency</span>
                                                        @endif
                                                    </div>
                                                    <span>
                                                        @if ($businessHour->is_closed)
                                                            <span class="text-danger">Closed</span>
                                                        @elseif($businessHour->is_emergency_24_7)
                                                            <span class="text-success">24/7 Emergency</span>
                                                        @else
                                                            <span class="text-success">
                                                                {{ $businessHour->open_time->format('h:i A') }} -
                                                                {{ $businessHour->close_time->format('h:i A') }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Current Status -->
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Current Status: </strong>
                                                @if ($hospital->isOpenNow())
                                                 
                                                    <span class="text-success"><i class="fas fa-circle"></i> Open
                                                        Now</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-circle"></i> Closed
                                                        Now</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Emergency Services: </strong>
                                                @if ($hospital->isEmergency24_7())
                                                    <span class="text-success"><i class="fas fa-ambulance"></i> 24/7
                                                        Available</span>
                                                @else
                                                    <span class="text-warning"><i class="fas fa-ambulance"></i> During
                                                        Business Hours</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
