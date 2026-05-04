@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('Deal Information')</h5>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Deal Name')</label>
                            <input class="form-control" type="text" value="{{ $deal->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Deal Type')</label>
                            <input class="form-control" type="text" value="{{ $deal->type }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Deal Stage')</label>
                            <input class="form-control" type="text" value="{{ $deal->deal_stage }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Created Date')</label>
                            <input class="form-control" type="text" value="{{ showDateTime($deal->created_at) }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Additional deal details can be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Deal Classes Section -->
<div class="row mt-50">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5>@lang('Deal Classes')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Class Name')</th>
                                <th>@lang('Min Investment')</th>
                                <th>@lang('Max Investment')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deal->deal_class as $class)
                                <tr>
                                    <td data-label="@lang('Class Name')">{{ $class->name }}</td>
                                    <td data-label="@lang('Min Investment')">{{ showAmount($class->min_investment) }}</td>
                                    <td data-label="@lang('Max Investment')">{{ showAmount($class->max_investment) }}</td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.deals.class.showClass', [$deal->id, $class->id]) }}" class="icon-btn" data-toggle="tooltip" data-original-title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No classes found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assets Section -->
<div class="row mt-50">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5>@lang('Deal Assets')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Asset Name')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Location')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deal->assets as $asset)
                                <tr>
                                    <td data-label="@lang('Asset Name')">{{ $asset->name }}</td>
                                    <td data-label="@lang('Type')">{{ $asset->type }}</td>
                                    <td data-label="@lang('Location')">{{ $asset->location }}</td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.assets.edit', $asset->id) }}" class="icon-btn" data-toggle="tooltip" data-original-title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No assets found')</td>
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
    <a href="{{ route('admin.partner.deals.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-list"></i>@lang('All Deals')</a>
    <a href="{{ route('admin.partner.deals.edit', $deal->id) }}" class="btn btn-sm btn--success box--shadow1 text--small"><i class="fa fa-fw fa-edit"></i>@lang('Edit')</a>
@endpush
