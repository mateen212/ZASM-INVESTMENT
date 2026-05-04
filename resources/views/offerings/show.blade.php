@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $offering->name }}</h1>
    <p>{{ $offering->summary }}</p>
    <!-- Add more offering details here -->
</div>
@endsection
