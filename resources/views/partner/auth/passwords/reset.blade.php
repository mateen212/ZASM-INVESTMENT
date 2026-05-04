@extends('admin.layouts.master')
@section('content')
<div class="login-main"
    style="background-image: url('{{ asset('assets/admin/images/login.jpg') }}')">
    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                <div class="login-area">
                    <div class="login-wrapper">
                        <div class="login-wrapper__top">
                            <h3 class="title text-white">@lang('Reset Password')</h3>
                            <p class="text-white">@lang('Create a new password for your account')</p>
                        </div>
                        <div class="login-wrapper__body">
                            <form action="{{ route('partner.password.update') }}" method="POST" class="cmn-form mt-30">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="email" class="form-control" value="{{ $email }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>@lang('New Password')</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Confirm Password')</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                                <button type="submit" class="btn cmn-btn w-100">@lang('Submit')</button>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('partner.login') }}" class="text-muted">@lang('Back to Login')</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
