<!DOCTYPE html>
<html lang="en">

<head>

    @include('backend.admins.layouts.base-meta')
    @stack('title')
    @include('backend.admins.layouts.base-styles')

</head>

<body>

    <div id="app">

        <!--**********************************
        Main wrapper start
    ***********************************-->
        <div class="main-wrapper">
            {{-- <!--**********************************
        Nav header start
        ***********************************-->
        @include('backend.admins.layouts.base-header-brand')
        <!--**********************************
            Nav header end --}}
            <!--**********************************
            Header start
        ***********************************-->
            @include('backend.admins.layouts.base-header')
            <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

            <!--**********************************
                Sidebar start
                ***********************************-->
            @include('backend.admins.layouts.base-sidebar')
            <!--**********************************
    Sidebar end
         ***********************************-->






            <!--**********************************
            Content body start
        ***********************************-->
            @yield('page-content')
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->
            @include('backend.admins.layouts.base-footer')
            <!--**********************************
            Footer end
        ***********************************-->


            <!--**********************************
        Main wrapper end
    ***********************************-->

            <!--**********************************
        Scripts
    ***********************************-->
            @include('backend.admins.layouts.base-scripts')

        </div>
    </div>
</body>

</html>
