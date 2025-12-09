@extends('frontend.layouts.base')

@push('title')
<title>Mr Care | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')

<div class="relative w-full h-[70vh] lg:h-[80vh] overflow-hidden shadow-2xl mt-16">
    <div id="heroSlider" class="flex transition-transform duration-700 ease-in-out h-full">
        @foreach($sliders as $slider)
        <div class="w-full flex-shrink-0 relative h-full">
            <img 
                src="{{ asset($slider->image) }}" 
                alt="{{ $slider->title }}" 
                class="w-full h-full object-full object-center"
                
            >
            <div class="absolute inset-0 bg-gray-900/60 flex flex-col items-start justify-center text-white px-6 md:px-20">
                <div class="max-w-3xl text-left">
                    @if(!empty($slider->title))
                    <h2 class="text-5xl md:text-6xl font-extrabold mb-4 leading-tight tracking-tight drop-shadow-lg">{{ $slider->title }}</h2>
                    @endif
                    @if(!empty($slider->subtitle))
                    <p class="text-xl max-w-2xl mb-8 opacity-95">{{ $slider->subtitle }}</p>
                    @endif
                    @if(!empty($slider->link))
                    <a href="{{ $slider->link }}" 
                       class="inline-block bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-semibold text-lg px-8 py-3 rounded-full shadow-xl transition duration-300 transform hover:scale-105">
                        Find Care Now →
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2">
        @foreach($sliders as $index => $slider)
        <button class="w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all dot border-2 border-white focus:outline-none {{ $index === 0 ? 'bg-fuchsia-600 w-4 h-4' : '' }}" data-slide="{{ $index }}"></button>
        @endforeach
    </div>
</div>

{{-- ========================================================================= --}}
{{--                                   DOCTORS SECTION                               --}}
{{-- ========================================================================= --}}
<section class="bg-gray-100 py-16 w-full overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-4 tracking-tight">
            Our <span class="text-fuchsia-600">Expert Doctors</span>
        </h2>
        <p class="text-lg text-center text-gray-600 mb-12">Meet the top-rated specialists in our network.</p>

        <div class="relative w-full">
            {{-- Horizontal scrollable container for Doctors --}}
            {{-- Removed animate-scroll-slow. Use manual scrolling or JS. --}}
            <div id="doctorSlider" class="flex gap-6 pb-4 overflow-x-auto no-scrollbar snap-x snap-mandatory">
                
                @foreach($doctors as $doctor)
                @php
                    $rating = $doctor->avg_rating ?? 0;
                    $hospitalDisplay = $doctor->hospitals->pluck('name')->join(', ') ?? 'N/A'; // Assuming you have this data/accessor
                    $doctorAddress = trim(($doctor->address ?? '') . ' ' . ($doctor->city ?? '') . ' ' . ($doctor->state ?? ''));
                    $doctorMapUrl = $doctorAddress ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($doctorAddress) : '#';
                @endphp
                {{-- CARD WITH ENHANCED INFO AND BUTTONS --}}
                <div class="flex-none w-[280px] sm:w-[300px] bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 text-center p-6 border-t-4 border-fuchsia-600 group snap-start">
                    
                    <a href="{{ route('doctor.show', ['doctor_id' => $doctor->id]) ?? '#' }}" class="block">
                        <img 
                            src="{{ asset($doctor->profile_image ?? 'img/default-doctor.png') }}" 
                            alt="{{ $doctor->name }}" 
                            class="w-24 h-24 object-cover rounded-full border-4 border-gray-200 mx-auto mb-3 shadow-md group-hover:border-fuchsia-700 transition"
                            
                        >
                        <h3 class="text-xl font-bold text-gray-900 truncate">{{ $doctor->name }}</h3>
                        <p class="text-sm text-fuchsia-700 font-semibold mt-1">{{ $doctor->specialization ?? 'Specialist' }}</p>
                    </a>

                    <div class="flex items-center justify-center mt-2 text-sm">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($rating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                        @endfor
                        <span class="ml-2 text-gray-600 font-semibold">({{ number_format($rating, 1) }}/5)</span>
                    </div>

                    <div class="text-sm text-gray-700 mt-3 space-y-1 text-center">
                        {{-- FEES --}}
                        @if(!empty($doctor->consultation_fee))
                        <p class="font-bold text-green-600 text-base">
                            Fee: ₹{{ number_format($doctor->consultation_fee, 0) }}
                        </p>
                        @endif

                        {{-- HOSPITALS --}}
                        <p class="text-gray-600 truncate px-2" title="{{ $hospitalDisplay }}">
                            <i class="fas fa-hospital mr-1 text-fuchsia-500"></i>
                            {{ $hospitalDisplay }}
                        </p>
                        
                        {{-- EXPERIENCE & LOCATION --}}
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-briefcase text-gray-400 mr-1"></i> {{ $doctor->experience ?? 'N/A' }} yrs Exp.
                        </p>
                        <a href="{{ $doctorMapUrl }}" target="_blank" class="text-xs text-gray-500 hover:text-fuchsia-700 hover:underline transition block">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> {{ $doctor->city ?? 'Location N/A' }}
                        </a>
                    </div>
                    
                    <div class="mt-4 space-y-2">
                        {{-- <a href="{{ route('doctor.show', ['doctor_id' => $doctor->id]) ?? '#' }}" 
                           class="w-full block bg-fuchsia-600/10 text-fuchsia-700 text-sm font-semibold px-4 py-2 rounded-full hover:bg-fuchsia-600 hover:text-white transition duration-300">
                            View Profile
                        </a> --}}
                        {{-- Requires JS function: openDoctorBookingModal(doctorId) --}}
                        {{-- <button onclick="openDoctorBookingModal('{{ $doctor->id }}')" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-4 py-2 rounded-full transition duration-300 shadow-md">
                            <i class="fas fa-calendar-check mr-1"></i> Book Appointment
                        </button> --}}
                    </div>

                </div>
                @endforeach
            </div>
        </div>
        
        {{-- <div class="text-center mt-12">
            <a href="{{ route('doctors.index') ?? '#' }}" 
               class="inline-block border-2 border-fuchsia-600 text-fuchsia-600 hover:bg-fuchsia-600 hover:text-white font-semibold px-8 py-3 rounded-full transition-all duration-300 text-lg shadow-md">
                View All Specialists
            </a>
        </div> --}}
    </div>
</section>

{{-- ========================================================================= --}}
{{--                                   HOSPITALS SECTION                             --}}
{{-- ========================================================================= --}}
<section class="py-16 bg-white w-full">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-4 tracking-tight">
            Our <span class="text-fuchsia-600">Partner Hospitals</span>
        </h2>
        <p class="text-lg text-center text-gray-600 mb-12">Collaborating with the best healthcare institutions.</p>

        <div class="space-y-8">
            @foreach($hospitals as $hospital)
            @php 
                $rating = $hospital->avg_rating ?? 0;
                $departmentCount = $hospital->departments->count() ?? 0; // Assuming departments relationship exists
                $hospitalAddress = trim(($hospital->address ?? '') . ' ' . ($hospital->city ?? '') . ' ' . ($hospital->state ?? ''));
                $mapUrl = $hospitalAddress ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($hospitalAddress) : '#';
            @endphp
            
            <div class="bg-gray-50 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border-l-4 border-r-4 border-fuchsia-600 p-4  flex flex-col sm:flex-row sm:items-start space-y-4 sm:space-y-0 sm:space-x-8 w-full group">
                
                <div class="flex-shrink-0 w-full sm:w-auto flex justify-center">
                    <img 
                        src="{{ asset($hospital->main_image ?? 'img/hospital-default.jpg') }}" 
                        alt="{{ $hospital->name }} Logo" 
                        class="w-40 h-40 sm:w-40 sm:h-40 object-cover rounded-xl  border-white shadow-md bg-white group-hover:border-fuchsia-700 transition"
                        
                    >
                </div>

                <div class="flex-grow text-center sm:text-left">
                    @if(!empty($hospital->name))
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                        <a href="{{ route('hospital.show', ['hospital_id' => $hospital->hospital_id]) ?? '#' }}" class="hover:text-fuchsia-700 transition">
                            {{ $hospital->name }}
                        </a>
                    </h3>
                    @endif

                    <div class="flex items-center justify-center sm:justify-start flex-wrap gap-x-4 gap-y-2 mb-3">
                        <span class="text-yellow-500 text-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($rating) ? 'text-yellow-500' : 'text-gray-300' }} text-xs"></i>
                            @endfor
                            <span class="ml-1 text-gray-600 text-sm font-semibold">({{ number_format($rating, 1) }}/5)</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $hospital->reviews->count() ?? 0 }} Reviews)</span>
                        </span>
                        
                        @if(!empty($hospital->is_verified))
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full"><i class="fas fa-check-circle mr-1"></i> Verified</span>
                        @endif
                        @if(!empty($hospital->hospital_type))
                        <span class="bg-fuchsia-100 text-fuchsia-800 text-xs font-medium px-3 py-1 rounded-full">{{ $hospital->hospital_type }}</span>
                        @endif
                        @if($departmentCount > 0)
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                            <i class="fas fa-bars mr-1"></i> {{ $departmentCount }} Depts
                        </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-2 text-sm text-gray-700">
                        @if(!empty($hospital->number_of_doctors))
                        <p><strong><i class="fas fa-user-md text-gray-500 mr-1"></i> Doctors:</strong> {{ $hospital->number_of_doctors }}</p>
                        @endif
                        @if(!empty($hospital->number_of_beds))
                        <p><strong><i class="fas fa-bed text-gray-500 mr-1"></i> Beds:</strong> {{ $hospital->number_of_beds }}</p>
                        @endif
                        @if(!empty($hospitalAddress))
                        <p class="truncate col-span-2"><strong><i class="fas fa-map-marker-alt text-gray-500 mr-1"></i> Location:</strong> {{ $hospital->city ?? '' }}, {{ $hospital->state ?? '' }}</p>
                        @endif
                    </div>

                    <div class="mt-5 flex flex-wrap items-center justify-center sm:justify-start gap-4">
                        
                        {{-- View Details Button --}}
                        <a href="{{ route('hospital.show', ['hospital_id' => $hospital->hospital_id]) ?? '#' }}" 
                           class="inline-block border border-fuchsia-700 text-fuchsia-700 hover:bg-fuchsia-700 hover:text-white font-medium text-sm px-5 py-2 rounded-full transition duration-300">
                            <i class="fas fa-info-circle mr-2"></i> View Details
                        </a>
                        
                        {{-- Book Appointment Button --}}
                        {{-- Requires JS function: openHospitalBookingModal(hospitalId) --}}
                        {{-- <button onclick="openHospitalBookingModal('{{ $hospital->hospital_id }}')"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium text-sm px-5 py-2 rounded-full transition duration-300 shadow-md">
                            <i class="fas fa-calendar-check mr-2"></i> Book Appointment
                        </button> --}}
                        
                        {{-- View Map Button --}}
                        <a href="{{ $mapUrl }}" target="_blank" 
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-5 py-2 rounded-full transition duration-300">
                            <i class="fas fa-route mr-2"></i> View Map
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- <div class="text-center mt-12">
            <a href="{{ route('hospitals.index') ?? '#' }}" 
               class="inline-block border-2 border-fuchsia-600 text-fuchsia-600 hover:bg-fuchsia-600 hover:text-white font-semibold px-8 py-3 rounded-full transition-all duration-300 text-lg shadow-md">
                View All Hospitals
            </a>
        </div> --}}
    </div>
</section>


<style>
    /*  Hiding scrollbar for aesthetic purposes */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>


<script>
document.addEventListener('DOMContentLoaded', () => {
    /* HERO SLIDER */
    const heroSlider = document.getElementById('heroSlider');
    const slides = heroSlider.children;
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;

    function goToSlide(index) {
        if (slides.length === 0) return; // Guard clause
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentSlide = index;
        heroSlider.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-fuchsia-600', i === currentSlide);
            dot.classList.toggle('w-4', i === currentSlide);
            dot.classList.toggle('h-4', i === currentSlide);
            dot.classList.toggle('bg-white/50', i !== currentSlide);
            dot.classList.toggle('w-3', i !== currentSlide);
            dot.classList.toggle('h-3', i !== currentSlide);
        });
    }

    dots.forEach((dot, i) => dot.addEventListener('click', () => goToSlide(i)));

    goToSlide(0); 

    let heroInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
    heroSlider.addEventListener('mouseenter', () => clearInterval(heroInterval));
    heroSlider.addEventListener('mouseleave', () => heroInterval = setInterval(() => goToSlide(currentSlide + 1), 5000));

    /* DOCTOR SLIDER (Horizontal Scroll Only) */
    const docSlider = document.getElementById('doctorSlider');
    // Using simple overflow-x-auto/snap for the doctor slider for better UX on mobile.
    // Manual JS/CSS scrolling is disabled as per the removal of 'animate-scroll-slow'.
    if (docSlider) {
        docSlider.scrollLeft = 0; // Ensure it starts at the beginning
        // Removed the complex auto-scrolling logic for better user control/accessibility.
    }
});

// --- DUMMY MODAL FUNCTIONS ---
// You need to define these functions or integrate them with your existing modal logic
function openDoctorBookingModal(doctorId) {
    console.log('Open Doctor Booking Modal for ID:', doctorId);
    // TODO: Implement modal display logic here
    alert(`Booking modal opened for Doctor ID: ${doctorId}. Please integrate your modal display code.`);
}

function openHospitalBookingModal(hospitalId) {
    console.log('Open Hospital Booking Modal for ID:', hospitalId);
    // TODO: Implement modal display logic here
    alert(`Booking modal opened for Hospital ID: ${hospitalId}. Please integrate your modal display code.`);
}
</script>

<script src="https://kit.fontawesome.com/a2e0e6a89b.js" crossorigin="anonymous"></script>

@endsection