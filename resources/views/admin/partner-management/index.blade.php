@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('Company')</th>
                                <th>@lang('Deals')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partners as $partner)
                                <tr>
                                    <td data-label="@lang('Name')">{{ $partner->name }}</td>
                                    <td data-label="@lang('Email')">{{ $partner->email }}</td>
                                    <td data-label="@lang('Company')">{{ $partner->company_name }}</td>
                                    <td data-label="@lang('Deals')">{{ $partner->partnerDeals->count() }}</td>
                                    <td data-label="@lang('Status')">
                                        @if($partner->status == 1)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @elseif($partner->status == 0)
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @elseif($partner->status == 2)
                                            <span class="badge badge--warning">@lang('Paused')</span>
                                        @elseif($partner->status == 3)
                                            <span class="badge badge--dark">@lang('Terminated')</span>
                                        @else
                                            <span class="badge badge--dark">@lang('Unknown')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <div class="d-flex flex-wrap justify-content-end gap-1">
                                            <div class="dropdown">
                                                <button class="icon-btn dropdown-toggle" type="button" id="dropdownMenu{{ $partner->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="las la-cog text--shadow"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $partner->id }}" style="min-width: 200px; max-height: none; overflow: visible;">
                                                    <li><h6 class="dropdown-header">@lang('Change Status')</h6></li>
                                                    <li><a href="{{ route('admin.partner-management.status', $partner->id) }}?status=1" class="dropdown-item">
                                                        <i class="las la-check-circle text--success"></i> @lang('Set Active')
                                                    </a></li>
                                                    <li><a href="{{ route('admin.partner-management.status', $partner->id) }}?status=0" class="dropdown-item">
                                                        <i class="las la-times-circle text--danger"></i> @lang('Set Inactive')
                                                    </a></li>
                                                    <li><a href="{{ route('admin.partner-management.status', $partner->id) }}?status=2" class="dropdown-item">
                                                        <i class="las la-pause-circle text--warning"></i> @lang('Set Paused')
                                                    </a></li>
                                                    <li><a href="{{ route('admin.partner-management.status', $partner->id) }}?status=3" class="dropdown-item">
                                                        <i class="las la-ban text--dark"></i> @lang('Set Terminated')
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @can('partnerships.delete')
                                                    <li><a href="javascript:void(0)" class="dropdown-item delete-partner" data-id="{{ $partner->id }}">
                                                        <i class="las la-trash text--danger"></i> @lang('Delete Partner')
                                                    </a></li>
                                                    @endcan
                                                </ul>
                                            </div>
                                            <a href="{{ route('admin.partner-management.show', $partner->id) }}" class="icon-btn" data-bs-toggle="tooltip" data-original-title="@lang('Details')">
                                                <i class="las la-desktop text--shadow"></i>
                                            </a>
                                            <a href="{{ route('admin.partner-management.edit', $partner->id) }}" class="icon-btn" data-bs-toggle="tooltip" data-original-title="@lang('Edit')">
                                                <i class="las la-edit text--shadow"></i>
                                            </a>
                                            <a href="{{ route('admin.partner-management.assign-deals.form', $partner->id) }}" class="icon-btn" data-bs-toggle="tooltip" data-original-title="@lang('Assign Deals')">
                                                <i class="las la-handshake text--shadow"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div class="pagination-wrapper">
                        {{ $partners->appends(['per_page' => $perPage])->links() }}
                    </div>
                    <div class="page-size-selector d-flex align-items-center">
                        <span class="mr-2">@lang('Show'):</span>
                        <div class="btn-group">
                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 40]) }}" class="btn btn-sm {{ $perPage == 40 ? 'btn--primary' : 'btn--dark' }}">40</a>
                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 80]) }}" class="btn btn-sm {{ $perPage == 80 ? 'btn--primary' : 'btn--dark' }}">80</a>
                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 160]) }}" class="btn btn-sm {{ $perPage == 160 ? 'btn--primary' : 'btn--dark' }}">160</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.partner-management.create') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-plus"></i>@lang('Add New Partner')</a>
@endpush

@push('style')
<style>
    @media (max-width: 767px) {
        .page-size-selector {
            margin-top: 15px;
            width: 100%;
            justify-content: center;
        }
        .pagination-wrapper {
            width: 100%;
            justify-content: center;
            margin-bottom: 15px;
        }
        .card-footer .d-flex {
            flex-direction: column;
        }
        .table-responsive--md td[data-label="Action"] .d-flex {
            justify-content: flex-start !important;
        }
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Handle delete partner
        $('.delete-partner').on('click', function() {
            const partnerId = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send delete request
                    $.ajax({
                        url: `{{ route('admin.partner-management.destroy', '') }}/${partnerId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Partner has been deleted.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(error) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the partner.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
