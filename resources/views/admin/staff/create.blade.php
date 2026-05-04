@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.staff.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Full Name')" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="email" class="form-control" placeholder="@lang('Email Address')" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Username')" name="username" value="{{ old('username') }}" required>
                                    <small class="text-muted">@lang('Minimum 6 characters, alphanumeric')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Role')</label>
                                    <select class="form-control" name="role" required>
                                        <option value="">@lang('Select Role')</option>
                                        
                                        @if(count($executiveRoles) > 0)
                                            <optgroup label="Executive Roles">
                                                @foreach($executiveRoles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        @if(count($managerRoles) > 0)
                                            <optgroup label="Department Managers">
                                                @foreach($managerRoles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        
                                        @if(count($otherRoles) > 0)
                                            <optgroup label="Other Roles">
                                                @foreach($otherRoles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="password" class="form-control" placeholder="@lang('Password')" name="password" required>
                                    <small class="text-muted">@lang('Minimum 6 characters')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Confirm Password')</label>
                                    <input type="password" class="form-control" placeholder="@lang('Confirm Password')" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">
                                        @lang('Create Staff Member')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn--primary">
        <i class="las la-list"></i>@lang('All Staff')
    </a>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Add any necessary JavaScript for form validation or dynamic behavior
        
    })(jQuery);
</script>
@endpush