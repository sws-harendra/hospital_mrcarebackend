<header class="bg-white fixed top-0 z-50 w-full shadow">
    <nav class="flex items-center justify-between md:px-8 px-4 py-3 relative">

        <div class="w-16 md:w-20 flex items-center flex-shrink-0">
            <a href="/">
                <img class="w-full"
                    src="{{ asset('frontend/logo.jpg') }}"
                    alt="Mr Care Logo">
            </a>
        </div>

        <div class="flex flex-grow justify-center mx-3 md:mx-6">
            {{-- Desktop Search Form --}}
            <form action="{{ route('unified.search') ?? '/search-results' }}" method="GET" id="desktop-search-form" class="w-full max-w-lg">
                <div class="flex items-center border border-fuchsia-300 rounded-lg shadow-sm w-full bg-gray-50 focus-within:ring-2 focus-within:ring-fuchsia-500 transition-shadow duration-300">
                    
                    <i class="fa-solid fa-magnifying-glass text-gray-500 ml-3 mr-2"></i>
                    
                    <input class="p-2 outline-none w-full bg-transparent text-gray-700 placeholder-gray-500 text-sm md:text-base" 
                           placeholder="Search Doctors, Hospitals..."
                           type="search" 
                           name="query"
                           required>

                    <button type="submit" class="bg-fuchsia-600 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-fuchsia-700 transition h-full flex items-center text-sm">
                        <i class="fa-solid fa-magnifying-glass md:hidden"></i> 
                        <span class="hidden ml-2 md:inline">Search</span>
                    </button>
                </div>
                {{-- NEW: Hidden fields for Coordinates (Desktop) --}}
                <input type="hidden" name="lat" id="desktop-lat">
                <input type="hidden" name="lon" id="desktop-lon">
            </form>
        </div>

        <ul class="md:flex hidden gap-6 items-center font-semibold flex-shrink-0">
            <li class="hover:text-fuchsia-700 text-fuchsia-700"><a href="/">Home</a></li>
            <li class="hover:text-fuchsia-700 "><a href="https://mrcarehealth.com/about-us">About Us</a></li>
            <li class="hover:text-fuchsia-700 "><a href="https://mrcarehealth.com/blogs">Blogs</a></li>
            <li class="hover:text-fuchsia-700 "><a href="https://mrcarehealth.com/contact-us">Contact Us</a></li>
            <li class="border-2 border-fuchsia-700 rounded-full px-3 py-1 text-md hover:text-fuchsia-700">
                <a href="https://mrcarehealth.com/login" class="hover:text-fuchsia-700">
                    <i class="fa-solid fa-user-tie mr-1"></i>Login
                </a>
            </li>
        </ul>

        <div class="md:hidden flex items-center space-x-3 flex-shrink-0">
            <button id="menu-toggle" class="text-fuchsia-700 focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

    </nav>
    
    <ul id="mobile-menu" class="absolute left-0 w-full bg-white shadow-lg flex-col gap-4 font-semibold p-4 hidden z-40">
        
        {{-- Mobile Search Form (Hidden inputs are managed via JS) --}}
        <li class="mb-4">
            <form action="{{ route('unified.search') ?? '/search-results' }}" method="GET" id="mobile-search-form">
                <div class="flex items-center border border-fuchsia-300 rounded-lg w-full shadow-sm">
                    <input class="p-2 outline-none w-full bg-transparent text-gray-700 placeholder-gray-500" 
                           placeholder="Search Doctors/Hospitals..."
                           type="search" 
                           name="query"
                           required>
                    <button type="submit" class="bg-fuchsia-600 text-white px-3 py-2 rounded-r-lg hover:bg-fuchsia-700 transition h-full flex items-center">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                {{-- NEW: Hidden fields for Coordinates (Mobile) --}}
                <input type="hidden" name="lat" id="mobile-lat">
                <input type="hidden" name="lon" id="mobile-lon">
            </form>
        </li>
        
        {{-- Navigation Links --}}
        <li class="border-b border-gray-200">
            <a href="/" class="block py-2 text-fuchsia-700">Home</a>
        </li>
        <li class="border-b border-gray-200">
            <a href="https://mrcarehealth.com/about-us" class="block py-2 hover:text-fuchsia-700 ">About Us</a>
        </li>
        <li class="border-b border-gray-200">
            <a href="https://mrcarehealth.com/blogs" class="block py-2 hover:text-fuchsia-700 ">Blogs</a>
        </li>
        <li>
            <a href="https://mrcarehealth.com/contact-us" class="block py-2 hover:text-fuchsia-700  ">Contact Us</a>
        </li>
        <li class="mt-4">
            <a href="https://mrcarehealth.com/login" class="block text-center border-2 border-fuchsia-700 rounded-full px-3 py-1 text-fuchsia-700 hover:bg-fuchsia-50">
                <i class="fa-solid fa-user-tie mr-1"></i>Login
            </a>
        </li>
    </ul>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      

        // Geolocation setup
        function updateCoordinates(latitude, longitude) {
            // Update desktop form fields
            document.getElementById('desktop-lat').value = latitude;
            document.getElementById('desktop-lon').value = longitude;
            
            // Update mobile form fields
            document.getElementById('mobile-lat').value = latitude;
            document.getElementById('mobile-lon').value = longitude;
            
            console.log(`Coordinates set: ${latitude}, ${longitude}`);
        }

        function getLocation() {
            if (navigator.geolocation) {
                // Request current position
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Success callback
                        updateCoordinates(position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        // Error callback (User denied or location unavailable)
                        console.warn(`Geolocation Error (${error.code}): ${error.message}`);
                        // No need for explicit fallback coordinates if we rely on Geolocation
                    },
                    {
                        enableHighAccuracy: false,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        }

        // Run geolocation fetch as soon as the page loads
        getLocation();

        
    });
</script>