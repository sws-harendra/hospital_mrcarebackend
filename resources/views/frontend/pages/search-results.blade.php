@extends('frontend.layouts.base')

@push('title')
    <title>Search Results for "{{ $query }}" | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <section class="bg-gray-100 py-12 lg:py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">

            <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Search Results</h1>
            <p class="text-lg text-gray-600 mb-8">Showing results for: <span
                    class="font-semibold text-fuchsia-600">"{{ $query }}"</span></p>

            {{-- Check if any results were found --}}
            @if ($doctors->isEmpty() && $hospitals->isEmpty())
                <div class="bg-white p-10 rounded-xl shadow-md text-center">
                    <i class="fas fa-search-minus fa-3x text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">Sorry, no matching doctors or hospitals were found.</h3>
                    <p class="text-gray-500 mt-2">Try refining your search query (e.g., use "Cardio" instead of "Heart
                        Specialist").</p>
                </div>
            @endif

            {{-- ======================== 1. DOCTORS RESULTS ======================== --}}
            @if ($doctors->isNotEmpty())
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2"><i
                            class="fas fa-user-md text-fuchsia-600 mr-2"></i> Found Doctors ({{ $doctors->count() }})</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($doctors as $doctor)
                            @php
                                $rating = $doctor->avg_rating ?? 0;
                                $hospitalDisplay = $doctor->hospital_names ?? 'N/A';

                                // Check if the hospitals relationship exists and is not empty
                                $hasHospitals = $doctor->hospitals->isNotEmpty();

                                // Prepare hospital options for the modal if they exist
                                $hospitalOptions = $hasHospitals
                                    ? $doctor->hospitals
                                        ->map(function ($h) {
                                            return [
                                                'id' => $h->hospital_id,
                                                'name' => $h->name . ' (' . ($h->city ?? 'N/A') . ')',
                                                'data_name' => $h->name, // Hospital name without city for modal header
                                            ];
                                        })
                                        ->toJson()
                                    : '[]';
                            @endphp
                            <div
                                class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 group block p-4 text-center border-t-4 border-fuchsia-400">
                                <a href="{{ route('doctor.show', ['doctor_id' => $doctor->id]) ?? '#' }}" class="block">
                                    <img src="{{ asset($doctor->profile_image ?? 'img/default-doctor.png') }}"
                                        alt="{{ $doctor->name }}"
                                        class="w-24 h-24 object-cover rounded-full mx-auto mb-3 border-2 border-gray-200 group-hover:border-fuchsia-600 transition">

                                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $doctor->name }}</h3>
                                    <p class="text-sm text-fuchsia-700 font-medium">
                                        {{ $doctor->specialization ?? 'Specialist' }}</p>

                                    {{-- Fees and Hospital Info --}}
                                    <div class="text-xs text-gray-500 mt-2 space-y-1">
                                        @if (!empty($doctor->consultation_fee))
                                            <p class="font-semibold text-green-600">Fee:
                                                ₹{{ number_format($doctor->consultation_fee, 0) }}</p>
                                        @endif
                                        @if (!empty($hospitalDisplay))
                                            <p class="text-gray-600 truncate" title="{{ $hospitalDisplay }}">
                                                <i class="fas fa-hospital mr-1 text-fuchsia-500"></i>
                                                {{ $hospitalDisplay }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Rating & Location/Distance --}}
                                    <div class="flex items-center justify-center mt-2 text-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= round($rating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                                        @endfor
                                        <span class="ml-2 text-xs text-gray-600">({{ number_format($rating, 1) }}/5)</span>
                                    </div>

                                    @if (isset($doctor->distance))
                                        <p class="text-sm font-semibold text-fuchsia-600 mt-1">
                                            <i class="fas fa-location-arrow mr-1"></i> {{ round($doctor->distance, 1) }} km
                                            away
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-2">{{ $doctor->city ?? 'N/A' }}</p>
                                    @endif


                                    <div class="mt-3 text-sm text-gray-700 flex flex-col items-center">
                                        @if (!empty($doctor->address) || !empty($doctor->city) || !empty($doctor->state))
                                            @php
                                                $doctorAddress = trim(
                                                    $doctor->address .
                                                        ' ' .
                                                        ($doctor->city ?? '') .
                                                        ' ' .
                                                        ($doctor->state ?? ''),
                                                );
                                                $doctorPrimaryMapUrl =
                                                    'https://www.google.com/maps/search/?api=1&query=' .
                                                    urlencode($doctorAddress);
                                            @endphp
                                            <a href="{{ $doctorPrimaryMapUrl }}" target="_blank"
                                                class="flex  hover:text-fuchsia-700 hover:underline transition cursor-pointer">
                                                <i class="fas fa-map-marker-alt text-fuchsia-600 mr-2"></i>
                                                {{ $doctor->city ?? '' }}{{ !empty($doctor->city) && !empty($doctor->state) ? ', ' : '' }}{{ $doctor->state ?? '' ?: 'Location not specified' }}
                                            </a>
                                        @endif

                                    </div>

                                </a>


                                <button onclick="openDoctorBookingModal('{{ $doctor->doctor_id }}')"
                                    class="w-full mt-3 bg-fuchsia-700 hover:bg-fuchsia-800 text-white font-semibold text-xs px-3 py-2 rounded-lg transition shadow-md">
                                    <i class="fas fa-calendar-check mr-1"></i> Book Appointment
                                </button>


                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ======================== 2. HOSPITALS RESULTS (Standard Hospital Booking) ======================== --}}
            @if ($hospitals->isNotEmpty())
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2"><i
                            class="fas fa-hospital-alt text-fuchsia-600 mr-2"></i> Found Hospitals
                        ({{ $hospitals->count() }})</h2>

                    <div class="space-y-6">
                        @foreach ($hospitals as $hospital)
                            @php
                                $rating = $hospital->avg_rating ?? 0;
                                // Ensure departments are available, even if empty
                                $departmentsJson = $hospital->departments
                                    ? json_encode($hospital->departments->pluck('name', 'id'))
                                    : json_encode([]);
                            @endphp
                            <div
                                class="bg-white rounded-xl shadow-md p-5 flex flex-col sm:flex-row items-center sm:items-start space-x-5 border border-fuchsia-100 hover:shadow-lg transition duration-300">

                                <div class="flex-shrink-0 mb-3 sm:mb-0">
                                    <img src="{{ asset($hospital->main_image ?? 'img/hospital-default.jpg') }}"
                                        alt="{{ $hospital->name }} Logo"
                                        class="w-16 h-16 object-contain rounded-lg border border-gray-200 p-1">
                                </div>

                                <div class="flex-grow text-center sm:text-left">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <a href="{{ route('hospital.show', ['hospital_id' => $hospital->hospital_id]) ?? '#' }}"
                                            class="hover:text-fuchsia-600 transition">{{ $hospital->name }}</a>
                                    </h3>

                                    <p class="text-sm text-gray-600 mt-1"><i
                                            class="fas fa-map-marker-alt text-fuchsia-500 mr-1"></i>
                                        {{ $hospital->city ?? 'N/A' }}, {{ $hospital->state ?? 'N/A' }}</p>

                                    <div class="flex items-center justify-center sm:justify-start mt-2">
                                        <span class="text-yellow-500 text-sm">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= round($rating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                                            @endfor
                                        </span>
                                        <span class="ml-2 text-xs text-gray-600">({{ number_format($rating, 1) }}/5) -
                                            {{ $hospital->hospital_type ?? 'Clinic' }}</span>
                                    </div>

                                    @if (isset($hospital->distance))
                                        <p class="text-sm font-semibold text-fuchsia-600 mt-1">
                                            <i class="fas fa-location-arrow mr-1"></i> {{ round($hospital->distance, 1) }}
                                            km away
                                        </p>
                                    @endif
                                </div>

                                {{-- Actions (View Details and Book Appointment) --}}
                                <div class="flex-shrink-0 mt-3 sm:mt-0 flex flex-col gap-2">
                                    <a href="{{ route('hospital.show', ['hospital_id' => $hospital->hospital_id]) ?? '#' }}"
                                        class="btn btn-primary bg-fuchsia-600 text-white text-sm px-4 py-2 rounded-full hover:bg-fuchsia-700 text-center">View
                                        Details</a>

                                    {{-- Standard Hospital Booking Button --}}
                                    <button
                                        onclick="openHospitalBookingModalSearch('{{ $hospital->hospital_id }}', '{{ $hospital->name }}', {{ $departmentsJson }})"
                                        class="bg-green-600 text-white text-sm px-4 py-2 rounded-full hover:bg-green-700 transition duration-300 shadow-md">
                                        <i class="fas fa-calendar-check mr-1"></i> Book Now
                                    </button>

                                      @if ($hospital->address)
                                @php
                                    $hospitalAddress = trim(
                                        $hospital->address .
                                            ' ' .
                                            ($hospital->city ?? '') .
                                            ' ' .
                                            ($hospital->state ?? ''),
                                    );
                                    $mapUrl =
                                        'https://www.google.com/maps/search/?api=1&query=' .
                                        urlencode($hospitalAddress);
                                @endphp
                                    @if (!empty($doctor->address))
                                    <a href="{{ $doctorPrimaryMapUrl }}" target="_blank"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-full transition shadow-md">
                                        <i class="fas fa-route mr-2"></i> View Map
                                    </a>
                                @endif
                            @endif


                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- ======================== MODAL STRUCTURES =============================== --}}
    {{-- ========================================================================= --}}


    {{-- DOCTOR BOOKING MODAL (Without Department) --}}
    <div id="doctorBookingModalSearch" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeDoctorBookingModalSearch()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-2xl font-bold text-gray-900"><i class="fas fa-calendar-check text-fuchsia-600 mr-2"></i>
                    Book Appointment</h3>
                <button onclick="closeDoctorBookingModalSearch()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Request appointment with Dr. <strong id="modalDoctorNameDisplay"></strong> at:
                <strong id="modalHospitalNameDoctor">Select Clinic Below</strong>
            </p>

            <div id="doctorBookingResponse" class="p-3 rounded-lg text-sm font-semibold hidden mb-4"></div>

            <form id="doctorAppointmentFormSearch" method="POST" onsubmit="handleDoctorBookingSubmitSearch(event)"
                class="space-y-4">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="doctor_id" id="doctorIdForFormSearch" value="">

                <div id="doctorFormFieldsContainerSearch">

                    {{-- Hospital Selection Field --}}
                    <select name="hospital_id" id="hospital_id_select_search" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500"
                        onchange="updateModalHospitalNameDoctor()">
                        <option value="">Select Hospital/Clinic *</option>
                        {{-- Options will be populated by JS --}}
                    </select>

                    {{-- User Info --}}
                    <input type="text" name="name" placeholder="Your Full Name *" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="tel" name="phone_number" placeholder="Phone Number *" required
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        <input type="email" name="email" placeholder="Email Address"
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                    </div>

                    {{-- Date/Time --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label for="appointment_date_doctor"
                                class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                                Date *</label>
                            <input type="date" name="appointment_date" id="appointment_date_doctor" required
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        </div>

                        <div>
                            <label for="appointment_time_doctor"
                                class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                                Time *</label>
                            <input type="time" name="appointment_time" id="appointment_time_doctor" required
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        </div>
                    </div>

                    <textarea name="message" rows="3" placeholder="Reason for visit / Message (Optional)"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500"></textarea>

                    <button type="submit" id="doctorBookingSubmitBtnSearch"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-md">
                        Submit Appointment Request
                    </button>
                </div>

                <a href="javascript:void(0)" id="callNowAfterDoctorBookingSearch"
                    class="hidden w-full text-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01] mt-3 flex items-center justify-center whitespace-nowrap">
                    <i class="fas fa-phone-alt mr-2"></i> Call Hospital Now
                </a>
            </form>
        </div>
    </div>


    {{-- BOOKING MODAL --}}
    <div id="bookingModal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeBookingModal()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-2xl font-bold text-gray-900"><i class="fas fa-calendar-check text-fuchsia-600 mr-2"></i>
                    Book Appointment</h3>
                <button onclick="closeBookingModal()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>

            <p class="text-sm text-gray-600 mb-4">Request appointment with Dr. **{{ $doctor->name }}** at: <strong
                    id="modalHospitalName"></strong></p>

            <form id="appointmentForm" method="POST" onsubmit="handleDoctorBookingSubmit(event)" class="space-y-4">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="doctor_id" id="doctorIdForForm" value="{{ $doctor->doctor_id }}">

                <div id="bookingResponse" class="p-3 rounded-lg text-sm font-semibold hidden"></div>

                {{-- Hospital Selection Field (Where the appointment will take place) --}}
                {{-- @if ($doctor->hospitals->isNotEmpty())
                    <select name="hospital_id" id="hospital_id_select" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        <option value="">Select Hospital/Clinic *</option>
                        @foreach ($doctor->hospitals as $hospital)
                            <option value="{{ $hospital->id }}" data-name="{{ $hospital->name }}">
                                {{ $hospital->name }} ({{ $hospital->city }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="hospital_id" value="">
                    <p class="text-red-500 text-sm font-medium">No associated hospitals found for booking.</p>
                @endif --}}

                {{-- User Info --}}
                <input type="text" name="name" placeholder="Your Full Name *" required
                    class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="tel" name="phone_number" placeholder="Phone Number *" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                    <input type="email" name="email" placeholder="Email Address"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                </div>

                {{-- Date/Time --}}
                {{-- BOOKING MODAL - Date/Time Section --}}

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">

                    <div>
                        <label for="appointment_date" class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                            Date *</label>
                        <input type="date" name="appointment_date" id="appointment_date" required
                            min="{{ now()->format('Y-m-d') }}"
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                    </div>

                    <div>
                        <label for="appointment_time" class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                            Time *</label>
                        <input type="time" name="appointment_time" id="appointment_time" required
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                    </div>
                </div>

                <textarea name="message" rows="3" placeholder="Reason for visit / Message (Optional)"
                    class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500"></textarea>

                <div id="formFieldsContainer">
                    <button type="submit" id="bookingSubmitBtn"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-md">
                        Submit Appointment Request
                    </button>
                </div>

                {{-- Success Flow: Call Now Button (FIXED: Added flex classes for proper alignment) --}}
                <a href="tel:{{ $doctor->mobile_number }}" id="callNowAfterBooking"
                    class="hidden w-full text-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01] mt-3 flex items-center justify-center whitespace-nowrap">
                    <i class="fas fa-phone-alt mr-2"></i> Call Now
                </a>
            </form>
        </div>
    </div>

    {{-- HOSPITAL BOOKING MODAL (With Department) --}}
    <div id="hospitalBookingModalSearch" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeHospitalBookingModalSearch()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-2xl font-bold text-gray-900"><i class="fas fa-calendar-check text-fuchsia-600 mr-2"></i>
                    Book Appointment</h3>
                <button onclick="closeHospitalBookingModalSearch()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>

            <p class="text-sm text-gray-600 mb-4">Request an appointment at: <strong
                    id="modalHospitalNameHospital"></strong></p>

            <div id="hospitalBookingResponse" class="p-3 rounded-lg text-sm font-semibold hidden mb-4"></div>

            <form id="hospitalAppointmentFormSearch" method="POST" onsubmit="handleHospitalBookingSubmitSearch(event)"
                class="space-y-4">
                @csrf

                <input type="hidden" name="hospital_id" id="hospitalIdForFormHospital" value="">

                <div id="hospitalFormFieldsContainerSearch">

                    {{-- Department Selection Field --}}
                    <select name="department_id" id="department_id_select_hospital" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-3">
                        <option value="">Select Department *</option>
                    </select>

                    {{-- User Info --}}
                    <input type="text" name="name" placeholder="Your Full Name *" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-3">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                        <input type="tel" name="phone_number" placeholder="Phone Number *" required
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        <input type="email" name="email" placeholder="Email Address"
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                    </div>

                    {{-- Date/Time --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label for="appointment_date_hospital"
                                class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                                Date *</label>
                            <input type="date" name="appointment_date" id="appointment_date_hospital" required
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        </div>
                        <div>
                            <label for="appointment_time_hospital"
                                class="text-xs font-semibold text-gray-700 block mb-1">Preferred
                                Time *</label>
                            <input type="time" name="appointment_time" id="appointment_time_hospital" required
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        </div>
                    </div>

                    <textarea name="message" rows="3" placeholder="Reason for visit / Message (Optional)"
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-4"></textarea>

                    <button type="submit" id="hospitalBookingSubmitBtnSearch"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-md">
                        Submit Appointment Request
                    </button>
                </div>

                <a href="javascript:void(0)" id="callNowAfterHospitalBookingSearch"
                    class="hidden w-full text-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01] mt-3 flex items-center justify-center whitespace-nowrap">
                    <i class="fas fa-phone-alt mr-2"></i> Call Hospital Now
                </a>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- ======================== JAVASCRIPT FUNCTIONS =========================== --}}
    {{-- ========================================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial date constraint for all date fields
            const today = new Date().toISOString().split('T')[0];
            const dateDoctor = document.getElementById('appointment_date_doctor');
            const dateHospital = document.getElementById('appointment_date_hospital');
            if (dateDoctor) dateDoctor.min = today;
            if (dateHospital) dateHospital.min = today;
        });

        // ===============================================
        //           DOCTOR BOOKING FUNCTIONS (SEARCH)
        // ===============================================

        /**
         * Populates hospital options in the doctor booking modal.
         * @param {string} doctorId 
         * @param {string} doctorName 
         * @param {string} hospitalOptionsJson JSON string of hospital options from Blade.
         */
        // --- Booking Modal Functions ---
        function openDoctorBookingModal(doctorId = '{{ $doctor->doctor_id }}', hospitalId = null, hospitalName = null) {
            const modal = document.getElementById('bookingModal');
            const form = document.getElementById('appointmentForm');
            const hospitalSelect = document.getElementById('hospital_id_select');
            const modalHospitalNameDisplay = document.getElementById('modalHospitalName');

            // Restore form visibility and state
            form.querySelector('#formFieldsContainer').classList.remove('hidden'); // Use ID to target the container
            document.getElementById('callNowAfterBooking').classList.add('hidden');
            document.getElementById('bookingResponse').classList.add('hidden');

            // Set the dynamic action URL
            form.action = `/doctor/${doctorId}/book`;
            document.getElementById('doctorIdForForm').value = doctorId;

            // Function to handle hospital name display
            function updateHospitalNameDisplay() {
                const selectedOption = hospitalSelect ? hospitalSelect.options[hospitalSelect.selectedIndex] : null;
                const initialName = (hospitalSelect && selectedOption && selectedOption.dataset.name) ? selectedOption
                    .dataset.name : 'Direct Appointment';
                modalHospitalNameDisplay.textContent = initialName;
            }

            // Initial setup and change listener
            if (hospitalSelect) {
                hospitalSelect.onchange = updateHospitalNameDisplay;
                // Pre-select the hospital if function is called from Associated Hospital button
                if (hospitalId) {
                    hospitalSelect.value = hospitalId;
                }
            }
            updateHospitalNameDisplay();

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // --- AJAX Booking Submit ---
        async function handleDoctorBookingSubmit(event) {
            event.preventDefault();

            const form = document.getElementById('appointmentForm');
            const submitBtn = document.getElementById('bookingSubmitBtn');
            const formFieldsContainer = document.getElementById('formFieldsContainer');
            const formData = new FormData(form);

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            document.getElementById('bookingResponse').classList.add('hidden');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showResponse(data.success || 'Appointment submitted!', true);
                    form.reset();

                    // UX FIX: Hide Form fields, Show Success Call Button
                    formFieldsContainer.classList.add('hidden'); // Hide fields container
                    document.getElementById('callNowAfterBooking').classList.remove('hidden'); // Show call button

                } else {
                    let errorMessage = 'An unexpected error occurred.';

                    if (response.status === 422 && data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        errorMessage = firstError;
                    } else if (data.error) {
                        errorMessage = data.error;
                    }
                    showResponse(`Error: ${errorMessage}`, false);
                }
            } catch (error) {
                console.error('AJAX Error:', error);
                showResponse('Network error. Please try again later.', false);
            } finally {
                // If submission failed, re-enable button and clear loader
                if (document.getElementById('callNowAfterBooking').classList.contains('hidden')) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        }

        function showResponse(message, isSuccess) {
            const responseDiv = document.getElementById('bookingResponse');
            responseDiv.textContent = message;
            responseDiv.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'border',
                'border-green-300', 'border-red-300');

            if (isSuccess) {
                responseDiv.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-300');
            } else {
                responseDiv.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-300');
            }
            responseDiv.classList.remove('hidden'); // Ensure message is visible
        }
        //           HOSPITAL BOOKING FUNCTIONS (SEARCH)
        // ===============================================

        /**
         * Opens the standard hospital booking modal.
         * @param {string} hospitalId 
         * @param {string} hospitalName 
         * @param {object} departments A {id: name} map for departments.
         */
        function openHospitalBookingModalSearch(hospitalId, hospitalName = 'N/A', departments = {}) {
            const modal = document.getElementById('hospitalBookingModalSearch');
            const form = document.getElementById('hospitalAppointmentFormSearch');
            const departmentSelect = document.getElementById('department_id_select_hospital');

            // --- 1. Reset state ---
            form.reset();
            document.getElementById('hospitalFormFieldsContainerSearch').classList.remove('hidden');
            document.getElementById('callNowAfterHospitalBookingSearch').classList.add('hidden');
            document.getElementById('hospitalBookingResponse').classList.add('hidden');
            document.getElementById('hospitalBookingSubmitBtnSearch').disabled = false;
            document.getElementById('hospitalBookingSubmitBtnSearch').innerHTML =
                '<i class="fas fa-calendar-check mr-2"></i> Submit Appointment Request';

            // --- 2. Set dynamic data ---
            document.getElementById('hospitalIdForFormHospital').value = hospitalId;
            document.getElementById('modalHospitalNameHospital').textContent = hospitalName;
            form.action = `/hospital/${hospitalId}/book`;

            // --- 3. Populate Department Select ---
            departmentSelect.innerHTML = '<option value="">Select Department *</option>';
            departmentSelect.required = true;

            if (Object.keys(departments).length > 0) {
                for (const id in departments) {
                    if (departments.hasOwnProperty(id)) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = departments[id];
                        departmentSelect.appendChild(option);
                    }
                }
            } else {
                departmentSelect.innerHTML = '<option value="">No departments listed (Select this option)</option>';
                departmentSelect.required = false; // Make department optional if none are listed
            }

            // --- 4. Open Modal ---
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeHospitalBookingModalSearch() {
            document.getElementById('hospitalBookingModalSearch').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        async function handleHospitalBookingSubmitSearch(event) {
            event.preventDefault();
            const form = document.getElementById('hospitalAppointmentFormSearch');
            const submitBtn = document.getElementById('hospitalBookingSubmitBtnSearch');
            const formFieldsContainer = document.getElementById('hospitalFormFieldsContainerSearch');
            const responseDiv = document.getElementById('hospitalBookingResponse');
            const formData = new FormData(form);

            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            responseDiv.classList.add('hidden');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showBookingResponseSearch(data.success || 'Appointment submitted successfully!', true,
                        'hospitalBookingResponse');
                    form.reset();
                    formFieldsContainer.classList.add('hidden');
                    document.getElementById('callNowAfterHospitalBookingSearch').classList.remove('hidden');

                } else {
                    let errorMessage = 'An unexpected error occurred.';

                    if (response.status === 422 && data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        errorMessage = firstError;
                    } else if (data.error) {
                        errorMessage = data.error;
                    }
                    showBookingResponseSearch(`Error: ${errorMessage}`, false, 'hospitalBookingResponse');
                }
            } catch (error) {
                console.error('AJAX Error:', error);
                showBookingResponseSearch('Network error. Please try again later.', false, 'hospitalBookingResponse');
            } finally {
                if (formFieldsContainer.classList.contains('hidden') === false) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        }


        // ===============================================
        //                GLOBAL UTILITY
        // ===============================================

        /**
         * Shows the AJAX response message.
         * @param {string} message 
         * @param {boolean} isSuccess 
         * @param {string} responseDivId 
         */
        function showBookingResponseSearch(message, isSuccess, responseDivId) {
            const responseDiv = document.getElementById(responseDivId);
            responseDiv.textContent = message;
            responseDiv.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'border',
                'border-green-300', 'border-red-300');

            if (isSuccess) {
                responseDiv.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-300');
                responseDiv.classList.remove('bg-red-100', 'text-red-800', 'border-red-300');
            } else {
                responseDiv.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-300');
                responseDiv.classList.remove('bg-green-100', 'text-green-800', 'border-green-300');
            }
            responseDiv.classList.remove('hidden');
        }
    </script>
    <script src="https://kit.fontawesome.com/a2e0e6a89b.js" crossorigin="anonymous"></script>
@endsection
