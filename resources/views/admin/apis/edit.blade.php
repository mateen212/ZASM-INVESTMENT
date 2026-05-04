@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">{{ $api->name }}</h4>
                        <a href="{{ route('admin.other-apis.index') }}" class="btn btn--dark btn-sm"><i class="las la-arrow-left"></i> Back</a>
                    </div>
                    <p class="text-muted mb-4">{{ $api->description }}</p>
                    
                    <form action="{{ route('admin.other-apis.update', $api->code) }}" method="POST">
                        @csrf
                        <div class="row">
                            @foreach(json_decode(json_encode($api->credentials), true) as $key => $credential)
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label>{{ $credential['title'] }}</label>
                                        <input type="{{ $credential['type'] }}" name="{{ $key }}" class="form-control" value="{{ $credential['value'] }}" placeholder="{{ $credential['title'] }}">
                                        <small class="text-muted">{{ $credential['description'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ $api->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
