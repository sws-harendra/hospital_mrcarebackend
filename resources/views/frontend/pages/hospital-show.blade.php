@extends('frontend.layouts.base')

@push('title')
    <title>{{ $hospital->name }} | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <section class="bg-gray-50 min-h-screen py-8 lg:py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 flex flex-col lg:flex-row gap-8">

            <div class="flex-1 space-y-6">

                {{-- 1. HOSPITAL HEADER & QUICK ACTIONS (Same as before) --}}
                <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 border-t-8 border-fuchsia-600">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                        <img src="{{ asset($hospital->logo ?? 'img/hospital-default.jpg') }}" alt="{{ $hospital->name }} Logo"
                            class="w-32 h-32 object-contain rounded-xl border-4 border-gray-200 shadow-xl flex-shrink-0 mx-auto sm:mx-0 p-1 bg-white">

                        <div class="flex-1 text-center sm:text-left">
                            <h1 class="text-4xl font-extrabold text-gray-900 leading-snug">{{ $hospital->name }}</h1>

                            <div class="mt-2 flex items-center justify-center sm:justify-start flex-wrap gap-2">
                                @if ($hospital->hospital_type)
                                    <span
                                        class="bg-fuchsia-100 text-fuchsia-800 text-sm font-medium px-3 py-1 rounded-full">{{ $hospital->hospital_type }}</span>
                                @endif
                                @if ($hospital->is_verified)
                                    <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full"><i
                                            class="fas fa-check-circle"></i> Verified</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-center sm:justify-start mt-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star {{ $i <= round($averageRating) ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600 font-medium">({{ number_format($averageRating, 1) }}
                                    / 5 from {{ $reviews->count() }} Reviews)</span>
                            </div>

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
                                <a href="{{ $mapUrl }}" target="_blank"
                                    class="text-sm text-gray-500 mt-2 flex items-center justify-center sm:justify-start hover:text-fuchsia-700 hover:underline transition cursor-pointer">
                                    <i class="fas fa-map-marker-alt text-fuchsia-600 mr-2"></i>
                                    {{ $hospitalAddress }}
                                </a>
                            @endif

                            {{-- INLINE ACTIONS (Book, Map, Share, TIMINGS) --}}
                            <div class="flex justify-center sm:justify-start gap-3 mt-4 flex-wrap">
                                <button onclick="openBookingModal('{{ $hospital->hospital_id }}')"
                                    class="bg-fuchsia-700 hover:bg-fuchsia-800 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                    <i class="fas fa-calendar-check mr-2"></i> Book Now
                                </button>

                                @if (!empty($hospital->address))
                                    <a href="{{ $mapUrl }}" target="_blank"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                        <i class="fas fa-route mr-2"></i> View Map
                                    </a>
                                @endif

                                {{-- TIMINGS BUTTON / DISPLAY (Always opens modal) --}}
                                @if ($hospital->businessHours->isNotEmpty())
                                    <button onclick="openTimingsModal()"
                                        class="bg-fuchsia-100 text-fuchsia-800 hover:bg-fuchsia-200 font-semibold px-4 py-2 rounded-lg transition shadow-md text-sm">
                                        <i class="fas fa-clock mr-2"></i> View Timings
                                    </button>
                                @endif

                                <button id="shareButtonHeader" onclick="handleShareClick()"
                                    class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                    <i class="fas fa-share-alt mr-2"></i> Share
                                </button>

                            </div>
                            <span id="shareMessageGlobal"
                                class="hidden text-xs font-medium text-green-600 mt-2 w-full"></span>

                        </div>
                    </div>
                </div>



                {{-- 3. KEY METRICS --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i class="fas fa-hospital text-fuchsia-600 mr-3"></i>
                        Hospital Infrastructure</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-base text-gray-700">
                        @if ($hospital->number_of_beds)
                            <p><strong><i class="fas fa-bed text-gray-500 w-5"></i> Beds:</strong>
                                <span class="text-gray-900 font-semibold">{{ $hospital->number_of_beds }}</span>
                            </p>
                        @endif
                        @if ($hospital->number_of_doctors)
                            <p><strong><i class="fas fa-user-md text-gray-500 w-5"></i> Doctors:</strong>
                                <span class="text-gray-900 font-semibold">{{ $hospital->number_of_doctors }}</span>
                            </p>
                        @endif
                        @if ($hospital->number_of_nurses)
                            <p><strong><i class="fas fa-user-nurse text-gray-500 w-5"></i> Nurses:</strong>
                                <span class="text-gray-900 font-semibold">{{ $hospital->number_of_nurses }}</span>
                            </p>
                        @endif
                        @if ($hospital->established_date)
                            <p><strong><i class="fas fa-calendar-alt text-gray-500 w-5"></i> Est. Date:</strong>
                                {{ $hospital->established_date->format('Y') }}</p>
                        @endif
                    </div>
                </div>

                {{-- 4. DEPARTMENTS & DOCTORS (Same as before) --}}
                @if ($hospital->departments->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-sitemap text-fuchsia-600 mr-3"></i> Departments & Specialists</h2>

                        <div class="flex flex-wrap gap-3 mb-5 department-list">
                            @foreach ($hospital->departments as $dept)
                                <button
                                    class="dept-btn bg-fuchsia-100 hover:bg-fuchsia-700 hover:text-white text-fuchsia-800 px-4 py-2 rounded-full text-sm font-medium transition"
                                    data-id="{{ $dept->id }}">
                                    {{ $dept->name }} ({{ count($departmentDoctors[$dept->id] ?? []) }})
                                </button>
                            @endforeach
                        </div>

                        <div id="departmentDoctorsContainer">
                            <div class="text-gray-500 text-base italic p-4 border border-gray-200 rounded-lg bg-gray-50">←
                                Select a department above to view associated doctors.</div>
                        </div>
                    </div>
                @endif

                {{-- 2. ABOUT --}}
                @if ($hospital->description)
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-info-circle text-fuchsia-600 mr-3"></i> About {{ $hospital->name }}</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $hospital->description }}</p>
                    </div>
                @endif

                {{-- 5. PHOTOS --}}
                @if ($hospital->photos->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-images text-fuchsia-600 mr-3"></i> Photo Gallery</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($hospital->photos->take(4) as $photo)
                                <div class="relative group overflow-hidden rounded-lg shadow-md cursor-pointer gallery-image"
                                    data-full-src="{{ asset($photo->photo_path) }}">
                                    <img src="{{ asset($photo->photo_path) }}"
                                        class="object-cover w-full h-44 group-hover:scale-105 transition" />
                                    @if ($photo->caption)
                                        <div
                                            class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-xs text-center py-1 opacity-0 group-hover:opacity-100 transition">
                                            {{ $photo->caption }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($hospital->photos->count() > 4)
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">More Photos (Scroll)</h3>
                                <div class="flex overflow-x-auto space-x-4 py-2 no-scrollbar">
                                    @foreach ($hospital->photos->skip(4) as $photo)
                                        <img src="{{ asset($photo->photo_path) }}" alt="Hospital Photo"
                                            class="gallery-image h-36 w-52 flex-shrink-0 rounded-lg object-cover shadow-md border border-gray-200 hover:scale-105 transition duration-300 cursor-pointer"
                                            data-full-src="{{ asset($photo->photo_path) }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif


                {{-- 6. REVIEWS (Same as before) --}}
                <div id="reviews" class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i class="fas fa-comments text-fuchsia-600 mr-3"></i>
                        Patient Reviews ({{ $reviews->count() }})</h2>

                    <div class="space-y-4 mb-6">
                        @forelse($reviews as $review)
                            <div class="p-4 border rounded-xl bg-gray-50 flex gap-4 items-start">
                                <img src="{{ $review->image ? asset($review->image) : 'https://ui-avatars.com/api/?name=' . urlencode($review->name) . '&background=fuchsia&color=fff' }}"
                                    class="w-12 h-12 rounded-full object-cover flex-shrink-0 shadow-sm">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">{{ $review->name }}</p>
                                    <div class="text-yellow-500 text-sm mb-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-xs text-gray-500">({{ $review->rating }}/5)</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-2">{{ $review->comment }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Reviewed:
                                        {{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-base italic p-4 border rounded-lg bg-gray-100">No patient reviews
                                available yet.</p>
                        @endforelse
                    </div>

                    {{-- REVIEW FORM --}}
                    <form action="{{ route('hospital.review.store', $hospital->hospital_id) ?? '#' }}" method="POST"
                        enctype="multipart/form-data"
                        class="mt-6 space-y-4 p-6 border border-fuchsia-300 rounded-xl bg-fuchsia-50">
                        @csrf
                        <h3 class="text-xl font-bold text-fuchsia-800 border-b border-fuchsia-300 pb-2">Write a Review</h3>

                        <div class="flex flex-col">
                            <label class="text-sm font-semibold text-gray-700 mb-2">Your Rating <span
                                    class="text-red-500">*</span></label>
                            <div class="flex space-x-1 rating-stars">
                                @for ($i = 5; $i >= 1; $i--)
                                    <label for="h_rating-{{ $i }}"
                                        class="cursor-pointer text-gray-400 text-2xl transition-colors hover:text-yellow-500">
                                        <i class="fas fa-star"></i>
                                        <input type="radio" name="rating" id="h_rating-{{ $i }}"
                                            value="{{ $i }}" class="hidden" required>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="Your Name *"
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500"
                                required>
                            <input type="email" name="email" placeholder="Your Email *"
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500"
                                required>
                        </div>

                        <input type="file" name="image" accept="image/*"
                            class="w-full border border-gray-400 bg-white rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-fuchsia-100 file:text-fuchsia-700 hover:file:bg-fuchsia-200 cursor-pointer">

                        <textarea name="comment" placeholder="Write your feedback..."
                            class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500"
                            required></textarea>

                        <button type="submit"
                            class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-md">
                            Submit Review
                        </button>
                    </form>
                </div>
            </div>

            <div class="w-full lg:w-[350px] space-y-6 flex-shrink-0">

                <div class="sticky top-4 bg-white rounded-xl shadow-xl p-6 border-t-4 border-fuchsia-600">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Book & Contact</h3>

                    <button onclick="openBookingModal('{{ $hospital->hospital_id }}')"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01]">
                        <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                    </button>

                    @if ($hospital->phone_number)
                        {{-- Show Number button opens the booking modal --}}
                        <button onclick="openBookingModal('{{ $hospital->hospital_id }}')"
                            class="w-full block text-center mt-3 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01]">
                            <i class="fas fa-phone-alt mr-2"></i> Show Number
                        </button>
                    @endif

                    @if ($hospital->whatsapp_number)
                        <a href="https://wa.me/{{ $hospital->whatsapp_number }}" target="_blank"
                            class="w-full block text-center mt-3 border border-fuchsia-700 text-fuchsia-700 hover:bg-fuchsia-700 hover:text-white py-2 rounded-lg font-semibold transition duration-300">
                            <i class="fab fa-whatsapp mr-2"></i> Message on WhatsApp
                        </a>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><i
                            class="fas fa-clock text-fuchsia-600 mr-3"></i> Hospital Timings</h2>
                    <div class="divide-y divide-gray-100">
                        @foreach ($hospital->businessHours as $hour)
                            <div class="flex py-2 text-base text-gray-700">
                                <span class="capitalize font-medium w-1/3 flex-shrink-0">{{ $hour->day }}</span>
                                <span class="flex-1 text-right">
                                    @if ($hour->is_closed)
                                        <span class="text-red-600 font-semibold">Closed</span>
                                    @elseif ($hour->is_emergency_24_7)
                                        <span class="text-green-600 font-semibold">24/7 Emergency</span>
                                    @else
                                        <span class="text-gray-900 font-semibold text-sm">
                                            {{ \Carbon\Carbon::parse($hour->open_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($hour->close_time)->format('h:i A') }}
                                        </span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><i
                            class="fas fa-share-alt text-fuchsia-600 mr-3"></i> Connect Online</h2>

                    @if ($hospital->website)
                        <a href="{{ $hospital->website }}" target="_blank"
                            class="flex items-center gap-3 p-3 my-3 border rounded-lg hover:bg-fuchsia-50 text-fuchsia-700 transition font-medium">
                            <i class="fas fa-globe text-lg"></i>
                            <span class="truncate">Visit Official Website</span>
                        </a>
                    @endif

                    <div class="flex flex-wrap gap-5 mt-4 justify-center">
                        @if ($hospital->facebook)
                            <a href="{{ $hospital->facebook }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Facebook"><i
                                    class="fab fa-facebook-square text-3xl"></i></a>
                        @endif
                        @if ($hospital->twitter)
                            <a href="{{ $hospital->twitter }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Twitter"><i
                                    class="fab fa-twitter-square text-3xl"></i></a>
                        @endif
                        @if ($hospital->linkedin)
                            <a href="{{ $hospital->linkedin }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="LinkedIn"><i
                                    class="fab fa-linkedin text-3xl"></i></a>
                        @endif
                        @if ($hospital->instagram)
                            <a href="{{ $hospital->instagram }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Instagram"><i
                                    class="fab fa-instagram-square text-3xl"></i></a>
                        @endif
                        @if ($hospital->youtube)
                            <a href="{{ $hospital->youtube }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="YouTube"><i
                                    class="fab fa-youtube-square text-3xl"></i></a>
                        @endif
                    </div>
                </div>

                {{-- SIMILAR HOSPITALS SECTION (Same as before) --}}
                @if (isset($similarHospitals) && $similarHospitals->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-hospital-alt text-fuchsia-600 mr-3"></i> Similar Hospitals
                        </h2>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($similarHospitals as $simHospital)
                                @php
                                    $simAvgRating = $simHospital->averageRating ?? 0;
                                    $imagePath = $simHospital->logo
                                        ? asset($simHospital->logo)
                                        : (isset($simHospital->photos[0])
                                            ? asset($simHospital->photos[0]->photo_path)
                                            : asset('img/hospital-default.jpg'));
                                @endphp

                                <div
                                    class="block border border-gray-200 rounded-xl p-3 bg-gray-50 hover:shadow-md hover:bg-white transition duration-300 group">
                                    <a href="{{ route('hospital.show', ['hospital_id' => $simHospital->hospital_id]) ?? '#' }}"
                                        class="block">

                                        <div class="flex items-center gap-3">
                                            <img src="{{ $imagePath }}" alt="{{ $simHospital->name }}"
                                                class="w-12 h-12 object-contain rounded-full flex-shrink-0 border-2 border-fuchsia-200 group-hover:border-fuchsia-600 transition-colors">

                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-sm text-gray-800 truncate">
                                                    {{ $simHospital->name }}</div>
                                                <div class="text-xs text-fuchsia-700 font-medium truncate">
                                                    {{ $simHospital->hospital_type ?? 'Hospital' }}</div>
                                                <div class="text-xs text-gray-500 truncate">
                                                    {{ $simHospital->address ?? '' }}
                                                </div>

                                                <div class="flex items-center mt-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= round($simAvgRating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                                                    @endfor
                                                    <span
                                                        class="ml-1 text-xs text-gray-600">({{ number_format($simAvgRating, 1) }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- <div class="flex space-x-3 mt-3">
                                        <a href="tel:{{ $simHospital->phone_number }}"
                                            class="flex-1 flex items-center justify-center bg-fuchsia-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-fuchsia-700 transition duration-150 shadow-md"
                                            title="Call Now">
                                            <i class="fas fa-phone mr-2"></i> Call
                                        </a>

                                        <a href="javascript:void(0)" onclick="openBookingModal('{{ $simHospital->hospital_id }}')"
                                            class="flex-1 flex items-center justify-center border border-fuchsia-600 text-fuchsia-600 text-sm font-medium py-2 rounded-lg hover:bg-fuchsia-600 hover:text-white transition duration-150"
                                            title="Book Appointment">
                                            <i class="fas fa-calendar-check mr-2"></i> Book
                                        </a>
                                    </div> --}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>

    {{-- BUSINESS HOURS MODAL (For Timings Display) --}}
    <div id="timingsModal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeTimingsModal()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-900">Business Timings</h3>
                <button onclick="closeTimingsModal()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($hospital->businessHours as $hour)
                    <div class="flex justify-between py-2 text-base text-gray-700">
                        <span class="capitalize font-medium">{{ $hour->day }}</span>
                        <span class="text-right">
                            @if ($hour->is_closed)
                                <span class="text-red-600 font-semibold">Closed</span>
                            @elseif ($hour->is_emergency_24_7)
                                <span class="text-green-600 font-semibold">24/7 Emergency</span>
                            @else
                                <span class="text-gray-900 font-semibold text-sm">
                                    {{ \Carbon\Carbon::parse($hour->open_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($hour->close_time)->format('h:i A') }}
                                </span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    {{-- BOOKING MODAL (FIXED SPACING AND SUCCESS FLOW) --}}
    <div id="bookingModal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeBookingModal()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-2xl font-bold text-gray-900"><i class="fas fa-calendar-check text-fuchsia-600 mr-2"></i>
                    Book Appointment</h3>
                <button onclick="closeBookingModal()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>

            <p class="text-sm text-gray-600 mb-4">Request an appointment at: <strong
                    id="modalHospitalName">{{ $hospital->name }}</strong></p>

            <div id="bookingResponse" class="p-3 rounded-lg text-sm font-semibold hidden"></div>

            <form id="appointmentForm" method="POST" onsubmit="handleBookingSubmit(event)" class="space-y-4">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="hospital_id_for_form" id="hospitalIdForForm"
                    value="{{ $hospital->hospital_id }}">

                <div id="formFieldsContainer">

                    {{-- Department Selection Field (FIXED MARGIN) --}}
                    @if ($hospital->departments->isNotEmpty())
                        <select name="department_id" id="department_id" required
                            class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-3">
                            <option value="">Select Department *</option>
                            @foreach ($hospital->departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="department_id" value="">
                        <p class="text-red-500 text-sm font-medium mb-3">No associated departments found for booking.</p>
                    @endif

                    {{-- User Info (FIXED MARGINS) --}}
                    <input type="text" name="name" placeholder="Your Full Name *" required
                        class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-3">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
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
                        class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500 mb-4"></textarea>

                    <button type="submit" id="bookingSubmitBtn"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-md">
                        Submit Appointment Request
                    </button>
                </div>

                {{-- Success Flow: Call Now Button --}}
                @if ($hospital->phone_number)
                    <a href="tel:{{ $hospital->phone_number }}" id="callNowAfterBooking"
                        class="hidden w-full text-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01] flex items-center justify-center whitespace-nowrap">
                        <i class="fas fa-phone-alt mr-2"></i> Call Now: {{ $hospital->phone_number }}
                    </a>
                @endif
            </form>
        </div>
    </div>


    {{-- Modal/Lightbox Structure (For Gallery) --}}
    <div id="imageModal" class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center hidden"
        onclick="closeModal()">
        <div class="relative max-w-5xl w-full mx-4" onclick="event.stopPropagation()">
            <button class="absolute top-4 right-4 text-white text-4xl hover:text-fuchsia-400 transition"
                onclick="closeModal()">×</button>
            <img id="modalImage" class="max-h-[90vh] w-auto mx-auto object-contain rounded-lg shadow-2xl" src=""
                alt="Gallery Image">
        </div>
    </div>


    {{-- INLINE SCRIPT FOR ALL FUNCTIONS --}}
    <script>
        // --- Business Hours Modal Functions ---
        function openTimingsModal() {
            document.getElementById('timingsModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeTimingsModal() {
            document.getElementById('timingsModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // --- Booking Modal Functions ---
        function openBookingModal(hospitalId = '{{ $hospital->hospital_id }}') {
            const modal = document.getElementById('bookingModal');
            const form = document.getElementById('appointmentForm');
            const modalHospitalNameDisplay = document.getElementById('modalHospitalName');

            // Restore form visibility and state
            document.getElementById('formFieldsContainer').classList.remove('hidden');
            document.getElementById('callNowAfterBooking').classList.add('hidden');
            document.getElementById('bookingResponse').classList.add('hidden');

            // Set the dynamic action URL
            form.action = `/hospital/${hospitalId}/book`;

            // Set hospital name display
            modalHospitalNameDisplay.textContent = '{{ $hospital->name }}';

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // --- AJAX Booking Submit ---
        async function handleBookingSubmit(event) {
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


        // --- Gallery Modal Functions (FIXED) ---
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        async function handleShareClick() {
            const profileUrl = window.location.href;
            const hospitalName = "{{ $hospital->name }}";
            const shareMessageEl = document.getElementById('shareMessageGlobal');

            shareMessageEl.classList.add('hidden');

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: `Check out ${hospitalName}'s profile on {{ env('APP_NAME') }}`,
                        url: profileUrl,
                    });
                } catch (error) {
                    console.error('Error sharing:', error);
                }
            } else {
                try {
                    await navigator.clipboard.writeText(profileUrl);
                    shareMessageEl.textContent = 'Link copied to clipboard!';
                    shareMessageEl.classList.remove('hidden');
                    setTimeout(() => {
                        shareMessageEl.classList.add('hidden');
                    }, 3000);
                } catch (error) {
                    shareMessageEl.textContent = 'Could not copy link.';
                    shareMessageEl.classList.remove('hidden');
                    setTimeout(() => {
                        shareMessageEl.classList.add('hidden');
                    }, 3000);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const headerShareButton = document.getElementById('shareButtonHeader');
            const sidebarBookButton = document.querySelector('.sticky button'); // Main Book Appt button

            // Attach share logic
            if (headerShareButton) {
                headerShareButton.addEventListener('click', handleShareClick);
            }

            // Attach main sticky book button
            if (sidebarBookButton) {
                sidebarBookButton.onclick = function() {
                    openBookingModal('{{ $hospital->hospital_id }}');
                };
            }

            // Attach Show Number button to open modal
            const sidebarShowNumberButton = document.querySelector('.sticky button:nth-child(3)');
            if (sidebarShowNumberButton) {
                sidebarShowNumberButton.onclick = function() {
                    openBookingModal('{{ $hospital->hospital_id }}');
                };
            }


            // Attach gallery click listener to all images
            document.querySelectorAll('.gallery-image').forEach(img => {
                img.addEventListener('click', function() {
                    openModal(this.getAttribute('data-full-src'));
                });
            });

            // Set initial date constraint for booking modal
            const appointmentDateInput = document.querySelector('#appointmentForm input[name="appointment_date"]');
            if (appointmentDateInput) {
                appointmentDateInput.min = new Date().toISOString().split('T')[0];
            }

            // --- Department Doctors Toggle Logic (Same as previous fix) ---
            const deptButtons = document.querySelectorAll('.dept-btn');
            const departmentDoctors = @json(isset($departmentDoctors) ? $departmentDoctors : []);
            const container = document.getElementById('departmentDoctorsContainer');
            const assetBaseUrl = '{{ asset('') }}';

            deptButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.dataset.id;
                    const doctors = Object.values(departmentDoctors[id] || {});

                    document.querySelectorAll('.dept-btn').forEach(b => {
                        b.classList.remove('bg-fuchsia-700', 'text-white', 'shadow-md');
                        b.classList.add('bg-fuchsia-100', 'text-fuchsia-800');
                    });
                    e.currentTarget.classList.add('bg-fuchsia-700', 'text-white', 'shadow-md');
                    e.currentTarget.classList.remove('bg-fuchsia-100', 'text-fuchsia-800');


                    if (doctors.length === 0) {
                        container.innerHTML =
                            `<div class='text-gray-500 text-base italic p-4 border border-gray-200 rounded-lg bg-gray-50'>No doctors found currently listed in this department.</div>`;
                        return;
                    }

                    let html =
                        `<div class='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-4'>`;
                    doctors.forEach(doc => {
                        const profileImage = doc.profile_image ? assetBaseUrl + doc
                            .profile_image : '{{ asset('img/default-doctor.png') }}';
                        const profileLink = `/doctor/${doc.id}`;

                        html += `
                        <a href="${profileLink}" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition transform hover:scale-[1.02] group block">
                            <img src="${profileImage}" 
                                class="w-20 h-20 rounded-full mx-auto mb-3 object-cover border-4 border-fuchsia-200 group-hover:border-fuchsia-600 transition">
                            <h3 class="text-gray-900 font-bold text-md truncate">${doc.name}</h3>
                            ${doc.specialization ? `<p class="text-xs text-fuchsia-700 mt-1 font-medium">${doc.specialization}</p>` : ''}
                            ${doc.experience ? `<p class="text-xs text-gray-500 mt-1">Exp: ${doc.experience}</p>` : ''}
                            <span class="text-fuchsia-700 text-xs font-semibold hover:underline mt-2 inline-block">View Profile →</span>
                        </a>`;
                    });
                    html += `</div>`;
                    container.innerHTML = html;
                });
            });


            // --- 4. Review Form Star Rating ---
            const ratingContainer = document.querySelector('.rating-stars');
            if (ratingContainer) {
                const stars = ratingContainer.querySelectorAll('label i');
                const inputs = ratingContainer.querySelectorAll('input[type="radio"]');

                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        const selectedValue = parseInt(input.value);
                        stars.forEach((star, index) => {
                            const starValue = 5 - index;
                            if (starValue <= selectedValue) {
                                star.classList.add('text-yellow-500');
                                star.classList.remove('text-gray-400');
                            } else {
                                star.classList.remove('text-yellow-500');
                                star.classList.add('text-gray-400');
                            }
                        });
                    });
                });

                // Visual effect on hover
                ratingContainer.addEventListener('mouseover', (e) => {
                    if (e.target.tagName === 'I') {
                        const starLabel = e.target.closest('label');
                        if (!starLabel) return;

                        const hoveredInput = starLabel.querySelector('input');
                        if (!hoveredInput) return;

                        const hoveredValue = parseInt(hoveredInput.value);

                        stars.forEach(star => {
                            const starInput = star.closest('label').querySelector('input');
                            const starValue = parseInt(starInput.value);

                            if (starValue <= hoveredValue) {
                                star.classList.add('text-yellow-400');
                            } else {
                                star.classList.remove('text-yellow-400');
                            }
                        });
                    }
                });

                ratingContainer.addEventListener('mouseout', () => {
                    stars.forEach(star => star.classList.remove('text-yellow-400'));
                });
            }
        });
    </script>

    <script src="https://kit.fontawesome.com/a2e0e6a89b.js" crossorigin="anonymous"></script>
@endsection
