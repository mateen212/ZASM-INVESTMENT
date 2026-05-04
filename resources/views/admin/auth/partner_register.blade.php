@extends('templates.basic.layouts.app')

@section('main-content')
<section class="account">
    <div class="account-inner py-60 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <form method="POST" action="{{ route('partner.register') }}" class="account-form verify-gcaptcha">
                        @csrf
                        <div class="account-form__header text-center">
                            <a class="mb-5" href="{{ route('home') }}"> <img src="{{ siteLogo() }}"></a>
                            <h5 class="account-form__title mb-3">{{ __('Partner Onboarding') }}</h5>
                            <p class="account-form__subtitle">{{ __('Join our partner network and grow your business') }}</p>
                            
                            @php
                                $credentials = gs('socialite_credentials');
                            @endphp
                            
                            @if (
                                $credentials->google->status == Status::ENABLE ||
                                $credentials->facebook->status == Status::ENABLE ||
                                $credentials->linkedin->status == Status::ENABLE)
                                <div class="account-form__social-btns">
                                    @if ($credentials->facebook->status == Status::ENABLE)
                                        <div class="continue-facebook flex-grow-1">
                                            <a href="{{ route('partner.social.login', 'facebook') }}" class="btn w-100 facebook">
                                                <span class="facebook-icon">
                                                    <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
                                                </span> @lang('Facebook')
                                            </a>
                                        </div>
                                    @endif
                                    @if ($credentials->google->status == Status::ENABLE)
                                        <div class="continue-google flex-grow-1">
                                            <a href="{{ route('partner.social.login', 'google') }}" class="btn w-100 google">
                                                <span class="google-icon">
                                                    <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
                                                </span> @lang('Google')
                                            </a>
                                        </div>
                                    @endif
                                    @if ($credentials->linkedin->status == Status::ENABLE)
                                        <div class="continue-facebook flex-grow-1">
                                            <a href="{{ route('partner.social.login', 'linkedin') }}" class="btn w-100 linkedin">
                                                <span class="facebook-icon">
                                                    <img src="{{ asset($activeTemplateTrue . 'images/linkedin.svg') }}" alt="Linkedin">
                                                </span> @lang('Linkedin')
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="other-option">
                                    <span class="other-option__text">@lang('OR')</span>
                                </div>
                            @endif
                        </div>
                        <div class="account-form__body">
                            <div class="row gx-3">
                                <div class="col-xsm-12 col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Full Name') }}</label>
                                        <input class="form--control" type="text" name="name" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row gx-3">
                                <div class="col-xsm-12 col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Email Address') }}</label>
                                        <input class="form--control" type="email" name="email" value="{{ old('email') }}" required>
                                        <small class="text-muted">{{ __('Your email will be used as your username') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xsm-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Password') }}</label>
                                        <div class="position-relative">
                                            <input class="form--control" type="password" name="password" required>
                                        </div>
                                        <small class="text-muted">{{ __('Min 6 characters') }}</small>
                                    </div>
                                </div>
                                <div class="col-xsm-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Confirm Password') }}</label>
                                        <input class="form--control" type="password" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xsm-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Company Name') }}</label>
                                        <input class="form--control" type="text" name="company_name" value="{{ old('company_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-xsm-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Contact Number') }}</label>
                                        <input class="form--control" type="tel" name="contact_number" value="{{ old('contact_number') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xsm-12 col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Company Website') }}</label>
                                        <input class="form--control" type="url" name="company_website" value="{{ old('company_website') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xsm-12 col-sm-12">
                                    <div class="form-group">
                                        <label class="form--label">{{ __('Invitation Code / Invited By') }}</label>
                                        <input class="form--control" type="text" name="invited_by" value="{{ old('invited_by') }}">
                                        <small class="text-muted">{{ __('If you were invited by an existing partner, please enter their name or code') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row gx-3 mt-3">
                                <div class="col-12">
                                    <x-captcha />
                                </div>
                            </div>

                            <div class="row gx-3 mt-3">
                                <div class="col">
                                    @if (gs('agree'))
                                        @php
                                            $policyPages = getContent(
                                                'policy_pages.element',
                                                false,
                                                null,
                                                true,
                                            );
                                        @endphp
                                        <div class="form--check">
                                            <input class="form-check-input" type="checkbox" id="agree"
                                                @checked(old('agree')) name="agree" required>
                                            <label class="form-check-label" for="agree">@lang('I agree with ')
                                                @foreach ($policyPages as $policy)
                                                    <a href="{{ route('policy.pages', slug($policy->data_values->title)) }}"
                                                        target="_blank">{{ __($policy->data_values->title) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="account-form__footer">
                            <button type="submit" class="w-100 btn btn--base">{{ __('Register as Partner') }}</button>
                            <p class="account-form__subtitle mt-3">{{ __('Already have an account?') }}
                                <a href="{{ route('partner.login') }}" class="text--base">{{ __('Login') }}</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
    .account-inner {
        background-color: #ffffff;
    }
    .account-form {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }
    .account-form__title {
        color: #333;
        font-weight: 600;
    }
    .account-form__subtitle {
        color: #666;
    }
    .form--label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }
    .form--control {
        height: 50px;
        border-radius: 5px;
        border: 1px solid #e5e5e5;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    .form--control:focus {
        border-color: var(--base-color);
        box-shadow: none;
    }
    .btn--base {
        height: 50px;
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    .text--base {
        color: var(--base-color);
    }
    .account-form__social-btns {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .account-form__social-btns .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 45px;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s;
    }
    .facebook {
        background-color: #3b5998;
        color: white;
    }
    .google {
        background-color: #db4437;
        color: white;
    }
    .linkedin {
        background-color: #0077b5;
        color: white;
    }
    .facebook:hover, .google:hover, .linkedin:hover {
        opacity: 0.9;
        color: white;
    }
    .other-option {
        position: relative;
        text-align: center;
        margin: 20px 0;
    }
    .other-option:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background-color: #e5e5e5;
        z-index: 0;
    }
    .other-option__text {
        display: inline-block;
        padding: 0 15px;
        background-color: white;
        position: relative;
        z-index: 1;
        color: #666;
    }
</style>
@endpush
