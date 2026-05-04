@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mr-auto mb-4 breadcrumb-dashboard">
        <h2 class="mb-4">Investor Dashboard</h2>

        <!-- Summary cards -->
        <div id="card-header" class="d-flex g-3 mb-4">
            <div class="">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total in-progress</h6>
                        <p class="card-text fw-bold">$234</p>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total invested</h6>
                        <p class="card-text fw-bold">$0</p>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total distributed</h6>
                        <p class="card-text fw-bold">$0</p>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted"># of distributions</h6>
                        <p class="card-text fw-bold">0</p>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted"># of deals</h6>
                        <p class="card-text fw-bold">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsible Sections -->
    <div class="accordion" id="dashboardAccordion">
        <!-- Invested Capital -->
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="headingInvestedCapital">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseInvestedCapital" aria-expanded="false" aria-controls="collapseInvestedCapital">
                    Invested Capital
                </button>
            </h2>
            <div id="collapseInvestedCapital" class="accordion-collapse collapse" aria-labelledby="headingInvestedCapital"
                data-bs-parent="#dashboardAccordion">
                <div class="accordion-body text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                        <!-- Placeholder chart -->
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 100px; height: 100px; background: rgba(0, 123, 255, 0.1);">
                            <div style="width: 70px; height: 70px; background: rgba(0, 123, 255, 0.2); border-radius: 50%;">
                            </div>
                        </div>
                        <!-- Text Content -->
                        <h6 class="mt-3 text-muted">No investment statistics yet</h6>
                        <p class="text-muted small">Invest in your first deal to start tracking investment statistics.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="headingInvestmentOpportunities">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseInvestmentOpportunities" aria-expanded="false"
                    aria-controls="collapseInvestmentOpportunities">
                    Investment Opportunities
                </button>
            </h2>
            <div id="collapseInvestmentOpportunities" class="accordion-collapse collapse"
                aria-labelledby="headingInvestmentOpportunities" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                        <!-- Placeholder chart -->
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 100px; height: 100px; background: rgba(0, 123, 255, 0.1);">
                            <div style="width: 70px; height: 70px; background: rgba(0, 123, 255, 0.2); border-radius: 50%;">
                            </div>
                        </div> <!-- Text Content -->
                        <h6 class="mt-3 text-muted">No current investment opportunities</h6>
                        <p class="text-muted small">Once opportunities become available from your sponsors, they will be
                            visible here.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- In-progress Investments -->
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="headingInProgressInvestments">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseInProgressInvestments" aria-expanded="false"
                    aria-controls="collapseInProgressInvestments">
                    In-progress Investments
                </button>
            </h2>
            <div id="collapseInProgressInvestments" class="accordion-collapse collapse"
                aria-labelledby="headingInProgressInvestments" data-bs-parent="#dashboardAccordion">
                <div class="accordion-body">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Offering name</th>
                                <th>Sponsors</th>
                                <th>Amount</th>
                                <th>Class</th>
                                <th>Profile</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="headingRecentActivity">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseRecentActivity" aria-expanded="false" aria-controls="collapseRecentActivity">
                    Recent Activity
                </button>
            </h2>
            <div id="collapseRecentActivity" class="accordion-collapse collapse" aria-labelledby="headingRecentActivity"
                data-bs-parent="#dashboardAccordion">
                <div class="accordion-body text-center">
                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                        <!-- Placeholder chart -->
                        {{--  <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 100px; height: 100px; background: rgba(0, 123, 255, 0.1);">
                            <div
                                style="width: 70px; height: 70px; background: rgba(0, 123, 255, 0.2); border-radius: 50%;">
                            </div>
                        </div> <!-- Text Content -->  --}}
                        <h6 class="mt-3 text-muted">No recent activity yet</h6>
                        <p class="text-muted small">See activity here once you start investing.</p>
                        <p class="text-muted small">Dec 10, 2024 - Your next deal</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@push('script')
 <style>
    .row .header-box{
        white-space:nowrap;
        
    }
    #card-header{
        justify-content:space-between;
    }
 </style>
@endpush