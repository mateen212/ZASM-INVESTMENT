@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Executive Roles Section -->
                    @if(count($executiveRoles) > 0)
                        <h4 class="mb-3">Executive Roles</h4>
                        <div class="table-responsive--md mb-4">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Role')</th>
                                        <th>@lang('Permissions')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($executiveRoles as $role)
                                        <tr>
                                            <td data-label="@lang('Role')">
                                                <span class="fw-bold">{{ $role->name }}</span>
                                            </td>
                                            <td data-label="@lang('Permissions')">
                                                <div class="permission-badges">
                                                    @php
                                                        // Group permissions by category
                                                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                                            // Special case for subscriber-related permissions
                                                            if ($permission->name === 'subscriber' || strpos($permission->name, 'subscriber.') === 0) {
                                                                return 'subscribers'; // Use plural form to match sidebar
                                                            }
                                                            
                                                            $parts = explode('.', $permission->name);
                                                            return count($parts) > 1 ? $parts[0] : 'general';
                                                        });
                                                        
                                                        // Sort categories alphabetically
                                                        $groupedPermissions = $groupedPermissions->sortKeys();
                                                    @endphp
                                                    
                                                    @foreach($groupedPermissions as $category => $permissions)
                                                        <div class="permission-category">
                                                            <span class="category-name">{{ ucfirst($category) }} <small>({{ count($permissions) }})</small></span>
                                                            <div class="permission-list">
                                                                @foreach($permissions->sortBy('name') as $permission)
                                                                    <span class="badge badge--primary">{{ str_replace($category.'.', '', $permission->name) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td data-label="@lang('Action')">
                                                <div class="button--group">
                                                    <a href="{{ route('admin.staff.roles.edit', $role->id) }}"
                                                        class="btn btn-sm btn--primary">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    @if(!in_array($role->name, ['Super Admin', 'CEO']))
                                                        <button type="button"
                                                            class="btn btn-sm btn--danger deleteButton"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-delete-route="{{ route('admin.staff.roles.destroy', $role->id) }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    <!-- Department Manager Roles Section -->
                    @if(count($managerRoles) > 0)
                        <h4 class="mb-3">Department Manager Roles</h4>
                        <div class="table-responsive--md mb-4">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Role')</th>
                                        <th>@lang('Permissions')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($managerRoles as $role)
                                        <tr>
                                            <td data-label="@lang('Role')">
                                                <span class="fw-bold">{{ $role->name }}</span>
                                            </td>
                                            <td data-label="@lang('Permissions')">
                                                <div class="permission-badges">
                                                    @php
                                                        // Group permissions by category
                                                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                                            // Special case for subscriber-related permissions
                                                            if ($permission->name === 'subscriber' || strpos($permission->name, 'subscriber.') === 0) {
                                                                return 'subscribers'; // Use plural form to match sidebar
                                                            }
                                                            
                                                            $parts = explode('.', $permission->name);
                                                            return count($parts) > 1 ? $parts[0] : 'general';
                                                        });
                                                        
                                                        // Sort categories alphabetically
                                                        $groupedPermissions = $groupedPermissions->sortKeys();
                                                    @endphp
                                                    
                                                    @foreach($groupedPermissions as $category => $permissions)
                                                        <div class="permission-category">
                                                            <span class="category-name">{{ ucfirst($category) }} <small>({{ count($permissions) }})</small></span>
                                                            <div class="permission-list">
                                                                @foreach($permissions->sortBy('name') as $permission)
                                                                    <span class="badge badge--primary">{{ str_replace($category.'.', '', $permission->name) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td data-label="@lang('Action')">
                                                <div class="button--group">
                                                    <a href="{{ route('admin.staff.roles.edit', $role->id) }}"
                                                        class="btn btn-sm btn--primary">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn--danger deleteButton"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-delete-route="{{ route('admin.staff.roles.destroy', $role->id) }}">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    <!-- Other Roles Section -->
                    @if(count($otherRoles) > 0)
                        <h4 class="mb-3">Other Roles</h4>
                        <div class="table-responsive--md">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Role')</th>
                                        <th>@lang('Permissions')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otherRoles as $role)
                                        <tr>
                                            <td data-label="@lang('Role')">
                                                <span class="fw-bold">{{ $role->name }}</span>
                                            </td>
                                            <td data-label="@lang('Permissions')">
                                                <div class="permission-badges">
                                                    @php
                                                        // Group permissions by category
                                                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                                            // Special case for subscriber-related permissions
                                                            if ($permission->name === 'subscriber' || strpos($permission->name, 'subscriber.') === 0) {
                                                                return 'subscribers'; // Use plural form to match sidebar
                                                            }
                                                            
                                                            $parts = explode('.', $permission->name);
                                                            return count($parts) > 1 ? $parts[0] : 'general';
                                                        });
                                                        
                                                        // Sort categories alphabetically
                                                        $groupedPermissions = $groupedPermissions->sortKeys();
                                                    @endphp
                                                    
                                                    @foreach($groupedPermissions as $category => $permissions)
                                                        <div class="permission-category">
                                                            <span class="category-name">{{ ucfirst($category) }} <small>({{ count($permissions) }})</small></span>
                                                            <div class="permission-list">
                                                                @foreach($permissions->sortBy('name') as $permission)
                                                                    <span class="badge badge--primary">{{ str_replace($category.'.', '', $permission->name) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td data-label="@lang('Action')">
                                                <div class="button--group">
                                                    <a href="{{ route('admin.staff.roles.edit', $role->id) }}"
                                                        class="btn btn-sm btn--primary">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    @if($role->name !== 'Staff')
                                                        <button type="button"
                                                            class="btn btn-sm btn--danger deleteButton"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-delete-route="{{ route('admin.staff.roles.destroy', $role->id) }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{--  @if (auth()->guard('admin')->user()->hasRole('Super Admin') === true)
            here add the below data
        @endif  --}}        
        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Create New Role</h4>
                    <form action="{{ route('admin.staff.roles.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">@lang('Role Name')</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Permissions')</label>
                                    <div class="row">
                                        @foreach($permissions as $category => $categoryPermissions)
                                            <div class="col-md-4 mb-3">
                                                <div class="permission-category-card">
                                                    <h5 class="mb-2">{{ ucfirst($category) }}</h5>
                                                    <div class="permission-checkboxes">
                                                        @foreach($categoryPermissions as $permission)
                                                            @if(is_object($permission) && isset($permission->id))
                                                            <div class="form-check">
                                                                <input type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}" value="{{ $permission->name }}" class="form-check-input">
                                                                <label for="permission-{{ $permission->id }}" class="form-check-label">{{ str_replace($category.'.', '', $permission->name) }}</label>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100">@lang('Create Role')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">@lang('Delete Role')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure you want to delete this role?')</p>
                </div>
                <div class="modal-footer">
                    <form action="" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--danger">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .permission-badges {
        max-height: 250px;
        overflow-y: auto;
        display: block;
        max-width: 100%;
        padding-right: 5px;
    }
    
    .permission-badges .badge {
        margin: 2px 4px 2px 0;
        white-space: normal;
        text-align: left;
        line-height: 1.3;
        display: inline-block;
        padding: 5px 8px;
        font-size: 0.85rem;
    }
    
    .permission-category {
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        clear: both;
    }
    
    .permission-list {
        padding-left: 5px;
    }
    
    .category-name {
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
        background-color: #f5f5f5;
        padding: 5px 8px;
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    
    .category-name small {
        color: #666;
        font-weight: normal;
    }
    
    .permission-category-card {
        border: 1px solid #e5e5e5;
        border-radius: 5px;
        padding: 15px;
        height: 100%;
        background-color: #f9f9f9;
    }
    
    .permission-checkboxes {
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Custom scrollbar for better UX */
    .permission-badges::-webkit-scrollbar {
        width: 8px;
    }
    
    .permission-badges::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .permission-badges::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .permission-badges::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Mobile-friendly table styles */
    @media (max-width: 767px) {
        .table-responsive--md {
            overflow-x: visible;
            -webkit-overflow-scrolling: touch;
        }
        
        .table--light.style--two thead {
            display: none;
        }
        
        .table--light.style--two tbody tr {
            display: block;
            border: 1px solid #e5e5e5;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table--light.style--two tbody td {
            display: block;
            width: 100% !important;
            text-align: left;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }
        
        .table--light.style--two tbody td:last-child {
            border-bottom: none;
        }
        
        .table--light.style--two tbody td:before {
            content: attr(data-label);
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.85rem;
        }
        
        .permission-badges {
            max-height: none;
            border: none;
            padding: 0;
        }
        
        .button--group {
            display: flex;
            justify-content: flex-start;
            margin-top: 5px;
        }
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        $('.deleteButton').on('click', function() {
            var route = $(this).data('delete-route');
            $('#deleteForm').attr('action', route);
        });
    })(jQuery);
</script>
@endpush