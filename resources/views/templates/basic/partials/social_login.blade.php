@php
    $credentials = gs('socialite_credentials');
    $text = isset($register) ? 'Register' : 'Login';
    $googleStatus = optional(optional($credentials)->google)->status ?? Status::DISABLE;
    $facebookStatus = optional(optional($credentials)->facebook)->status ?? Status::DISABLE;
    $linkedinStatus = optional(optional($credentials)->linkedin)->status ?? Status::DISABLE;
@endphp

@if ($googleStatus == Status::ENABLE || $facebookStatus == Status::ENABLE || $linkedinStatus == Status::ENABLE)
    <div class="account-form__social-btns">
        @if ($facebookStatus == Status::ENABLE)
            <div class="continue-facebook flex-grow-1">
                <a href="{{ route('user.social.login', 'facebook') }}" class="btn w-100 facebook">
                    <span class="facebook-icon">
                        <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
                    </span> @lang('Facebook')
                </a>
            </div>
        @endif
        @if ($googleStatus == Status::ENABLE)
            <div class="continue-google flex-grow-1">
                <a href="{{ route('user.social.login', 'google') }}" class="btn w-100 google">
                    <span class="google-icon">
                        <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
                    </span> @lang('Google')
                </a>
            </div>
        @endif
        @if ($linkedinStatus == Status::ENABLE)
            <div class="continue-facebook flex-grow-1">
                <a href="{{ route('user.social.login', 'linkedin') }}" class="btn w-100 linkedin">
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
