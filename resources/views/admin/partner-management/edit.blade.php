@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.partner-management.update', $partner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ $partner->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Email')</label>
                                <input type="email" class="form-control" name="email" value="{{ $partner->email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Password')</label>
                                <input type="password" class="form-control" name="password" placeholder="@lang('Leave blank if you do not want to change')">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Confirm Password')</label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="@lang('Leave blank if you do not want to change')">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Company Name')</label>
                                <input type="text" class="form-control" name="company_name" value="{{ $partner->company_name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Company Website')</label>
                                <input type="url" class="form-control" name="company_website" value="{{ $partner->company_website }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Company Description')</label>
                                <textarea class="form-control" name="company_description" rows="4">{{ $partner->company_description }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Company Logo')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['profile']['admin']['path'].'/'. $partner->company_logo, imagePath()['profile']['admin']['size']) }})">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="company_logo" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                            <label for="profilePicUpload1" class="bg--success">@lang('Upload Logo')</label>
                                            <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg, jpg, png')</b>. @lang('Image will be resized to'): {{imagePath()['profile']['admin']['size']}}px.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <select class="form-control" name="status" required>
                                    <option value="1" {{ $partner->status == 1 ? 'selected' : '' }}>@lang('Active')</option>
                                    <option value="0" {{ $partner->status == 0 ? 'selected' : '' }}>@lang('Inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Update Partner')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.partner-management.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-list"></i>@lang('All Partners')</a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            // Profile Image Upload
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.profilePicPreview').css('background-image', 'url(' + e.target.result + ')');
                        $('.profilePicPreview').removeClass('has-error');
                        $('.profilePicPreview').hide();
                        $('.profilePicPreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".profilePicUpload").on('change', function() {
                readURL(this);
            });
            
            $(".remove-image").on('click', function(){
                $(".profilePicPreview").css('background-image', 'none');
                $(".profilePicUpload").val('');
            });
        })(jQuery);
    </script>
@endpush
