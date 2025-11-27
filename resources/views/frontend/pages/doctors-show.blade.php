@extends('frontend.layouts.base')

@push('title')
    <title>{{ $doctor->name }} | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
    <section class="bg-gray-50 min-h-screen py-8 lg:py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 flex flex-col lg:flex-row gap-8">

            <div class="flex-1 space-y-6">

                {{-- 1. DOCTOR HEADER & QUICK ACTIONS --}}
                <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 border-t-8 border-fuchsia-600">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                        <img src="{{ asset($doctor->profile_image ?? 'img/default-doctor.png') }}" alt="{{ $doctor->name }}"
                            class="w-32 h-32 object-cover rounded-full border-4 border-gray-200 shadow-xl flex-shrink-0 mx-auto sm:mx-0 p-1 bg-white">

                        <div class="flex-1 text-center sm:text-left">
                            <h1 class="text-4xl font-extrabold text-gray-900 leading-snug">{{ $doctor->name }}</h1>
                            @if ($doctor->specialization)
                                <p class="text-xl text-fuchsia-700 font-bold mt-1">{{ $doctor->specialization }}</p>
                            @endif

                            <div class="flex items-center justify-center sm:justify-start mt-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star {{ $i <= round($averageRating) ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600 font-medium">({{ number_format($averageRating, 1) }}
                                    / 5 from {{ $doctor->reviews->count() }} Reviews)</span>
                            </div>

                            {{-- Location & Fee --}}
                            <div class="mt-3 text-sm text-gray-700 flex flex-col items-center sm:items-start">
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
                                        class="flex items-center hover:text-fuchsia-700 hover:underline transition cursor-pointer">
                                        <i class="fas fa-map-marker-alt text-fuchsia-600 mr-2"></i>
                                        {{ $doctorAddress ?: 'Location not specified' }}
                                    </a>
                                @endif
                                @if ($doctor->consultation_fee)
                                    <p class="mt-1">
                                        Consultation Fee: <strong
                                            class="text-green-600">₹{{ number_format($doctor->consultation_fee, 2) }}</strong>
                                    </p>
                                @endif
                            </div>

                            {{-- INLINE ACTIONS (Book, Map, Share, TIMINGS) --}}
                            <div class="flex justify-center sm:justify-start gap-3 mt-4 flex-wrap">
                                <button onclick="openDoctorBookingModal('{{ $doctor->doctor_id }}')"
                                    class="bg-fuchsia-700 hover:bg-fuchsia-800 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                    <i class="fas fa-calendar-check mr-2"></i> Book Now
                                </button>

                                @if (!empty($doctor->address))
                                    <a href="{{ $doctorPrimaryMapUrl }}" target="_blank"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                        <i class="fas fa-route mr-2"></i> View Map
                                    </a>
                                @endif

                                {{-- TIMINGS BUTTON / DISPLAY (Always opens modal) --}}
                                @if ($doctor->businessHours->isNotEmpty())
                                    @php
                                        $today = strtolower(now()->englishDayOfWeek);
                                        $todayHour = $doctor->businessHours->firstWhere('day', $today);
                                        $displayTime = 'Check Timings';

                                        if ($todayHour && !$todayHour->is_closed) {
                                            $displayTime =
                                                \Carbon\Carbon::parse($todayHour->open_time)->format('h:i A') .
                                                ' - ' .
                                                \Carbon\Carbon::parse($todayHour->close_time)->format('h:i A');
                                        } elseif ($todayHour && $todayHour->is_closed) {
                                            $displayTime = 'Closed Today';
                                        }
                                    @endphp
                                    <button id="timingsButtonHeader" onclick="openTimingsModal()"
                                        class="bg-fuchsia-100 text-fuchsia-800 hover:bg-fuchsia-200 font-semibold px-4 py-2 rounded-lg transition shadow-md text-sm">
                                        <i class="fas fa-clock mr-2"></i> {{ $displayTime }}
                                    </button>
                                @endif

                                <button id="shareButtonHeader"
                                    class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold px-4 py-2 rounded-lg transition shadow-md">
                                    <i class="fas fa-share-alt mr-2"></i> Share
                                </button>

                            </div>
                            <span id="shareMessageGlobal"
                                class="hidden text-xs font-medium text-green-600 mt-2 w-full"></span>

                        </div>
                    </div>
                </div>

                {{-- 2. QUICK DETAILS & ABOUT (Same as before) --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><i
                            class="fas fa-stethoscope text-fuchsia-600 mr-3"></i> Quick Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-base text-gray-700">
                        @if ($doctor->qualification)
                            <p><strong><i class="fas fa-graduation-cap text-gray-500 w-5"></i> Qualification:</strong>
                                {{ $doctor->qualification }}</p>
                        @endif
                        @if ($doctor->experience)
                            <p><strong><i class="fas fa-briefcase text-gray-500 w-5"></i> Experience:</strong>
                                <span class="text-gray-900 font-semibold">{{ $doctor->experience }} years</span>
                            </p>
                        @endif
                        @if ($doctor->doctor_registration_number)
                            <p><strong><i class="fas fa-id-badge text-gray-500 w-5"></i> Reg. No.:</strong>
                                {{ $doctor->doctor_registration_number }}</p>
                        @endif
                        @if ($doctor->gender)
                            <p><strong><i class="fas fa-venus-mars text-gray-500 w-5"></i> Gender:</strong>
                                {{ ucfirst($doctor->gender) }}</p>
                        @endif
                        @if ($doctor->age)
                            <p><strong><i class="fas fa-birthday-cake text-gray-500 w-5"></i> Age:</strong>
                                {{ $doctor->age }} years</p>
                        @endif
                    </div>
                </div>

                @if ($doctor->education || $doctor->bio)
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-info-circle text-fuchsia-600 mr-3"></i> Detailed Profile</h2>
                        @if ($doctor->education)
                            <h3 class="text-lg font-bold text-gray-800 mt-4 mb-2">Educational Background</h3>
                            <p class="text-gray-700 leading-relaxed italic border-l-4 border-gray-200 pl-3">
                                {{ $doctor->education }}</p>
                        @endif
                        @if ($doctor->bio)
                            <h3 class="text-lg font-bold text-gray-800 mt-6 mb-2">Biography</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $doctor->bio }}</p>
                        @endif
                    </div>
                @endif

                {{-- 3. SERVICES (Same as before) --}}
                @if (!empty($doctor->decoded_services) && !empty($doctor->decoded_services))
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-procedures text-fuchsia-600 mr-3"></i> Services Offered</h2>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($doctor->decoded_services as $service)
                                <span
                                    class="bg-fuchsia-100 text-fuchsia-800 text-sm px-4 py-2 rounded-full font-medium border border-fuchsia-300 transition-colors hover:bg-fuchsia-200">
                                    {{ $service }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 4. ASSOCIATED HOSPITALS (Same as before) --}}
                @if ($doctor->hospitals->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><i
                                class="fas fa-hospital-alt text-fuchsia-600 mr-3"></i> Associated Hospitals</h2>
                        <div class="space-y-4">
                            @foreach ($doctor->hospitals as $hospital)
                                @php
                                    $hospitalAddress = trim(
                                        ($hospital->address ?? '') .
                                            ' ' .
                                            ($hospital->city ?? '') .
                                            ' ' .
                                            ($hospital->state ?? ''),
                                    );
                                    $mapUrl =
                                        'https://www.google.com/maps/search/?api=1&query=' .
                                        urlencode($hospitalAddress ?: $hospital->name);
                                    $hospitalImage = asset(
                                        $hospital->logo ??
                                            ($hospital->photos->first()->photo_path ?? 'img/hospital-default.jpg'),
                                    );
                                    $hospAvgRating = $hospital->reviews->avg('rating') ?? 0;
                                @endphp

                                <div
                                    class="border border-gray-200 rounded-xl p-4 bg-gray-50 hover:bg-white hover:shadow-lg transition duration-300 group">
                                    <a href="{{ url('/hospitals/' . $hospital->hospital_id) }}" class="block">
                                        <div class="flex items-start gap-4">

                                            <img src="{{ $hospitalImage }}" alt="{{ $hospital->name }} Logo"
                                                class="w-20 h-20 object-cover rounded-lg flex-shrink-0 border-2 border-fuchsia-100 group-hover:border-fuchsia-600 transition">

                                            <div class="flex-1">
                                                <div
                                                    class="font-bold text-lg text-gray-800 group-hover:text-fuchsia-700 transition">
                                                    {{ $hospital->name }}</div>

                                                <div class="flex items-center mt-1 text-sm">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= round($hospAvgRating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                                                    @endfor
                                                    <span
                                                        class="ml-1 text-xs text-gray-600">({{ number_format($hospAvgRating, 1) }}/5)</span>
                                                </div>

                                                @if ($hospitalAddress)
                                                    <a href="{{ $mapUrl }}" target="_blank"
                                                        class="text-sm text-gray-600 mt-1 flex items-start hover:underline"
                                                        onclick="event.stopPropagation();">
                                                        <i class="fas fa-map-marker-alt text-fuchsia-500 mr-2 mt-1"></i>
                                                        <span class="break-words w-full">{{ $hospitalAddress }}</span>
                                                    </a>
                                                @endif

                                                @if (isset($hospital->departments) && $hospital->departments->count())
                                                    <div class="text-xs text-gray-600 mt-2">
                                                        <strong>Dept:</strong>
                                                        <span class="inline-flex flex-wrap gap-1 mt-1">
                                                            @foreach ($hospital->departments->take(2) as $dept)
                                                                <span
                                                                    class="bg-gray-200 px-2 py-0.5 rounded">{{ $dept->name }}</span>
                                                            @endforeach
                                                            @if ($hospital->departments->count() > 2)
                                                                <span
                                                                    class="bg-gray-200 px-2 py-0.5 rounded">+{{ $hospital->departments->count() - 2 }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-shrink-0 self-center">
                                                <i
                                                    class="fas fa-chevron-right text-gray-400 group-hover:text-fuchsia-700 transition"></i>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Book Button specific to this hospital/doctor combo --}}
                                    <div class="mt-3 text-center">
                                        <a href="javascript:void(0)"
                                            onclick="openDoctorBookingModal('{{ $doctor->doctor_id }}', '{{ $hospital->id }}', '{{ $hospital->name }}')"
                                            class="inline-block bg-fuchsia-600 text-white text-sm font-medium py-2 px-6 rounded-lg hover:bg-fuchsia-700 transition duration-150">
                                            Book at this Clinic
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 7. GALLERY & REVIEWS (Same as before) --}}
                @if ($doctor->photos->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                                class="fas fa-images text-fuchsia-600 mr-3"></i> Photo Gallery</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($doctor->photos->take(4) as $photo)
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

                        @if ($doctor->photos->count() > 4)
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">More Photos (Scroll)</h3>
                                <div class="flex overflow-x-auto space-x-4 py-2 no-scrollbar">
                                    @foreach ($doctor->photos->skip(4) as $photo)
                                        <img src="{{ asset($photo->photo_path) }}" alt="Doctor Photo"
                                            class="gallery-image h-36 w-52 flex-shrink-0 rounded-lg object-cover shadow-md border border-gray-200 hover:scale-105 transition duration-300 cursor-pointer"
                                            data-full-src="{{ asset($photo->photo_path) }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div id="reviews" class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-2"><i
                            class="fas fa-comments text-fuchsia-600 mr-3"></i>
                        Patient Reviews ({{ $doctor->reviews->count() }})</h2>

                    <div class="space-y-4 mb-6">
                        @forelse($doctor->reviews as $review)
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

                    <form action="{{ route('doctor.review.store', $doctor->doctor_id) }}" method="POST"
                        enctype="multipart/form-data"
                        class="mt-10 space-y-4 p-6 border border-fuchsia-300 rounded-xl bg-fuchsia-50">
                        @csrf
                        <h3 class="text-xl font-bold text-fuchsia-800 mb-4 border-b border-fuchsia-300 pb-2">Share Your
                            Experience</h3>

                        <div class="flex flex-col">
                            <label class="text-sm font-semibold text-gray-700 mb-2">Your Rating <span
                                    class="text-red-500">*</span></label>
                            <div class="flex space-x-1 rating-stars">
                                @for ($i = 5; $i >= 1; $i--)
                                    <label for="rating-{{ $i }}"
                                        class="cursor-pointer text-gray-400 text-2xl transition-colors hover:text-yellow-500">
                                        <i class="fas fa-star"></i>
                                        <input type="radio" name="rating" id="rating-{{ $i }}"
                                            value="{{ $i }}" class="hidden" required>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="Your Name *" required
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                            <input type="email" name="email" placeholder="Your Email *" required
                                class="w-full border border-gray-400 rounded-lg px-4 py-2.5 focus:ring-fuchsia-500 focus:border-fuchsia-500">
                        </div>

                        <input type="file" name="image" accept="image/*"
                            class="w-full border border-gray-400 bg-white rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-fuchsia-100 file:text-fuchsia-700 hover:file:bg-fuchsia-200 cursor-pointer">

                        <textarea name="comment" rows="4" placeholder="Write your detailed review here... *" required
                            class="w-full border border-gray-400 rounded-lg px-4 py-2 focus:ring-fuchsia-500 focus:border-fuchsia-500"></textarea>

                        <button type="submit"
                            class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white px-6 py-3 rounded-lg font-bold transition duration-300 shadow-lg transform hover:scale-[1.01]">
                            Submit Review
                        </button>
                    </form>
                </div>


            </div>

            <div class="w-full lg:w-[350px] space-y-6 flex-shrink-0">

                <div class=" bg-white rounded-xl shadow-xl p-6 border-t-4 border-fuchsia-600">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Book & Contact</h3>

                    <button onclick="openDoctorBookingModal('{{ $doctor->doctor_id }}')"
                        class="w-full bg-fuchsia-700 hover:bg-fuchsia-800 text-white py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01]">
                        <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                    </button>

                    @if ($doctor->mobile_number)
                        <button onclick="openDoctorBookingModal('{{ $doctor->doctor_id }}')"
                            class="w-full bg-green-600 hover:bg-green-800 mt-3 text-white py-3 rounded-lg font-bold text-lg transition duration-300 shadow-lg transform hover:scale-[1.01]">
                            <i class="fas fa-phone mr-2"></i> Show Number
                        </button>
                    @endif

                    {{-- @if ($doctor->whatsapp_number)
                        <a href="https://wa.me/{{ $doctor->whatsapp_number }}" target="_blank"
                            class="w-full block text-center mt-3 border border-fuchsia-700 text-fuchsia-700 hover:bg-fuchsia-700 hover:text-white py-2 rounded-lg font-semibold transition duration-300">
                            <i class="fab fa-whatsapp mr-2"></i> Message on WhatsApp
                        </a>
                    @endif --}}
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><i
                            class="fas fa-share-alt text-fuchsia-600 mr-3"></i> Connect Online</h2>

                    @if ($doctor->website)
                        <a href="{{ $doctor->website }}" target="_blank"
                            class="flex items-center gap-3 p-3 my-3 border rounded-lg hover:bg-fuchsia-50 text-fuchsia-700 transition font-medium">
                            <i class="fas fa-globe text-lg"></i>
                            <span class="truncate">Visit Official Website</span>
                        </a>
                    @endif

                    <div class="flex flex-wrap gap-5 mt-4 justify-center">
                        @if ($doctor->facebook)
                            <a href="{{ $doctor->facebook }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Facebook"><i
                                    class="fab fa-facebook-square text-3xl"></i></a>
                        @endif
                        @if ($doctor->twitter)
                            <a href="{{ $doctor->twitter }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Twitter"><i
                                    class="fab fa-twitter-square text-3xl"></i></a>
                        @endif
                        @if ($doctor->linkedin)
                            <a href="{{ $doctor->linkedin }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="LinkedIn"><i
                                    class="fab fa-linkedin text-3xl"></i></a>
                        @endif
                        @if ($doctor->instagram)
                            <a href="{{ $doctor->instagram }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="Instagram"><i
                                    class="fab fa-instagram-square text-3xl"></i></a>
                        @endif
                        @if ($doctor->youtube)
                            <a href="{{ $doctor->youtube }}" target="_blank"
                                class="text-gray-600 hover:text-fuchsia-700 transition" aria-label="YouTube"><i
                                    class="fab fa-youtube-square text-3xl"></i></a>
                        @endif
                    </div>

                    {{-- 9. SIMILAR DOCTORS SECTION (Same as before) --}}
                    @if (isset($similarDoctors) && $similarDoctors->isNotEmpty())
                        <div class="mt-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                                <i class="fas fa-users text-fuchsia-600 mr-3"></i> Similar Doctors
                            </h2>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($similarDoctors as $sim)
                                    @php
                                        $simAvg = round($sim->avg_rating ?? ($sim->reviews->avg('rating') ?? 0), 1);
                                    @endphp
                                    <a href="{{ url('/doctor/' . $sim->id) }}"
                                        class="block border border-gray-200 rounded-xl p-4 bg-gray-50 hover:shadow-xl hover:bg-white transition duration-300 group">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ asset($sim->profile_image ?? 'img/default-doctor.png') }}"
                                                class="w-16 h-16 object-cover rounded-full flex-shrink-0 border-2 border-fuchsia-200 group-hover:border-fuchsia-600 transition-colors">

                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-800 truncate">{{ $sim->name }}</div>
                                                @if ($sim->specialization)
                                                    <div class="text-sm text-fuchsia-700 font-medium truncate">
                                                        {{ $sim->specialization }}</div>
                                                @endif

                                                @if (isset($sim->experience))
                                                    <div class="text-xs text-gray-500 mt-1">Exp: {{ $sim->experience }}
                                                        yrs
                                                    </div>
                                                @endif

                                                <div class="flex items-center mt-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= round($simAvg) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                                                    @endfor
                                                    <span
                                                        class="ml-1 text-xs text-gray-600">({{ number_format($simAvg, 1) }})</span>
                                                </div>

                                                @if (!empty($sim->city) || !empty($sim->state))
                                                    <div class="text-xs text-gray-500 mt-1 truncate">
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        {{ trim(($sim->city ?? '') . ' ' . ($sim->state ?? '')) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>

    {{-- BUSINESS HOURS MODAL (For Timings Display) --}}
    <div id="timingsModal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center hidden"
        onclick="closeTimingsModal()">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm m-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-900">Doctor Timings</h3>
                <button onclick="closeTimingsModal()"
                    class="text-gray-500 hover:text-gray-900 text-3xl leading-none">×</button>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($doctor->businessHours as $hour)
                    <div class="flex justify-between py-2 text-base text-gray-700">
                        <span class="capitalize font-medium">{{ $hour->day }}</span>
                        <span class="text-right">
                            @if ($hour->is_closed)
                                <span class="text-red-600 font-semibold">Closed</span>
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
                @if ($doctor->hospitals->isNotEmpty())
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
                @endif

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
            const doctorName = "{{ $doctor->name }}";
            const shareMessageEl = document.getElementById('shareMessageGlobal');

            shareMessageEl.classList.add('hidden');

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: `Check out Dr. ${doctorName}'s profile on {{ env('APP_NAME') }}`,
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
            const sidebarBookButton = document.querySelector('.sticky button');

            // Attach share logic
            if (headerShareButton) {
                headerShareButton.addEventListener('click', handleShareClick);
            }

            // Attach main sticky book button
            if (sidebarBookButton) {
                sidebarBookButton.onclick = function() {
                    openDoctorBookingModal('{{ $doctor->doctor_id }}');
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

            // --- Department Doctor Toggle Logic (Same as previous fix) ---
            const deptButtons = document.querySelectorAll('.dept-btn');
            const departmentDoctors = @json($departmentDoctors);
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
                        const profileLink = `/doctors/${doc.doctor_id}`;

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
