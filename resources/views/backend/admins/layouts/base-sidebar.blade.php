<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">Mr Care</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">MC</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ Route::is('admins.dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.dashboard') }}"><i
                        class="fas fa-home"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="{{ Route::is('admins.home-slider*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.home-slider.index') }}"><i
                        class="fas fa-sliders-h"></i>
                    <span>Home Slider</span></a>
            </li>
            <li class="{{ Route::is('admins.department*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.department.index') }}"><i
                        class="fas fa-hospital"></i>
                    <span>Manage Department</span></a>
            </li>
            <li class="{{ Route::is('admins.doctors*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.doctors.index') }}"><i
                        class="fas fa-user-md"></i>
                    <span>Manage Doctors</span></a>
            </li>
            <li class="{{ Route::is('admins.hospitals*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.hospitals.index') }}"><i
                        class="fas fa-user-md"></i>
                    <span>Manage Hospitals</span></a>
            </li>
            <li class="{{ Route::is('admins.doctor-appointments*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.doctor-appointments.index') }}"><i
                        class="fas fa-user-md"></i>
                    <span>Doctor Appointments</span></a>
            </li>
            <li class="{{ Route::is('admins.hospital-appointments*') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admins.hospital-appointments.index') }}"><i
                        class="fas fa-user-md"></i>
                    <span>Hospital Appointments</span></a>
            </li>

            {{-- <li class="nav-item dropdown ">
                <a href="#" class="nav-link has-dropdown"><i
                        class="fas fa-shopping-cart"></i><span>section</span></a>

                <ul class="dropdown-menu">
                    <li>
                        <a class="nav-link" href="#">Section 1</a>
                    </li>

                </ul>
            </li> --}}
            </li>

        </ul>

    </aside>
</div>
