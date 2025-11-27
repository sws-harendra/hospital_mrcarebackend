<!DOCTYPE html>
<html lang="en">

<head>

    @include('frontend.layouts.base-meta')
    @stack('title')
    @include('frontend.layouts.base-styles')

</head>

<body >

    {{-- <div id="app"> --}}

        <!--**********************************
            Header start
        ***********************************-->
            @include('frontend.layouts.base-header')
            <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

            <!--**********************************
                Sidebar start
                ***********************************-->
            {{-- @include('frontend.layouts.base-sidebar') --}}
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
            @include('frontend.layouts.base-footer')
            <!--**********************************
            Footer end
        ***********************************-->


            <!--**********************************
        Main wrapper end
    ***********************************-->

            <!--**********************************
        Scripts
    ***********************************-->
            @include('frontend.layouts.base-scripts')

        </div>
    {{-- </div> --}}
</body>

</html>
