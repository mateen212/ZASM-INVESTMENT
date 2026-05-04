@extends('admin.layouts.app')

@php
    if(auth('admin')->user()->hasRole('partner')){
        $prefix = 'partner';
    } else { 
        $prefix = 'admin';
    }
@endphp

@section('panel')
    <div class="card">
        <div class="card-body">
            <div class="deal-summary" x-data="classSummary()" x-cloak>
                <template x-if="loading">
                    <div class="custom-loader-overlay">
                        <div class="custom-loader"></div>
                    </div>
                </template>
                <nav aria-label="breadcrumbs">
                    <ol class="breadcrumbs align-items-center">
                        <li class="breadcrumbs-item">
                            <a href="{{ route($prefix . '.dashboard') }}" class="home-icon"><i class="fas fa-home"
                                    title="Dashboard"></i></a>
                        </li>
                        <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.index') }}'">Deals
                        </li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item"
                            onclick="window.location.href='{{ route($prefix . '.deals.summary', $deal->id) }}'">
                            {{ $deal->name }}</li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item">{{ $class->equity_class_name }}</li>
                    </ol>
                </nav>
                <hr>
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <h2 class="mb-0 fw-semibold" style="font-size: 24px;">{{ $class->equity_class_name }}</h2>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" id="add-investment" onclick="initSelect2()"
                            data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            + Add Investment
                        </button>
                    </div>
                </div>
                <hr>
                <div class="container mt-4 mb-4">
                    <div class="row">
                        <!-- Class details -->
                        <div class="col-md-6">
                            <h5 class="fw-bold">Class details</h5>
                            <div class="row">
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph"># of investors</div>
                                    <div class="fw-bold">{{ $class->investments->count() ?? '--' }}</div>
                                </div>
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph">Ownership of entity</div>
                                    <div class="fw-bold">{{ $class->entity_legal_ownership ?? '--' }}</div>
                                </div>
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph">Target IRR</div>
                                    <div>{{ $class->target_irr ?? '--' }}</div>
                                </div>
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph">Preferred return</div>
                                    <div>{{ $class->preferred_return ?? '--' }}</div>
                                </div>
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph">Minimum investment</div>
                                    <div class="fw-bold">{{ $class->minimum_investment ?? '--' }}</div>
                                </div>
                                <div class="col-4 mt-3">
                                    <div class="header-paragraph">Maximum investment</div>
                                    <div>{{ $class->maximum_investment ?? '--' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Class allocated -->
                        <div class="col-md-6">
                            <h5 class="fw-bold">Class allocated</h5>
                            <div class="d-flex justify-content-between">
                                <div>0.00% of 20.00%</div>
                                <div>0.00%</div>
                            </div>
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 0%;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="20"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                @include ('admin.deals.investment_deal')
                <div class="">
                    <h3>Investments</h3>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Search Bar -->
                        <div>
                            <div class="search-bar position-relative">
                                <input type="text" name="search" id="search-investments"
                                    class="form-control form-control-sm" placeholder="Search investments..."
                                    style="padding-right: 2.5rem;" />
                                <i class="la la-search position-absolute"></i>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#manageColumnsModal">Manage columns</button>
                            <button class="btn btn-outline-secondary" onclick="exportToExcel()">Export as
                                Excel</button>
                            <button class="btn btn-outline-info" data-bs-toggle="modal"
                                data-bs-target="#filterModal">Filters <span class="badge bg-primary"
                                    id="filterCount">0</span></button>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered mt-3" id="investments-table">
                            <thead>
                                <tr>
                                    <th class="sticky-left sortable" data-sort="investor_profile">
                                        @lang('Investor name & profile')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="email_address">
                                        @lang('Email address')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="date_placed">
                                        @lang('Date placed')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="offering_name">
                                        @lang('Offering name')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="invested_amount">
                                        @lang('Invested amount')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="capital_balance">
                                        @lang('Capital balance')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="funded_amount">
                                        @lang('Funded amount')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="date_funded">
                                        @lang('Date funded')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="funds_sent_at">
                                        @lang('Funds sent at')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="percent_class_bucket">
                                        @lang('Percent of class or bucket (ownership)')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="ownership_percentage">
                                        @lang('Ownership percentage (ownership)')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="status">
                                        @lang('Status')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="document_signed_on">
                                        @lang('Document signed on')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="accrued_preferred_return">
                                        @lang('Accrued preferred return')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="unpaid_preferred_return">
                                        @lang('Unpaid preferred return')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="preferred_return_start_date">
                                        @lang('Preferred return start date')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="cash_balance">
                                        @lang('Cash balance')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="deployed_capital_balance">
                                        @lang('Deployed capital balance')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="accreditation">
                                        @lang('Accreditation')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="identify_verification">
                                        @lang('Identify verification')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="questionnaire">
                                        @lang('Questionnaire')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="class">
                                        @lang('Class')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="sponsor">
                                        @lang('Sponsor')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="primary_company_member">
                                        @lang('Primary company member')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="profile_completed">
                                        @lang('Profile completed')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="investment_tags">
                                        @lang('Investment tags')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="notes">
                                        @lang('Notes')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sortable" data-sort="distributed_amount">
                                        @lang('Distributed amount')
                                        <span class="sort-icons">
                                            <i class="fas fa-sort-up"></i>
                                            <i class="fas fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th class="sticky-right">
                                        @lang('Actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($class->investments as $investment)
                                    <tr>
                                        <td class="sticky-left">
                                            {{ $investment->investor?->investor_fname . ' ' . $investment->investor?->investor_lname }}
                                        </td>
                                        <td>{{ $investment->investor?->investor_email }}</td>
                                        <td>{{ $investment->date_placed }}</td>
                                        <td>{{ $investment->offering?->name }}</td>
                                        <td>{{ $investment->investment_amount }}</td>
                                        {{--  find funded investment  --}}
                                        <td>{{ $investment->offering?->offering_size }}</td>
                                        <td>{{ $investment->funds ?? '' }}</td>
                                        <td>{{ $investment->funds ?? '' }}</td>
                                        <td>{{ $investment->funds ?? '' }}</td>
                                        <td>{{ $investment->op_ownership }}</td>
                                        <td>{{ $investment->op_ownership }}</td>
                                        <td>{{ $investment->investment_status_text ?? 'In-progress' }}</td>
                                        <td>{{ $investment->documents ?? 'N/A' }}</td>
                                        <td>{{ $investment->class->preferred_return_type ?? 'N/A' }}</td>
                                        <td>1235</td>
                                        <td>{{ $investment->class->pref_return_start_date ?? 'N/A' }}</td>
                                        <td>1238</td>
                                        <td>1239</td>
                                        <td>1240</td>
                                        <td>1241</td>
                                        <td>{{ $investment->primary_sponsor }}</td>
                                        <td>43</td>
                                        <td>1245</td>
                                        <td>1246</td>
                                        <td>1247</td>
                                        <td>1248</td>
                                        <td>1249</td>
                                        <td>1249</td>
                                        <td>
                                            <span role="button" title="delete"
                                                onclick="confirmInvestmentDelete(this, '{{ route($prefix . '.investments.deleteInvestment', ['id' => $investment->id]) }}')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                <template x-if="investments.length < 0">
                                    <tr>
                                        <td colspan="29" class="text-center">No investment available</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="manageColumnsModal" tabindex="-1"
                        aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="manageColumnsForm">
                                        <div class="row">
                                            <div class="col-6">
                                                <label><input type="checkbox" class="column-toggle" data-column="1"
                                                        checked disabled> Investor name & profile</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="2"
                                                        checked>
                                                    Email address</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="3"
                                                        checked>
                                                    Offering name</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="4"
                                                        checked>
                                                    Date placed</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="5"
                                                        checked>
                                                    Invested amount</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="6"
                                                        checked>
                                                    Capital balance</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="7"
                                                        checked>
                                                    Funded amount</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="8"
                                                        checked>
                                                    Date funded</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="9"
                                                        checked>
                                                    Funds sent at</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="10"
                                                        checked>
                                                    Percent of class or bucket (ownership)</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="11"
                                                        checked>
                                                    Ownership percentage (ownership)</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="12"
                                                        checked>
                                                    Status</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="13"
                                                        checked>
                                                    Document signed on</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="14"
                                                        checked>
                                                    Accrued preferred return</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="15"
                                                        checked>
                                                    Unpaid preferred return</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="16"
                                                        checked>
                                                    Preferred return start date</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="17"
                                                        checked>
                                                    Cash balance</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="18"
                                                        checked>
                                                    Deployed capital balance</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="19"
                                                        checked>
                                                    Accreditation</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="20"
                                                        checked>
                                                    Identify verification</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="21"
                                                        checked>
                                                    Questionnaire</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="22"
                                                        checked>
                                                    Class</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="23"
                                                        checked>
                                                    Sponsor</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="24"
                                                        checked>
                                                    Primary company member</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="25"
                                                        checked>
                                                    Profile completed</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="26"
                                                        checked>
                                                    Investment tags</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="27"
                                                        checked>
                                                    Notes</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="28"
                                                        checked>
                                                    Distributed amount</label><br>
                                                <label><input type="checkbox" class="column-toggle" data-column="29"
                                                        checked disabled> Actions</label><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
@push('script')
    <script>
        var csrf = '{{ csrf_token() }}';

        function classSummary() {
            return {
                investments: @json($deal->investments),

            }
        }
        function confirmInvestmentDelete(element, url) {
            Swal.fire({
                title: 'Delete Investment',
                text: "Are you sure you want to delete this Investment? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteInvestment(element, url);
                }
            });
        }

        function deleteInvestment(element, url) {
            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Remove row from table without reloading
                        const row = element.closest('tr');
                        row.remove();

                        Swal.fire(
                            'Deleted!',
                            'Investment has been deleted successfully.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the Investment.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the Investment.',
                        'error'
                    );
                });
        }
    </script>
    {{--  add bootstrap 5 link  --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"></script>
    <!-- Bootstrap JS (Include jQuery & Popper.js if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/app.css') }}" rel="stylesheet" />
    <style>
        .header-paragraph {
            font-size: 12px;
            color: #6c757d;
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
    </style>
    <style>
        /* Sorting styles */
        .sortable {
            cursor: pointer;
            position: relative;
            padding-right: 20px !important;

            /* Space for icons */
        }

        .sort-icons {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            line-height: 0;
            opacity: 0.3;
            gap: 3px;
            /* Add space between arrows */
        }

        .sort-icons i {
            font-size: 0.75rem;
            line-height: 0.5;
        }

        .sort-icons i.fa-sort-up {
            margin-bottom: 0;
            /* Remove previous margin */
        }

        .sort-icons i.fa-sort-down {
            margin-top: 0;
            /* Remove previous margin */
        }

        /* Active sort states */
        .sortable.asc .sort-icons .fa-sort-up,
        .sortable.desc .sort-icons .fa-sort-down {
            opacity: 1;
            color: #0d6efd;
        }

        .sortable:hover .sort-icons {
            opacity: 0.8;
        }

        .search-bar i {
            right: 10px;
            top: 50%;
            transform:
                translateY(-50%) scaleX(-1);
            pointer-events: none;
        }
    </style>
@endpush
@section('panel')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function initSelect2() {
            $('#select_investment_tags').select2({
                tags: true,
                insertTag: function(data, tag) {
                    data.push(tag);
                },
                dropdownParent: $('#addInvestmentModal'),
                tokenSeparators: [',', ' ']
            });
            $('#select_investor_tags').select2({
                tags: true,
                insertTag: function(data, tag) {
                    data.push(tag);
                },
                dropdownParent: $('#addInvestorModal'),
                tokenSeparators: [',', ' ']
            });
        }
    </script>
    <script>
        function exportToExcel() {
            const table = document.getElementById("investments-table");
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Investments"
            });
            XLSX.writeFile(workbook, "Investments.xlsx");
        }
        // Apply Filters
        function applyFilters() {
            const status = document.getElementById("statusFilter").value.toLowerCase();
            const rows = document.querySelectorAll("#investments-table tbody tr");

            rows.forEach(row => {
                const statusCell = row.cells[11].textContent.trim().toLowerCase();
                row.style.display = status === "all" || statusCell === status ? "" : "none";
            });
        }

        // Search Investments
        document.getElementById("search-investments").addEventListener("input", function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll("#investments-table tbody tr");

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? "" : "none";
            });
        });

        // Column Toggle
        document.querySelectorAll('.column-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const columnIndex = parseInt(this.dataset.column) - 1;
                const columns = document.querySelectorAll(
                    `#investments-table tr th:nth-child(${columnIndex + 1}), #investments-table tr td:nth-child(${columnIndex + 1})`
                );

                columns.forEach(cell => {
                    cell.style.display = this.checked ? "" : "none";
                });
            });
        });

        
    </script>
@endsection
