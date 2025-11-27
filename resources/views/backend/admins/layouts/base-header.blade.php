  <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown dropdown-list-toggle"><a target="_blank" href="#" class="nav-link nav-link-lg"><i class="fas fa-home"></i>Admin Visit Website</i></a>

          </li>

        
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
             
              <img alt="image" src="{{ asset('backend/img/avatar/avatar-2.png') }}" class="rounded-circle mr-1">
           
           
            <div class="d-sm-none d-lg-inline-block">Admin</div></a>
            <div class="dropdown-menu dropdown-menu-right">

              {{-- <a href="#" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a> --}}
              <div class="dropdown-divider"></div>
              <a href="{{ route('admins.logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault();
              document.getElementById('admin-logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>Logout
              </a>
            {{-- start admin logout form --}}
            <form id="admin-logout-form" action="{{ route('admins.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            {{-- end admin logout form --}}
       


            </div>
          </li>
        </ul>
      </nav>