@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-wallet"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{ $totalDeals }}</span>
                </div>
                <div class="desciption">
                    <span class="text--small">@lang('Total Deals')</span>
                </div>
                <a href="{{ route('partner.deals.index') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div>
    <!-- Add more dashboard widgets as needed -->
</div>

<div class="row mt-50 mb-none-30">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Partner Information')</h5>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="image-upload">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['profile']['partner']['path'].'/'. auth()->guard('admin')->user()->company_logo, imagePath()['profile']['partner']['size']) }})">
                                            <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Company Name')</label>
                            <input class="form-control" type="text" value="{{ auth()->guard('admin')->user()->company_name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('Company Website')</label>
                            <input class="form-control" type="text" value="{{ auth()->guard('admin')->user()->company_website }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('Company Description')</label>
                            <textarea class="form-control" rows="4" readonly>{{ auth()->guard('admin')->user()->company_description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('partner.profile.index') }}" class="btn btn--primary">@lang('Edit Profile')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-50">
    <div class="col-lg-12">
        <div class="card b-radius--10">
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
                            @forelse($deals as $partnerDeal)
                                <tr>
                                    <td data-label="@lang('Deal')">{{ $partnerDeal->deal->name ?? 'N/A' }}</td>
                                    <td data-label="@lang('Type')">{{ $partnerDeal->deal->type ?? 'N/A' }}</td>
                                    <td data-label="@lang('Stage')">{{ $partnerDeal->deal->deal_stage ?? 'N/A' }}</td>
                                    <td data-label="@lang('Created')">{{ showDateTime($partnerDeal->deal->created_at ?? now()) }}</td>
                                    <td data-label="@lang('Action')">
                                        @if($partnerDeal->deal)
                                        <a href="{{ route('partner.deals.show', ['deal' => $partnerDeal->deal->id]) }}" class="icon-btn" data-toggle="tooltip" data-original-title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No deals found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-4">
                {{-- Pagination is removed as deals is a regular collection, not a paginated one --}}
            </div>
        </div>
    </div>
</div>
@endsection
