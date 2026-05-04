@extends($activeTemplate . 'layouts.app')
@section('main-content')
    @include($activeTemplate . 'partials.header')
    <div class="dashboard position-relative">
        <div class="mx-3">
            <div class="dashboard__wrapper">
                @if(auth()->check())
                    @include($activeTemplate . 'partials.sidenav')
                @endif
                <div class="dashboard-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @include($activeTemplate . 'partials.footer')
@endsection

@push('style')
    <style>
        /* Hide breadcrumb completely */
        section.breadcrumb {
            display: none !important;
        }
        
        /* Match live reference header size */
        .header {
            padding: 0.5rem 0;
            min-height: auto;
        }
        
        /* Match live reference spacing */
        .dashboard {
            padding: 0;
        }
        
        .dashboard-body {
            padding-top: 1rem;
        }
        
        /* Adjust container spacing */
        .container {
            padding-top: 0.5rem;
        }
        
        /* Clean up extra margins */
        .mb-5 {
            margin-bottom: 2rem !important;
        }
        
        .mt-5 {
            margin-top: 2rem !important;
        }
    </style>
@endpush

@push('script')
    <script>
        $('.showFilterBtn').on('click', function() {
            $('.responsive-filter-card').slideToggle();
        });
    </script>
@endpush
