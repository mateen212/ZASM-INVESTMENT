@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Member</h5>
                    <form action="{{ route('admin.member.update', $member->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" value="{{ $member->name }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" value="{{ $member->email }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" value="{{ $member->username }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                @foreach ($roles as $roleName)
                                    <option value="{{ $roleName }}" {{ $member->hasRole($roleName) ? 'selected' : '' }}>
                                        {{ ucfirst($roleName) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn--primary">Update</button>
                        <a href="{{ route('admin.member.index') }}" class="btn btn--secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection