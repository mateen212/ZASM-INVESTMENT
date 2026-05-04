@extends('partner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Company Profile')</h5>

                    <form action="{{ route('partner.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="form-group">
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview" style="width: 300px; height: 200px;">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['profile']['partner']['path'].'/'. auth()->guard('admin')->user()->company_logo, imagePath()['profile']['partner']['size']) }}); background-size: contain; background-position: center; background-repeat: no-repeat; width: 100%; height: 100%;">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit mt-3">
                                                <input type="file" class="profilePicUpload" name="company_logo" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload1" class="bg--primary">@lang('Upload Company Logo')</label>
                                                <small class="mt-2 text-muted d-block">@lang('Supported files'): <b>@lang('jpeg, jpg, png')</b>. @lang('Image will be resized to'): {{imagePath()['profile']['partner']['size']}}px.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Name')</label>
                                    <input class="form-control" type="text" name="name" value="{{ auth()->guard('admin')->user()->name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="{{ auth()->guard('admin')->user()->email }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Company Name')</label>
                                    <input class="form-control" type="text" name="company_name" value="{{ auth()->guard('admin')->user()->company_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Company Website')</label>
                                    <input class="form-control" type="url" name="company_website" value="{{ auth()->guard('admin')->user()->company_website }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Company Description')</label>
                                    <textarea class="form-control" name="company_description" rows="4">{{ auth()->guard('admin')->user()->company_description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary">@lang('Save Changes')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('partner.password') }}" class="btn btn-sm btn-outline--primary"><i class="las la-key"></i>@lang('Change Password')</a>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        
        // Define a default image if needed
        const defaultImage = "{{ getImage(imagePath()['profile']['partner']['path'].'/default.png', imagePath()['profile']['partner']['size']) }}";
        
        $('.profilePicUpload').on('change', function(e) {
            readURL(this);
        });

        $('.remove-image').on('click', function(){
            $('.profilePicPreview').css({
                'background-image': 'url(' + defaultImage + ')',
                'background-size': 'contain',
                'background-position': 'center',
                'background-repeat': 'no-repeat'
            });
            $('.profilePicUpload').val('');
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.profilePicPreview').css({
                        'background-image': 'url(' + e.target.result + ')',
                        'background-size': 'contain',
                        'background-position': 'center',
                        'background-repeat': 'no-repeat'
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    })(jQuery);
</script>
@endpush
