@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="property-page  bg-pattern" style='padding-top:50px;'>
        <div class="complete-data">
            <div class="property-page-inner">
                <aside id="property-page-sidebar" class="property-page-sidebar">
                    <button class="close-btn" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                    <form action="{{ url()->current() }}" class="filter-form">
                        <div class="filter-form__block">
                            <h6 class="title">@lang('Search Property')</h6>
                            <div class="form-group">
                                <input class="form--control" type="text" name="search" value="{{ request()->search }}"
                                    placeholder="@lang('Type and press Filter Now')">
                            </div>
                        </div>


                        <button type="submit" class="btn--sm btn btn-outline--base w-100">
                            <i class="las la-filter"></i> @lang('Filter Now')
                        </button>
                    </form>
                </aside>
                <div class="property-page-content">
                    <div class="text-end d-lg-none mb-4">
                        <button class="btn btn--sm btn-outline--base btn--sidebar-open" type="button" data-toggle="sidebar"
                            data-target="#property-page-sidebar">
                            <i class="las la-filter"></i>
                        </button>
                    </div>
                    <div class="row gy-4 g-sm-3 g-md-4 justify-content-center">
                        @include($activeTemplate . 'partials.deal', [
                            'offerings' => @$offerings,
                            'col' => '6',
                        ])
                        @if (@$offerings->hasPages())
                            {{ paginateLinks(@$offerings) }}
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    @if (@$sections->secs != null)
        @foreach (json_decode(@$sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.select2').select2();

            $(".on-change-submit").on('change', function(e) {
                $(this).closest('form').submit();
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .form-check-input:checked {
            background-color: hsla(var(--base));
            border-color: hsla(var(--base));
        }
        .property-page-sidebar{
            margin-top: 67px;
        }
        .form-check-input:focus {
            border-color: hsla(var(--base)/0.5);
            box-shadow: 0 0 0 0.25rem hsla(var(--base)/0.5);
        }

        .complete-data {
            width: 100%;
            padding-left: 50px;
            padding-right: 50px;
        }
    </style>
@endpush
