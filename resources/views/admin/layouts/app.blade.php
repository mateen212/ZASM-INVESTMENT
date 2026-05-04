@extends('admin.layouts.master')
@section('content')

@if(auth('admin')->user()->hasRole('Super Admin'))
    @php
        $sidenav = file_get_contents(resource_path('views/admin/partials/sidenav.json'));
        $sideBarLinks = json_decode($sidenav);
        // Add debugging to check if the JSON is being loaded correctly
        if (!$sideBarLinks) {
            echo "<div class='alert alert-danger'>Error loading sidebar JSON</div>";
        }
        $filteredSideBarLinks = filterSidebarByPermission($sideBarLinks);
        // Add debugging to check if the filtered sidebar links are empty
        if (empty($filteredSideBarLinks)) {
            echo "<div class='alert alert-danger'>Filtered sidebar links are empty</div>";
            // Fallback to original sidebar links if filtered is empty
            $filteredSideBarLinks = $sideBarLinks;
        }
    @endphp
@endif
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        @if(auth('admin')->user()->hasRole('Super Admin'))
            @include('admin.partials.sidenav', ['sideBarLinks' => $filteredSideBarLinks])
            @include('admin.partials.topnav')
        @endif

        @if(auth('admin')->user()->hasRole('partner'))
            @include('partner.layouts.partials.sidenav')
            @include('partner.layouts.partials.topnav')
        @endif

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">

                    @stack('topBar')
                    @include('admin.partials.breadcrumb')

                    @yield('panel')

                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>
    </div>
@endsection
