@extends('backend.admins.layouts.base')

@push('title')
    <title>Hospital Business Hours | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
<style>
    .business-hours-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .business-hours-card:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .day-header {
        background-color: #f8f9fc;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    .time-inputs {
        padding: 1rem;
    }
    .emergency-checkbox, .closed-checkbox {
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
        flex: 1;
    }
    .emergency-badge {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .time-input-group {
        display: flex;
        gap: 10px;
        align-items: end;
    }
    .time-input {
        flex: 1;
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Hospital Business Hours</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.hospitals.index') }}">Hospitals</a></div>
                <div class="breadcrumb-item">Business Hours</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Business Hours for {{ $hospital->name }}</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admins.hospitals.show', $hospital->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Hospital
                                </a>
                                <a href="{{ route('admins.hospitals.edit', $hospital->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Hospital
                                </a>
                                <a href="{{ route('admins.hospitals.index') }}" class="btn btn-secondary">
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

                            <form action="{{ route('admins.hospitals.update-business-hours', $hospital->id) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            Set the business hours for each day. Check "Closed" if the hospital doesn't operate on that day.
                                            Mark "24/7 Emergency" if emergency services are available round the clock on that day.
                                        </div>
                                    </div>

                                    @foreach($hospital->businessHours->sortBy(function($item) {
                                        return array_search($item->day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
                                    }) as $businessHour)
                                    <div class="col-md-6">
                                        <div class="business-hours-card">
                                            <div class="day-header">
                                                <h6 class="day-name mb-0">{{ ucfirst($businessHour->day) }}</h6>
                                                @if($businessHour->is_emergency_24_7)
                                                    <span class="emergency-badge">24/7 Emergency</span>
                                                @endif
                                            </div>
                                            
                                            <div class="time-inputs">
                                                <div class="time-input-group">
                                                    <div class="form-group time-input">
                                                        <label>Opening Time</label>
                                                        <input type="time" 
                                                               name="business_hours[{{ $businessHour->day }}][open_time]" 
                                                               class="form-control opening-time"
                                                               value="{{ $businessHour->open_time ? $businessHour->open_time->format('H:i') : '' }}"
                                                               {{ $businessHour->is_closed || $businessHour->is_emergency_24_7 ? 'disabled' : '' }}>
                                                    </div>
                                                    
                                                    <div class="form-group time-input">
                                                        <label>Closing Time</label>
                                                        <input type="time" 
                                                               name="business_hours[{{ $businessHour->day }}][close_time]" 
                                                               class="form-control closing-time"
                                                               value="{{ $businessHour->close_time ? $businessHour->close_time->format('H:i') : '' }}"
                                                               {{ $businessHour->is_closed || $businessHour->is_emergency_24_7 ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="emergency-checkbox">
                                                <div class="form-group mb-0">
                                                    <label class="custom-switch">
                                                        <input type="hidden" name="business_hours[{{ $businessHour->day }}][is_emergency_24_7]" value="0">
                                                        <input type="checkbox" 
                                                               name="business_hours[{{ $businessHour->day }}][is_emergency_24_7]" 
                                                               class="custom-switch-input emergency-toggle"
                                                               value="1"
                                                               {{ $businessHour->is_emergency_24_7 ? 'checked' : '' }}
                                                               data-day="{{ $businessHour->day }}">
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">24/7 Emergency Services</span>
                                                    </label>
                                                    <small class="form-text text-muted">
                                                        When checked, regular timings will be disabled
                                                    </small>
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
                                        <div class="card">
                                            <div class="card-body">
                                                <h5>Quick Actions</h5>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-success btn-block" id="apply-weekdays">
                                                            <i class="fas fa-copy"></i> Apply to All Weekdays
                                                        </button>
                                                        <small class="form-text text-muted">Apply Monday hours to Tuesday-Friday</small>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-info btn-block" id="apply-weekend">
                                                            <i class="fas fa-copy"></i> Apply to Weekend
                                                        </button>
                                                        <small class="form-text text-muted">Apply Saturday hours to Sunday</small>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-warning btn-block" id="clear-all">
                                                            <i class="fas fa-times"></i> Clear All Times
                                                        </button>
                                                        <small class="form-text text-muted">Reset all time inputs</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Update Business Hours
                                            </button>
                                            <a href="{{ route('admins.hospitals.show', $hospital->id) }}" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-arrow-left"></i> Back to Hospital
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
                // Uncheck emergency if closed
                $(`.business-hours-card input[name="business_hours[${day}][is_emergency_24_7]"]`).prop('checked', false);
            }
        });

        // Toggle time inputs based on emergency checkbox
        $('.emergency-toggle').change(function() {
            const day = $(this).data('day');
            const isEmergency = $(this).is(':checked');
            
            $(`.business-hours-card input[name="business_hours[${day}][open_time]"]`).prop('disabled', isEmergency);
            $(`.business-hours-card input[name="business_hours[${day}][close_time]"]`).prop('disabled', isEmergency);
            
            if (isEmergency) {
                $(`.business-hours-card input[name="business_hours[${day}][open_time]"]`).val('');
                $(`.business-hours-card input[name="business_hours[${day}][close_time]"]`).val('');
                // Uncheck closed if emergency
                $(`.business-hours-card input[name="business_hours[${day}][is_closed]"]`).prop('checked', false);
            }
        });

        // Apply to all weekdays functionality
        $('#apply-weekdays').click(function() {
            const mondayOpen = $('input[name="business_hours[monday][open_time]"]').val();
            const mondayClose = $('input[name="business_hours[monday][close_time]"]').val();
            const mondayClosed = $('input[name="business_hours[monday][is_closed]"]').is(':checked');
            const mondayEmergency = $('input[name="business_hours[monday][is_emergency_24_7]"]').is(':checked');
            
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
                $(`input[name="business_hours[${day}][is_emergency_24_7]"]`).prop('checked', mondayEmergency);
                
                // Trigger change events to update disabled state
                $(`input[name="business_hours[${day}][is_closed]"]`).trigger('change');
                $(`input[name="business_hours[${day}][is_emergency_24_7]"]`).trigger('change');
            });

            Swal.fire({
                icon: 'success',
                title: 'Applied Successfully',
                text: 'Monday hours have been applied to all weekdays (Tuesday to Friday).',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Apply to weekend functionality
        $('#apply-weekend').click(function() {
            const saturdayOpen = $('input[name="business_hours[saturday][open_time]"]').val();
            const saturdayClose = $('input[name="business_hours[saturday][close_time]"]').val();
            const saturdayClosed = $('input[name="business_hours[saturday][is_closed]"]').is(':checked');
            const saturdayEmergency = $('input[name="business_hours[saturday][is_emergency_24_7]"]').is(':checked');
            
            if (!saturdayOpen || !saturdayClose) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Data',
                    text: 'Please set Saturday hours first before applying to weekend.',
                });
                return;
            }

            // Apply to Sunday
            $(`input[name="business_hours[sunday][open_time]"]`).val(saturdayOpen);
            $(`input[name="business_hours[sunday][close_time]"]`).val(saturdayClose);
            $(`input[name="business_hours[sunday][is_closed]"]`).prop('checked', saturdayClosed);
            $(`input[name="business_hours[sunday][is_emergency_24_7]"]`).prop('checked', saturdayEmergency);
            
            // Trigger change events to update disabled state
            $(`input[name="business_hours[sunday][is_closed]"]`).trigger('change');
            $(`input[name="business_hours[sunday][is_emergency_24_7]"]`).trigger('change');

            Swal.fire({
                icon: 'success',
                title: 'Applied Successfully',
                text: 'Saturday hours have been applied to Sunday.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Clear all times functionality
        $('#clear-all').click(function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will clear all time inputs for all days!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, clear all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.opening-time, .closing-time').val('');
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleared!',
                        text: 'All time inputs have been cleared.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
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

        // Ensure closing time is after opening time
        $('.closing-time').change(function() {
            const closingTime = $(this);
            const openingTime = closingTime.closest('.time-input-group').find('.opening-time');
            
            if (openingTime.val() && closingTime.val()) {
                if (openingTime.val() >= closingTime.val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time Range',
                        text: 'Closing time must be after opening time.',
                    });
                    closingTime.val('');
                }
            }
        });
    });
</script>

@endsection

