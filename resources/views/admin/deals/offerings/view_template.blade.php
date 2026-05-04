@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header">
            {{--  <h4 class="mb-0">{{ $template->template_name }}</h4>
            <p class="text-muted mb-0">Type: {{ $template->template_type }}</p>  --}}
        </div>
        <div class="card-body text-center">
            <iframe src="{{ asset($template->file_path) }}" width="100%" height="800px" style="border: none;"></iframe>
        </div>
    </div>
</div>
@endsection
