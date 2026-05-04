@extends('partner.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="mb-4">
        <p class="text-muted">{{ auth()->guard('admin')->user()->name }}'s Dashboard</p>
    </div>
</div>

<div class="row mb-none-30">
    <!-- Total Deals Card -->
    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="avatar-lg bg-light-primary rounded p-2">
                            <i class="las la-handshake fs-2 text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Deals</h6>
                        <h2 class="fs-4 fw-bold">{{ $totalDeals }}</h2>
                    </div>
                </div>
                <a href="{{ route('admin.deals.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Total Offerings Card -->
    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="avatar-lg bg-light-success rounded p-2">
                            <i class="las la-file-contract fs-2 text-success"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Offerings</h6>
                        <h2 class="fs-4 fw-bold">{{ $totalOfferings }}</h2>
                    </div>
                </div>
                <a href="#" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Total Assets Card -->
    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="avatar-lg bg-light-danger rounded p-2">
                            <i class="las la-building fs-2 text-danger"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Assets</h6>
                        <h2 class="fs-4 fw-bold">{{ $totalAssets }}</h2>
                    </div>
                </div>
                <a href="#" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="avatar-lg bg-light-warning rounded p-2">
                            <i class="las la-users fs-2 text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Users</h6>
                        <h2 class="fs-4 fw-bold">{{ $totalUsers }}</h2>
                    </div>
                </div>
                <a href="#" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<!-- Investments and Distributions Section -->
<div class="row mb-none-30 mt-4">
    <!-- Investments Section -->
    <div class="col-xl-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Investments</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Total Invested -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-primary rounded p-2">
                                            <i class="las la-dollar-sign fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Invested</h6>
                                        <h5 class="mb-0">${{ number_format($totalInvested, 2, '.', ',') }} USD</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Investments -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-warning rounded p-2">
                                            <i class="las la-hourglass-half fs-3 text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Pending Investments</h6>
                                        <h5 class="mb-0">${{ number_format($pendingInvestments, 2, '.', ',') }} USD</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Deals -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-success rounded p-2">
                                            <i class="las la-check-circle fs-3 text-success"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Active Deals</h6>
                                        <h5 class="mb-0">{{ $activeDeals }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Deals -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-info rounded p-2">
                                            <i class="las la-clock fs-3 text-info"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Pending Deals</h6>
                                        <h5 class="mb-0">{{ $pendingDeals }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Distributions Section -->
    <div class="col-xl-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Distributions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Total Distributed -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-primary rounded p-2">
                                            <i class="las la-hand-holding-usd fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Total Distributed</h6>
                                        <h5 class="mb-0">${{ number_format($totalDistributed, 2, '.', ',') }} USD</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Last Distribution -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar-md bg-light-warning rounded p-2">
                                            <i class="las la-calendar fs-3 text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Last Distribution</h6>
                                        <h5 class="mb-0">{{ $lastDistribution ? $lastDistribution->format('M d, Y') : 'N/A' }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Placeholder -->
                    <div class="col-12 mt-3">
                        <div class="card bg-white border">
                            <div class="card-body p-3 text-center">
                                <p class="text-muted mb-0">Distribution Chart Coming Soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities and Deals Section -->
<div class="row mb-none-30 mt-4">
    <!-- Recent Activities Section -->
    <div class="col-xl-6 mb-30">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                @if(count($activities) > 0)
                <div class="timeline-list">
                    @foreach($activities as $activity)
                    <div class="timeline-item mb-3 pb-3 border-bottom">
                        <div class="d-flex">
                            <div class="avatar-sm bg-light-{{ $activity->type == 'investment' ? 'success' : ($activity->type == 'distribution' ? 'primary' : 'info') }} rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="las la-{{ $activity->type == 'investment' ? 'dollar-sign' : ($activity->type == 'distribution' ? 'hand-holding-usd' : 'file-alt') }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $activity->title }}</h6>
                                <p class="text-muted mb-1 small">{{ $activity->description }}</p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No recent activities found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Deals Quick Access Section -->
    <div class="col-xl-6 mb-30">
        <div class="card bg-white shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">My Deals</h5>
                <a href="{{ route('admin.deals.index') }}" class="btn btn-sm btn-primary">View All Deals</a>
            </div>
            <div class="card-body">
                @if(count($recentDeals) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Deal Name</th>
                                <th>Stage</th>
                                <th>Created</th>
                                <th>Investors</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDeals as $deal)
                            <tr>
                                <td><a href="{{ route('admin.deals.show', $deal->id) }}">{{ $deal->name }}</a></td>
                                <td>
                                    @if($deal->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                    @elseif($deal->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($deal->status == 'closed')
                                    <span class="badge bg-danger">Closed</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($deal->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $deal->created_at->format('M d, Y') }}</td>
                                <td>{{ $investorCounts[$deal->id] ?? 0 }}</td>
                                <td>
                                    <div class="button-group">
                                        <a href="{{ route('admin.deals.show', $deal->id) }}" class="btn btn-sm btn-primary"><i class="las la-eye"></i></a>
                                        <a href="{{ route('admin.deals.summary', $deal->id) }}" class="btn btn-sm btn-info"><i class="las la-chart-bar"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No deals found. <a href="{{ route('admin.deals.create') }}">Create a new deal</a></p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-lg {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-md {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-light-primary {
        background-color: rgba(63, 135, 245, 0.15);
    }
    
    .bg-light-success {
        background-color: rgba(80, 205, 137, 0.15);
    }
    
    .bg-light-danger {
        background-color: rgba(241, 85, 108, 0.15);
    }
    
    .bg-light-warning {
        background-color: rgba(255, 199, 0, 0.15);
    }
    
    .bg-light-info {
        background-color: rgba(0, 184, 217, 0.15);
    }
    
    .card {
        border-radius: 10px;
        border: none;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>
@endsection
