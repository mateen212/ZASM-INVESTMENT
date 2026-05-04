@extends($activeTemplate . 'layouts.app')

@section('main-content')
    @yield('content')
@endsection

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/slick.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpush
