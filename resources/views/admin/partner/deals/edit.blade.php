@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.partner.deals.update', $deal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Name')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ $deal->name }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Type')</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="type" required>
                                <option value="" selected disabled>@lang('Select Type')</option>
                                <option value="Equity" {{ $deal->type == 'Equity' ? 'selected' : '' }}>@lang('Equity')</option>
                                <option value="Debt" {{ $deal->type == 'Debt' ? 'selected' : '' }}>@lang('Debt')</option>
                                <option value="Preferred Equity" {{ $deal->type == 'Preferred Equity' ? 'selected' : '' }}>@lang('Preferred Equity')</option>
                                <option value="Fund" {{ $deal->type == 'Fund' ? 'selected' : '' }}>@lang('Fund')</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Stage')</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="deal_stage" required>
                                <option value="" selected disabled>@lang('Select Stage')</option>
                                <option value="Draft" {{ $deal->deal_stage == 'Draft' ? 'selected' : '' }}>@lang('Draft')</option>
                                <option value="Active" {{ $deal->deal_stage == 'Active' ? 'selected' : '' }}>@lang('Active')</option>
                                <option value="Closed" {{ $deal->deal_stage == 'Closed' ? 'selected' : '' }}>@lang('Closed')</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional deal fields can be added here -->

                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Update Deal')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.partner.deals.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-list"></i>@lang('All Deals')</a>
    <a href="{{ route('admin.partner.deals.show', $deal->id) }}" class="btn btn-sm btn--success box--shadow1 text--small"><i class="fa fa-fw fa-eye"></i>@lang('View')</a>
@endpush
