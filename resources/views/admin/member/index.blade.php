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
                                @forelse($members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>{{ $member->username }}</td>
                                        <td>
                                            @foreach ($member->roles as $role)
                                                <span class="badge badge--primary">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.member.edit', $member->id) }}"
                                                class="btn btn-sm btn--primary">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn--danger deleteBtn" data-id="{{ $member->id }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No members found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($members->hasPages())
                    <div class="card-footer py-4">
                        {{ $members->links() }}
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
                    <h5 class="modal-title">Delete Member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this member?</p>
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

{{--  @push('breadcrumb-plugins')
    <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
        <i class="las la-plus"></i>Add New Staff
    </a>
@endpush  --}}

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.deleteBtn').on('click', function() {
                var modal = $('#deleteModal');
                var id = $(this).data('id');
                var form = document.getElementById('deleteForm');
                form.action = '{{ route('admin.member.destroy', '') }}/' + id;
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush