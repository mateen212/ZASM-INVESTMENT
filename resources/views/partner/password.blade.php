@extends('partner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-6 col-lg-8 col-md-12 mb-30 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Change Password')</h5>

                    <form action="{{ route('partner.password.update') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">@lang('Current Password')</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="current_password" required>
                                <span class="input-group-text toggle-password" data-target="current_password">
                                    <i class="las la-eye"></i>
                                </span>
                            </div>
                            <small class="form-text text-muted">@lang('Enter your current password')</small>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-control-label font-weight-bold">@lang('New Password')</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password" required>
                                <span class="input-group-text toggle-password" data-target="password">
                                    <i class="las la-eye"></i>
                                </span>
                            </div>
                            <small class="form-text text-muted">@lang('Password must be at least 6 characters')</small>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-control-label font-weight-bold">@lang('Confirm Password')</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="password_confirmation" required>
                                <span class="input-group-text toggle-password" data-target="password_confirmation">
                                    <i class="las la-eye"></i>
                                </span>
                            </div>
                            <small class="form-text text-muted">@lang('Re-enter your new password')</small>
                        </div>
                        
                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn--primary w-100">@lang('Update Password')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('partner.profile') }}" class="btn btn-sm btn-outline--primary"><i class="las la-user"></i>@lang('Profile')</a>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        
        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const target = $(this).data('target');
            const input = $('input[name="' + target + '"]');
            const icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('la-eye').addClass('la-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('la-eye-slash').addClass('la-eye');
            }
        });
    })(jQuery);
</script>
@endpush
