@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStaff as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge badge--primary">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm btn--primary">
                                                <i class="las la-edit"></i>
                                            </a>
                                            @if(!$user->hasRole('super-admin'))
                                                <button type="button" 
                                                        class="btn btn-sm btn--danger deleteBtn"
                                                        data-toggle="modal" 
                                                        data-target="#deleteModal"
                                                        data-id="{{ $user->id }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">No staff members found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($allStaff->hasPages())
                    <div class="card-footer py-4">
                        {{ $allStaff->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Staff Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this staff member?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn--danger">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="las la-plus"></i>Add New Staff
    </a>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        
        $('.deleteBtn').on('click', function() {
            var modal = $('#deleteModal');
            var id = $(this).data('id');
            var form = document.getElementById('deleteForm');
            form.action = '{{ route('admin.staff.destroy', '') }}/' + id;
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
