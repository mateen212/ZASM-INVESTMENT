@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('Assign Deals to Partner'): {{ $partner->name }} ({{ $partner->company_name }})</h5>

                <form action="{{ route('admin.partner-management.assign-deals', $partner->id) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label class="font-weight-bold">@lang('Select Deals')</label>
                        <select class="form-control select2-multi-select" name="deal_ids[]" multiple required>
                            @foreach($availableDeals as $deal)
                                <option value="{{ $deal->id }}" {{ in_array($deal->id, $assignedDealIds) ? 'selected' : '' }}>
                                    {{ $deal->name }} ({{ $deal->type }}) - {{ $deal->deal_stage }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Assign Deals')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Currently Assigned Deals -->
<div class="row mt-50">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5>@lang('Currently Assigned Deals')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Deal')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Stage')</th>
                                <th>@lang('Created')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partnerDeals as $partnerDeal)
                                <tr>
                                    <td data-label="@lang('Deal')">{{ $partnerDeal->deal->name }}</td>
                                    <td data-label="@lang('Type')">{{ $partnerDeal->deal->type }}</td>
                                    <td data-label="@lang('Stage')">{{ $partnerDeal->deal->deal_stage }}</td>
                                    <td data-label="@lang('Created')">{{ showDateTime($partnerDeal->created_at) }}</td>
                                    <td data-label="@lang('Action')">
                                        <form action="{{ route('admin.partner-management.remove-deal', [$partner->id, $partnerDeal->deal->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn btn--danger" data-toggle="tooltip" data-original-title="@lang('Remove')" onclick="return confirm('Are you sure you want to remove this deal from the partner?')">
                                                <i class="las la-trash text--shadow"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No deals assigned')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.partner-management.show', $partner->id) }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-user"></i>@lang('Partner Details')</a>
    <a href="{{ route('admin.partner-management.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-list"></i>@lang('All Partners')</a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/select2.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/select2.min.css') }}">
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            // Initialize select2
            $('.select2-multi-select').select2({
                placeholder: 'Select deals to assign',
                allowClear: true
            });
        })(jQuery);
    </script>
@endpush
