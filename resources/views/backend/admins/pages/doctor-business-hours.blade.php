@extends('backend.admins.layouts.base')

@push('title')
    <title>Doctor Business Hours | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
<style>
    .business-hours-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
    }
    .day-header {
        background-color: #f8f9fc;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    .time-inputs {
        padding: 1rem;
    }
    .closed-checkbox {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
    }
    .time-slot {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .day-name {
        text-transform: capitalize;
        font-weight: 600;
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Doctor Business Hours</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.doctors.index') }}">Doctors</a></div>
                <div class="breadcrumb-item">Business Hours</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Business Hours for Dr. {{ $doctor->name }}</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admins.doctors.show', $doctor->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Doctor
                                </a>
                                <a href="{{ route('admins.doctors.edit', $doctor->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Doctor
                                </a>
                                <a href="{{ route('admins.doctors.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
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

                            <form action="{{ route('admins.doctors.update-business-hours', $doctor->id) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            Set the business hours for each day. Check "Closed" if the doctor doesn't work on that day.
                                        </div>
                                    </div>

                                    @foreach($doctor->businessHours->sortBy(function($item) {
                                        return array_search($item->day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
                                    }) as $businessHour)
                                    <div class="col-md-6">
                                        <div class="business-hours-card">
                                            <div class="day-header">
                                                <h6 class="day-name mb-0">{{ ucfirst($businessHour->day) }}</h6>
                                            </div>
                                            
                                            <div class="time-inputs">
                                                <div class="time-slot">
                                                    <div class="form-group mb-2" style="flex: 1;">
                                                        <label>Opening Time</label>
                                                        <input type="time" 
                                                               name="business_hours[{{ $businessHour->day }}][open_time]" 
                                                               class="form-control opening-time"
                                                               value="{{ $businessHour->open_time ? $businessHour->open_time->format('H:i') : '' }}"
                                                               {{ $businessHour->is_closed ? 'disabled' : '' }}>
                                                    </div>
                                                    
                                                    <div class="form-group mb-2" style="flex: 1;">
                                                        <label>Closing Time</label>
                                                        <input type="time" 
                                                               name="business_hours[{{ $businessHour->day }}][close_time]" 
                                                               class="form-control closing-time"
                                                               value="{{ $businessHour->close_time ? $businessHour->close_time->format('H:i') : '' }}"
                                                               {{ $businessHour->is_closed ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="closed-checkbox">
                                                <div class="form-group mb-0">
                                                    <label class="custom-switch">
                                                        <input type="hidden" name="business_hours[{{ $businessHour->day }}][is_closed]" value="0">
                                                        <input type="checkbox" 
                                                               name="business_hours[{{ $businessHour->day }}][is_closed]" 
                                                               class="custom-switch-input closed-toggle"
                                                               value="1"
                                                               {{ $businessHour->is_closed ? 'checked' : '' }}
                                                               data-day="{{ $businessHour->day }}">
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">Closed</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <input type="hidden" name="business_hours[{{ $businessHour->day }}][day]" value="{{ $businessHour->day }}">
                                        </div>
                                    </div>
                                    @endforeach

                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Update Business Hours
                                            </button>
                                            <button type="button" class="btn btn-success btn-lg" id="apply-all">
                                                <i class="fas fa-copy"></i> Apply to All Weekdays
                                            </button>
                                            <a href="{{ route('admins.doctors.show', $doctor->id) }}" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-arrow-left"></i> Back to Doctor
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
    $(document).ready(function() {
        // Toggle time inputs based on closed checkbox
        $('.closed-toggle').change(function() {
            const day = $(this).data('day');
            const isClosed = $(this).is(':checked');
            
            $(`.business-hours-card input[name="business_hours[${day}][open_time]"]`).prop('disabled', isClosed);
            $(`.business-hours-card input[name="business_hours[${day}][close_time]"]`).prop('disabled', isClosed);
            
            if (isClosed) {
                $(`.business-hours-card input[name="business_hours[${day}][open_time]"]`).val('');
                $(`.business-hours-card input[name="business_hours[${day}][close_time]"]`).val('');
            }
        });

        // Apply to all weekdays functionality
        $('#apply-all').click(function() {
            const mondayOpen = $('input[name="business_hours[monday][open_time]"]').val();
            const mondayClose = $('input[name="business_hours[monday][close_time]"]').val();
            const mondayClosed = $('input[name="business_hours[monday][is_closed]"]').is(':checked');
            
            if (!mondayOpen || !mondayClose) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Data',
                    text: 'Please set Monday hours first before applying to all weekdays.',
                });
                return;
            }

            // Apply to all weekdays (Tuesday to Friday)
            const weekdays = ['tuesday', 'wednesday', 'thursday', 'friday'];
            
            weekdays.forEach(day => {
                $(`input[name="business_hours[${day}][open_time]"]`).val(mondayOpen);
                $(`input[name="business_hours[${day}][close_time]"]`).val(mondayClose);
                $(`input[name="business_hours[${day}][is_closed]"]`).prop('checked', mondayClosed);
                
                // Trigger change event to update disabled state
                $(`input[name="business_hours[${day}][is_closed]"]`).trigger('change');
            });

            Swal.fire({
                icon: 'success',
                title: 'Applied Successfully',
                text: 'Monday hours have been applied to all weekdays (Tuesday to Friday).',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Time validation
        $('.opening-time, .closing-time').change(function() {
            const timeInput = $(this);
            const timeValue = timeInput.val();
            
            if (timeValue) {
                const [hours, minutes] = timeValue.split(':');
                if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time',
                        text: 'Please enter a valid time format (00:00 to 23:59).',
                    });
                    timeInput.val('');
                }
            }
        });
    });
</script>
@endsection
