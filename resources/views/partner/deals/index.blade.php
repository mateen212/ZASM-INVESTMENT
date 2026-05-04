@extends('partner.layouts.app')
@section('panel')
    <div class="card card-body" x-data="dashboard()" x-cloak>
        <div class="d-flex justify-content-between pb-4">
            <h1>My Deals</h1>
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

            .mobile-deal-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            /* Mobile list view styles */
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
                    font-size: 1.1rem;
                }

                .mobile-deal-stage {
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 0.8rem;
                    background-color: #f8f9fa;
                }

                .mobile-deal-info {
                    margin-top: 10px;
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                }

                .mobile-deal-action {
                    margin-top: 15px;
                    text-align: right;
                }
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
                                            <form action="{{ route('partner.deals.index') }}" method="GET">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="@lang('Search deals...')" value="{{ request('search') }}">
                                                    <button class="btn btn-primary" type="submit">Search</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table custom-data-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <a href="{{ route('partner.deals.index', ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Deal name')
                                                            <i class="las {{ request('sort') == 'name' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('partner.deals.index', ['sort' => 'deal_stage', 'direction' => request('sort') == 'deal_stage' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Stage')
                                                            <i class="las {{ request('sort') == 'deal_stage' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('partner.deals.index', ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Created')
                                                            <i class="las {{ request('sort') == 'created_at' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a href="{{ route('partner.deals.index', ['sort' => 'investors', 'direction' => request('sort') == 'investors' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Investors')
                                                            <i class="las {{ request('sort') == 'investors' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>@lang('Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($deals as $deal)
                                                    <tr>
                                                        <td><a href="{{ route('partner.deals.show', $deal->id) }}">{{ $deal->name }}</a></td>
                                                        <td>
                                                            <span class="badge badge-{{ $deal->deal_stage == 'active' ? 'success' : ($deal->deal_stage == 'pending' ? 'warning' : 'info') }}">
                                                                {{ ucfirst($deal->deal_stage) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ showDateTime($deal->created_at) }}</td>
                                                        <td>{{ $investorCounts[$deal->id] ?? 0 }}</td>
                                                        <td>
                                                            <a href="{{ route('partner.deals.show', $deal->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="las la-eye"></i> @lang('View')
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-muted text-center" colspan="5">@lang('No deals found')</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Mobile List View -->
                                    <div class="mobile-list">
                                        @forelse($deals as $deal)
                                            <div class="mobile-deal-item">
                                                <div class="mobile-deal-header">
                                                    <div class="mobile-deal-name">{{ $deal->name }}</div>
                                                    <div class="mobile-deal-stage badge badge-{{ $deal->deal_stage == 'active' ? 'success' : ($deal->deal_stage == 'pending' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($deal->deal_stage) }}
                                                    </div>
                                                </div>
                                                <div class="mobile-deal-info">
                                                    <div>
                                                        <strong>Created:</strong> {{ showDateTime($deal->created_at) }}
                                                    </div>
                                                    <div>
                                                        <strong>Investors:</strong> {{ $investorCounts[$deal->id] ?? 0 }}
                                                    </div>
                                                </div>
                                                <div class="mobile-deal-action">
                                                    <a href="{{ route('partner.deals.show', $deal->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="las la-eye"></i> @lang('View')
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
                // Any Alpine.js data or methods can be added here if needed
            };
        }
    </script>
@endpush
