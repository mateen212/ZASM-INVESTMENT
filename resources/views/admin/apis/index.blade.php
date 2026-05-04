@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('API Key')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($apis as $api)
                                    <tr>
                                        <td>{{ $api->name }}</td>
                                        <td>
                                            @if($api->status == 1)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($api->getCredentialValue('api_key'))
                                                <code>{{ substr($api->getCredentialValue('api_key'), 0, 10) }}...</code>
                                            @else
                                                <span class="text-muted">@lang('Not Set')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.other-apis.edit', $api->code) }}" class="btn btn-sm btn--primary">@lang('Edit')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="4">@lang('No API integrations found')</td>
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
