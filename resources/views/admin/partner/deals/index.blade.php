@extends('admin.layouts.app')
@section('panel')
    <div class="card card-body" x-data="dashboard()" x-cloak>
        <div class="d-flex justify-content-between pb-4">
            <h1>Partner Deals</h1>
            <button type="button" class="btn btn_primary" data-bs-toggle="modal" data-bs-target="#addDealModal">Add Deal  <span class="ms-4 text-white fw-bold">+</span></button>
        </div>
        <style>
            .btn_primary {
                background-color: blue;
                border-radius: 4px;
                padding: 0px;
                height: 40px;
                width: 200px;
                display: flex;
                justify-content: center;
                float: right;
                color: white;
                font-weight: bold;
                border: none;
                padding: 10px 16px;
                cursor: pointer;
            }
            .btn_primary:hover {
                color: white;
                border-radius: 4px;
                padding: 0px;
                height: 40px;
                width: 200px;
                display: flex;
                float: right;
                background-color: #69A2FF;
                font-weight: bold;
                border: none;
                padding: 10px 16px;
                cursor: pointer;
                border-radius: 4px;
                transition: 0.3sec;
            }
            /* Table sorting styles */
            .table th a {
                color: inherit;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }

            .table th a i {
                margin-left: 5px;
                font-size: 14px;
            }

            .deal-modal.right .modal-dialog {
                position: fixed;
                margin: auto;
                width: 50rem;
                max-width: 50%;
                height: 100%;
                right: 0;
                top: 0;
                bottom: 0;
            }

            .deal-modal.right .modal-content {
                height: 100%;
                overflow-y: auto;
            }

            .deal-modal.right .modal-body {
                padding: 15px 15px 80px;
            }

            .mobile-deal-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }
        </style>
        <div class="row dashboard-widget-wrapper justify-content-center">
            <div class="col-md-12">
                @if (count($deals) > 0)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="search-box">
                                            <input type="text" class="form-control" placeholder="@lang('Search deals...')">
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table custom-data-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <a href="{{ route('admin.partner.deals.index', ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Deal name')
                                                            <i class="las {{ request('sort') == 'name' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('admin.partner.deals.index', ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Type')
                                                            <i class="las {{ request('sort') == 'type' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('admin.partner.deals.index', ['sort' => 'deal_stage', 'direction' => request('sort') == 'deal_stage' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Stage')
                                                            <i class="las {{ request('sort') == 'deal_stage' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('admin.partner.deals.index', ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Created')
                                                            <i class="las {{ request('sort') == 'created_at' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>@lang('Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($deals as $deal)
                                                    <tr>
                                                        <td><a href="{{ route('admin.partner.deals.show', $deal->id) }}">{{ $deal->name }}</a></td>
                                                        <td>{{ $deal->type }}</td>
                                                        <td>{{ $deal->deal_stage }}</td>
                                                        <td>{{ showDateTime($deal->created_at) }}</td>
                                                        <td>
                                                            <div class="button-group">
                                                                <a href="{{ route('admin.partner.deals.show', $deal->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="las la-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('admin.partner.deals.edit', $deal->id) }}" class="btn btn-sm btn-outline-info">
                                                                    <i class="las la-edit"></i> Edit
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

                                    <!-- Mobile List View -->
                                    <style>
                                        .mobile-list {
                                            display: none;
                                        }
                                        @media (max-width: 767px) {
                                            .table-responsive {
                                                display: none;
                                            }
                                            .mobile-list {
                                                display: block;
                                            }
                                            .mobile-deal-item {
                                                border: 1px solid #e9ecef;
                                                border-radius: 8px;
                                                padding: 15px;
                                                margin-bottom: 15px;
                                            }
                                            .mobile-deal-name {
                                                font-weight: bold;
                                                font-size: 18px;
                                            }
                                            .mobile-deal-info {
                                                margin-top: 10px;
                                            }
                                            .mobile-deal-info p {
                                                margin-bottom: 5px;
                                            }
                                            .mobile-deal-actions {
                                                margin-top: 15px;
                                                display: flex;
                                                gap: 10px;
                                            }
                                        }
                                    </style>
                                    <div class="mobile-list">
                                        @forelse($deals as $deal)
                                            <div class="mobile-deal-item">
                                                <div class="mobile-deal-header">
                                                    <div class="mobile-deal-name">
                                                        <a href="{{ route('admin.partner.deals.show', $deal->id) }}">{{ $deal->name }}</a>
                                                    </div>
                                                </div>
                                                <div class="mobile-deal-info">
                                                    <p><strong>Type:</strong> {{ $deal->type }}</p>
                                                    <p><strong>Stage:</strong> {{ $deal->deal_stage }}</p>
                                                    <p><strong>Created:</strong> {{ showDateTime($deal->created_at) }}</p>
                                                </div>
                                                <div class="mobile-deal-actions">
                                                    <a href="{{ route('admin.partner.deals.show', $deal->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="las la-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('admin.partner.deals.edit', $deal->id) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="las la-edit"></i> Edit
                                                    </a>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="alert alert-warning">No deals found.</div>
                                        @endforelse
                                    </div>

                                    @if ($deals->hasPages())
                                        <div class="card-footer py-4">
                                            {{ paginateLinks($deals) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">No deals found.</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function dashboard() {
            return {
                init() {
                    // Initialize any dashboard functionality here
                }
            };
        }
    </script>
@endpush
