@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $staff->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $staff->email }}" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="{{ $staff->username }}" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" required>
                                <option value="">Select Role</option>
                                
                                @if(count($executiveRoles) > 0)
                                    <optgroup label="Executive Roles">
                                        @foreach($executiveRoles as $role)
                                            <option value="{{ $role->id }}" {{ $staff->roles->contains($role->id) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(count($managerRoles) > 0)
                                    <optgroup label="Department Managers">
                                        @foreach($managerRoles as $role)
                                            <option value="{{ $role->id }}" {{ $staff->roles->contains($role->id) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(count($otherRoles) > 0)
                                    <optgroup label="Other Roles">
                                        @foreach($otherRoles as $role)
                                            <option value="{{ $role->id }}" {{ $staff->roles->contains($role->id) ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">Update Staff Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
