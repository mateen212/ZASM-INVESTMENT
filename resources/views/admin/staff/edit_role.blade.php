@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Role: {{ $role->name }}</h5>
                </div>
                <form action="{{ route('admin.staff.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Role Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $role->name }}" required {{ in_array($role->name, ['Super Admin', 'CEO', 'CFO', 'COO', 'CTO', 'CLO']) ? 'readonly' : '' }}>
                        </div>
                        
                        <div class="form-group mt-4">
                            <label class="fw-bold mb-3">Permissions</label>
                            <div class="row">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="col-md-4 mb-4">
                                        <div class="permission-category-card">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5>{{ ucfirst($group) }}</h5>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input select-all" 
                                                           id="select_all_{{ $group }}" 
                                                           data-group="{{ $group }}">
                                                    <label class="form-check-label" for="select_all_{{ $group }}">
                                                        Select All
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="permission-checkboxes">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" 
                                                               class="form-check-input permission-checkbox {{ $group }}-checkbox"
                                                               id="permission_{{ $permission->id }}"
                                                               name="permissions[]" 
                                                               value="{{ $permission->name }}"
                                                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            @if($permission->display_name)
                                                                {{ ucfirst(str_replace($group.'.', '', $permission->name)) }}
                                                            @else
                                                                {{ ucfirst(str_replace($group.'.', '', $permission->name)) }}
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100">Update Role</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .permission-category-card {
        border: 1px solid #e5e5e5;
        border-radius: 5px;
        padding: 15px;
        height: 100%;
        background-color: #f9f9f9;
    }
    
    .permission-checkboxes {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 10px;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Handle "Select All" checkboxes
        $('.select-all').on('change', function() {
            var group = $(this).data('group');
            var isChecked = $(this).prop('checked');
            
            $('.' + group + '-checkbox').prop('checked', isChecked);
        });
        
        // Update "Select All" checkbox state based on individual checkboxes
        $('.permission-checkbox').on('change', function() {
            var group = $(this).attr('class').split(' ')[1].split('-')[0];
            var totalCheckboxes = $('.' + group + '-checkbox').length;
            var checkedCheckboxes = $('.' + group + '-checkbox:checked').length;
            
            $('#select_all_' + group).prop('checked', totalCheckboxes === checkedCheckboxes);
        });
        
        // Initialize "Select All" checkbox states
        $('.select-all').each(function() {
            var group = $(this).data('group');
            var totalCheckboxes = $('.' + group + '-checkbox').length;
            var checkedCheckboxes = $('.' + group + '-checkbox:checked').length;
            
            $(this).prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        });
    })(jQuery);
</script>
@endpush
