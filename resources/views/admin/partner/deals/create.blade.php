@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.partner.deals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Name')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Type')</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="type" required>
                                <option value="" selected disabled>@lang('Select Type')</option>
                                <option value="Equity">@lang('Equity')</option>
                                <option value="Debt">@lang('Debt')</option>
                                <option value="Preferred Equity">@lang('Preferred Equity')</option>
                                <option value="Fund">@lang('Fund')</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label">@lang('Deal Stage')</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="deal_stage" required>
                                <option value="" selected disabled>@lang('Select Stage')</option>
                                <option value="Draft">@lang('Draft')</option>
                                <option value="Active">@lang('Active')</option>
                                <option value="Closed">@lang('Closed')</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional deal fields can be added here -->

                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Create Deal')</button>
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
@endpush
