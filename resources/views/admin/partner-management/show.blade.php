@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('Partner Information')</h5>

                <div class="row">
                    <div class="col-md-12 text-center mb-4">
                        <div class="thumb">
                            <img src="{{ getImage(imagePath()['profile']['partner']['path'].'/'. $partner->company_logo, imagePath()['profile']['partner']['size']) }}" alt="@lang('Company Logo')" class="w-50">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Name')</label>
                            <input class="form-control" type="text" value="{{ $partner->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Email')</label>
                            <input class="form-control" type="text" value="{{ $partner->email }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Company Name')</label>
                            <input class="form-control" type="text" value="{{ $partner->company_name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Company Website')</label>
                            <input class="form-control" type="text" value="{{ $partner->company_website }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Company Description')</label>
                            <textarea class="form-control" rows="4" readonly>{{ $partner->company_description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Status')</label>
                            <input class="form-control" type="text" value="{{ $partner->status ? 'Active' : 'Inactive' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Created Date')</label>
                            <input class="form-control" type="text" value="{{ showDateTime($partner->created_at) }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('admin.partner-management.edit', $partner->id) }}" class="btn btn--primary mr-2">@lang('Edit Partner')</a>
                        <a href="{{ route('admin.partner-management.assign-deals.form', $partner->id) }}" class="btn btn--success">@lang('Assign Deals')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Partner Deals Section -->
<div class="row mt-50">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5>@lang('Assigned Deals')</h5>
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
                                    <td data-label="@lang('Deal')">{{ $partnerDeal->deal->name ?? 'N/A' }}</td>
                                    <td data-label="@lang('Type')">{{ $partnerDeal->deal->type ?? 'N/A' }}</td>
                                    <td data-label="@lang('Stage')">{{ $partnerDeal->deal->deal_stage ?? 'N/A' }}</td>
                                    <td data-label="@lang('Created')">{{ showDateTime($partnerDeal->created_at) }}</td>
                                    <td data-label="@lang('Action')">
                                        <form action="{{ route('admin.partner-management.remove-deal', [$partner->id, $partnerDeal->deal_id]) }}" method="POST" style="display:inline;">
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
    <a href="{{ route('admin.partner-management.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-list"></i>@lang('All Partners')</a>
@endpush
