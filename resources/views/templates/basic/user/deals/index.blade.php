@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="flex-end mb-4 breadcrumb-dashboard">
        <form>
            <div class="input-group">
                <input type="text" name="search" class="form--control" value="{{ request()->search }}"
                    placeholder="@lang('Property name')">
                <button class="btn--base btn" type="submit">
                    <span class="icon"><i class="la la-search"></i></span>
                </button>
            </div>
        </form>
    </div>
    <div class="row dashboard-widget-wrapper justify-content-center">
        <div class="col-md-12">
            @if (count($deals) > 0)
                <div class="table-responsive table--responsive--xl">
                    <table class="table custom--table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('SEC Type')</th>
                                <th>@lang('Close Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deals as $deal)
                                <tr>
                                    <td data-label="@lang('Name')">{{ $deal->name }}</td>
                                    <td data-label="@lang('Type')">{{ $deal->type }}</td>
                                    <td data-label="@lang('SEC Type')">{{ $deal->sec_type }}</td>
                                    <td data-label="@lang('Close Date')">{{ showDateTime($deal->close_date) }}</td>
                                    <td data-label="@lang('Action')">
                                        <button class="btn--base btn-sm detailBtn" data-deal="{{ json_encode($deal) }}">
                                            <i class="las la-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($deals->hasPages())
                    {{ $deals->links() }}
                @endif
            @else
                <div class="text-center">
                    @include($activeTemplate . 'partials.empty', ['message' => 'No investment found!'])
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade custom--modal" id="detailModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Investment Details')</h5>
                    <button class="close-btn" type="button" data-bs-dismiss="modal">
                        <i class="las fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-form__header">
                        <ul class="list-group userData mb-2 list-group-flush"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                let modal = $('#detailModal');
                let deal = $(this).data('deal');
                let curSymbol = '{{ gs('cur_sym') }}';
                let html = '';
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Property')</span>
                            <span class="list--group-desc"><strong>${deal.name}</strong></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Total Invest Amount')</span>
                            <span class="list--group-desc"><strong>${deal.type}</strong></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Paid Amount')</span>
                            <span class="list--group-desc">${deal.sec_type}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Next Profit Date')</span>
                            <span class="list--group-desc">${deal.close_date}</span>
                        </li>`;

                modal.find('.userData').html(html);
                modal.modal('show');
            });

            function formatTime(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }
        })(jQuery);
    </script>
@endpush
