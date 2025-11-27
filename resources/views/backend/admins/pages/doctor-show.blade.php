@extends('backend.admins.layouts.base')

@push('title')
    <title>Doctor Details | {{ env('APP_NAME') }}</title>
@endpush





@section('page-content')
    <style>
        .doctor-profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #e3e6f0;
        }

        .detail-card {
            border-left: 4px solid #4e73df;
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
    </style>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Doctor Details</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('admins.doctors.index') }}">Doctors</a></div>
                    <div class="breadcrumb-item">Details</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Doctor Information</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('admins.doctors.edit', $doctor->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Doctor
                                    </a>
                                    <a href="{{ route('admins.doctors.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Profile Image & Basic Info -->
                                    <div class="col-md-4 text-center">
                                        @if ($doctor->profile_image && file_exists(public_path($doctor->profile_image)))
                                            <img src="{{ asset($doctor->profile_image) }}" alt="{{ $doctor->name }}"
                                                class="img-fluid doctor-profile-image rounded-circle">
                                        @else
                                            <div
                                                class="doctor-profile-image bg-light d-flex align-items-center justify-content-center mx-auto mb-3">
                                                <i class="fas fa-user-md fa-3x text-muted"></i>
                                            </div>
                                        @endif

                                        <h4>{{ $doctor->name }}</h4>
                                        <p class="text-muted">{{ $doctor->specialization ?? 'General Practitioner' }}</p>

                                        <div class="mt-3">
                                            <span class="badge badge-{{ $doctor->status ? 'success' : 'danger' }} mr-2">
                                                {{ $doctor->status ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if ($doctor->is_popular)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-star"></i> Popular
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            <strong>Doctor ID:</strong> {{ $doctor->doctor_id }}<br>
                                            @if ($doctor->doctor_registration_number)
                                                <strong>Registration No:</strong> {{ $doctor->doctor_registration_number }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Professional Details -->
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i
                                                                class="fas fa-graduation-cap text-primary"></i> Professional
                                                            Information</h5>
                                                        <div class="row mt-3">
                                                            <div class="col-md-6">
                                                                <strong>Qualification:</strong><br>
                                                                {{ $doctor->qualification ?? 'N/A' }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Experience:</strong><br>
                                                                {{ $doctor->experience ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                        @if ($doctor->education)
                                                            <div class="row mt-2">
                                                                <div class="col-12">
                                                                    <strong>Education:</strong><br>
                                                                    {{ $doctor->education }}
                                                                </div>
                                                            </div>

                                                        @endif
                                                        @if ($doctor->consultation_fee)
                                                            <div class="row mt-2">
                                                                <div class="col-12">
                                                                    <strong>Consultation Fee:</strong><br>
                                                                    Rs {{ number_format($doctor->consultation_fee, 2) }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contact Information -->
                                            <div class="col-md-6">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-phone text-success"></i>
                                                            Contact Information</h5>
                                                        <div class="mt-3">
                                                            @if ($doctor->email)
                                                                <p><i class="fas fa-envelope mr-2"></i>
                                                                    {{ $doctor->email }}</p>
                                                            @endif
                                                            @if ($doctor->mobile_number)
                                                                <p><i class="fas fa-mobile-alt mr-2"></i>
                                                                    {{ $doctor->mobile_number }}</p>
                                                            @endif
                                                            @if ($doctor->whatsapp_number)
                                                                <p><i class="fab fa-whatsapp mr-2"></i>
                                                                    {{ $doctor->whatsapp_number }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Personal Information -->
                                            <div class="col-md-6">
                                                <div class="card detail-card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><i class="fas fa-user text-info"></i>
                                                            Personal Information</h5>
                                                        <div class="mt-3">
                                                            @if ($doctor->gender)
                                                                <p><strong>Gender:</strong> {{ $doctor->gender }}</p>
                                                            @endif
                                                            @if ($doctor->date_of_birth)
                                                                <p><strong>Date of Birth:</strong>
                                                                    {{ $doctor->date_of_birth->format('d M Y') }}</p>
                                                            @endif
                                                            @if ($doctor->age)
                                                                <p><strong>Age:</strong> {{ $doctor->age }} years</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Address Information -->
                                            @if ($doctor->address || $doctor->city || $doctor->state)
                                                <div class="col-12">
                                                    <div class="card detail-card mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><i
                                                                    class="fas fa-map-marker-alt text-warning"></i> Address
                                                                Information</h5>
                                                            <div class="mt-3">
                                                                @if ($doctor->address)
                                                                    <p><strong>Address:</strong> {{ $doctor->address }}</p>
                                                                @endif
                                                                <div class="row">
                                                                    @if ($doctor->city)
                                                                        <div class="col-md-3"><strong>City:</strong>
                                                                            {{ $doctor->city }}</div>
                                                                    @endif
                                                                    @if ($doctor->state)
                                                                        <div class="col-md-3"><strong>State:</strong>
                                                                            {{ $doctor->state }}</div>
                                                                    @endif
                                                                    @if ($doctor->country)
                                                                        <div class="col-md-3"><strong>Country:</strong>
                                                                            {{ $doctor->country }}</div>
                                                                    @endif
                                                                    @if ($doctor->postal_code)
                                                                        <div class="col-md-3"><strong>Postal Code:</strong>
                                                                            {{ $doctor->postal_code }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Services -->
                                            @if ($doctor->services && count($doctor->services) > 0)
                                                <div class="col-12">
                                                    <div class="card detail-card mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><i class="fas fa-list text-primary"></i>
                                                                Services</h5>
                                                            <div class="mt-3">
                                                                @foreach ($doctor->services as $service)
                                                                    @if (!empty($service))
                                                                        <span
                                                                            class="badge badge-light mr-2 mb-2 p-2">{{ $service }}</span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Bio -->
                                            @if ($doctor->bio)
                                                <div class="col-12">
                                                    <div class="card detail-card mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><i class="fas fa-file-alt text-info"></i>
                                                                Bio</h5>
                                                            <div class="mt-3">
                                                                {{ $doctor->bio }}
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
                                                                @if ($doctor->website)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->website }}" target="_blank"
                                                                            class="text-primary">
                                                                            <i class="fas fa-globe mr-2"></i> Website
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->facebook)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->facebook }}" target="_blank"
                                                                            class="text-primary">
                                                                            <i class="fab fa-facebook mr-2"></i> Facebook
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->twitter)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->twitter }}" target="_blank"
                                                                            class="text-info">
                                                                            <i class="fab fa-twitter mr-2"></i> Twitter
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->linkedin)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->linkedin }}" target="_blank"
                                                                            class="text-primary">
                                                                            <i class="fab fa-linkedin mr-2"></i> LinkedIn
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->instagram)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->instagram }}"
                                                                            target="_blank" class="text-danger">
                                                                            <i class="fab fa-instagram mr-2"></i> Instagram
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->youtube)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->youtube }}" target="_blank"
                                                                            class="text-danger">
                                                                            <i class="fab fa-youtube mr-2"></i> YouTube
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                @if ($doctor->google_map)
                                                                    <div class="col-md-6 mb-2">
                                                                        <a href="{{ $doctor->google_map }}"
                                                                            target="_blank" class="text-success">
                                                                            <i class="fas fa-map-marked-alt mr-2"></i>
                                                                            Google Map
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
                                                        <strong>{{ $doctor->created_at->format('d M Y, h:i A') }}</strong>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Last Updated</small><br>
                                                        <strong>{{ $doctor->updated_at->format('d M Y, h:i A') }}</strong>
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
                                    <a href="{{ route('admins.doctors.business-hours', $doctor->id) }}"
                                        class="btn btn-sm btn-primary float-right">
                                        <i class="fas fa-edit"></i> Edit Hours
                                    </a>
                                </h5>
                                <div class="mt-3">
                                    <div class="row">
                                        @foreach ($doctor->businessHours->sortBy(function ($item) {
            return array_search($item->day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
        }) as $businessHour)
                                            <div class="col-md-6 mb-2">
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <strong class="text-capitalize">{{ $businessHour->day }}</strong>
                                                    <span>
                                                        @if ($businessHour->is_closed)
                                                            <span class="text-danger">Closed</span>
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
                                        <strong>Current Status: </strong>
                                        @if ($doctor->isOpenNow())
                                            <span class="text-success"><i class="fas fa-circle"></i> Open Now</span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-circle"></i> Closed Now</span>
                                        @endif
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
