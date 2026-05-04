@extends('admin.layouts.app')
@section('panel')
    @php
        if (auth('admin')->user()->hasRole('partner')) {
            $prefix = 'partner';
        } else {
            $prefix = 'admin';
        }
    @endphp
    <div class="card card-body" x-data="dashboard()" x-cloak>
        <div class="d-flex justify-content-between pb-4">
            <h1>All Deals</h1>
            <button type="button" class="btn btn_primary" data-bs-toggle="modal" data-bs-target="#addDealModal">Add Deal <span
                    class="ms-4 text-white fw-bold">+</span></button>
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

            .step-progress {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }

            .step-progress .step {
                flex: 1;
                text-align: center;
                position: relative;
            }

            .step-progress .step::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 2px;
                background-color: #e9ecef;
                z-index: 1;
            }

            .step-progress .step.active::before {
                background-color: #007bff;
            }

            .step-progress .step .step-number {
                position: relative;
                z-index: 2;
                background-color: #fff;
                border: 2px solid #e9ecef;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                line-height: 26px;
                display: inline-block;
            }

            .step-progress .step.active .step-number {
                border-color: #007bff;
                color: #007bff;
            }

            .file-uploader .drop-zone {
                border: 2px dashed #007bff;
                padding: 20px;
                text-align: center;
                cursor: pointer;
            }

            .file-uploader .drop-zone.drag-over {
                background-color: #e9ecef;
            }

            .file-uploader .file-list .file-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 5px 0;
            }

            .table-responsive {
                margin: 0;
                padding: 0;
            }

            .custom-data-table {
                width: 100%;
                font-size: 14px;
            }

            .custom-data-table th {
                font-weight: 500;
                padding: 12px 10px;
                text-align: left;
                white-space: nowrap;
                border-bottom: 1px solid #eee;
            }

            .custom-data-table td {
                padding: 15px 10px;
                white-space: normal;
                word-break: break-word;
                border-bottom: 1px solid #eee;
            }

            .custom-data-table th a {
                color: #666;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .custom-data-table th a i {
                font-size: 14px;
            }

            .mobile-list {
                display: none;
                margin-top: 20px;
            }

            .mobile-deal-item {
                border-bottom: 1px solid #eee;
                padding: 15px 0;
            }

            .mobile-deal-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .mobile-deal-name {
                color: #0d6efd;
                font-weight: 500;
            }

            .mobile-deal-info {
                display: grid;
                grid-template-columns: auto auto;
                gap: 10px;
                font-size: 14px;
            }

            .mobile-deal-label {
                color: #666;
            }

            .mobile-deal-value {
                text-align: right;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .table-responsive {
                    display: none;
                }

                .mobile-list {
                    display: block;
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
                                            <input type="text" class="form-control" placeholder="@lang('Search deals...')">
                                        </div>
                                        <a href="#" class="btn btn-outline-primary px-4">@lang('Export all deals')</a>
                                    </div>

                                    <!-- Desktop Table View -->
                                    <div class="table-responsive">
                                        <table class="table custom-data-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Deal name')
                                                            <i
                                                                class="las {{ request('sort') == 'name' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'deal_stage', 'direction' => request('sort') == 'deal_stage' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Deal stage')
                                                            <i
                                                                class="las {{ request('sort') == 'deal_stage' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'total_in_progress', 'direction' => request('sort') == 'total_in_progress' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Total in progress')
                                                            <i
                                                                class="las {{ request('sort') == 'total_in_progress' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'total_accepted', 'direction' => request('sort') == 'total_accepted' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Total accepted')
                                                            <i
                                                                class="las {{ request('sort') == 'total_accepted' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'raise_target', 'direction' => request('sort') == 'raise_target' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Raise target')
                                                            <i
                                                                class="las {{ request('sort') == 'raise_target' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'distributions', 'direction' => request('sort') == 'distributions' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Distributions')
                                                            <i
                                                                class="las {{ request('sort') == 'distributions' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'investors', 'direction' => request('sort') == 'investors' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Investors')
                                                            <i
                                                                class="las {{ request('sort') == 'investors' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>
                                                        <a
                                                            href="{{ route($prefix . '.deals.index', ['sort' => 'close_date', 'direction' => request('sort') == 'close_date' && request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                            @lang('Close date')
                                                            <i
                                                                class="las {{ request('sort') == 'close_date' ? (request('direction') == 'asc' ? 'la-arrow-up' : 'la-arrow-down') : 'la-arrows-alt-v' }}"></i>
                                                        </a>
                                                    </th>
                                                    <th>@lang('Action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($deals as $deal)
                                                    <tr>
                                                        <td><a
                                                                href="{{ route($prefix . '.deals.summary', $deal->id) }}">{{ $deal->name }}</a>
                                                        </td>
                                                        <td>{{ $deal->deal_stage }}</td>
                                                        <td>${{ number_format($deal->total_in_progress, 2) }}
                                                            ({{ number_format($deal->progress_percentage, 2) }}%)
                                                        </td>
                                                        <td>{{ $deal->total_accepted }}</td>
                                                        <td>${{ number_format($deal->raise_target, 0) }}</td>
                                                        <td>
                                                            @foreach ($deal->distributions as $distribution)
                                                                <div>
                                                                    {{ $distribution->amount }}<br>

                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td>N/A</td>

                                                        <td>{{ showDateTime($deal->close_date) }}</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn p-0" type="button"
                                                                    data-bs-toggle="dropdown">
                                                                    <i class="las la-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route($prefix . '.deals.summary', $deal->id) }}">
                                                                            <i class="las la-eye me-2"></i> View
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <button class="dropdown-item text-danger"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#deleteConfirmModal"
                                                                            onclick="setDeleteData({{ $deal->id }}, '{{ $deal->name }}')">
                                                                            <i class="las la-trash me-2"></i> Delete
                                                                        </button>
                                                                    </li>
                                                                    @if ($deal->achsettings && $deal->achsettings->entity_name && $deal->achsettings->verify_confirmation === 'review')
                                                                        <li>
                                                                            <button
                                                                                class="dropdown-item  confirmation-entity-btn"
                                                                                onclick="setDealId({{ $deal->id }}, {{ $deal }})"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#confirmDealEntityModal"
                                                                                data-deal-id="{{ $deal->id }}">
                                                                                <i class="las la-check me-2"></i>
                                                                                Confirm Deal Entity
                                                                            </button>
                                                                        </li>
                                                                    @endif



                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-muted text-center" colspan="100%">
                                                            {{ __($emptyMessage ?? 'No deals found') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Mobile List View -->
                                    <style>
                                        .mobile-list {
                                            display: none;
                                            margin-top: 20px;
                                        }

                                        .mobile-deal-item {
                                            border-bottom: 1px solid #eee;
                                            padding: 15px 0;
                                        }

                                        .mobile-deal-header {
                                            display: flex;
                                            justify-content: space-between;
                                            margin-bottom: 10px;
                                        }

                                        .mobile-deal-name {
                                            color: #0d6efd;
                                            font-weight: 500;
                                        }

                                        .mobile-deal-info {
                                            display: grid;
                                            grid-template-columns: auto auto;
                                            gap: 10px;
                                            font-size: 14px;
                                        }

                                        .mobile-deal-label {
                                            color: #666;
                                        }

                                        .mobile-deal-value {
                                            text-align: right;
                                            font-weight: 500;
                                        }

                                        @media (max-width: 768px) {
                                            .table-responsive {
                                                display: none;
                                            }

                                            .mobile-list {
                                                display: block;
                                            }
                                        }
                                    </style>

                                    <div class="mobile-list">
                                        @forelse($deals as $deal)
                                            <div class="mobile-deal-item">
                                                <div class="mobile-deal-header">
                                                    <div class="mobile-deal-name">
                                                        <a
                                                            href="{{ route($prefix . '.deals.summary', $deal->id) }}">{{ $deal->name }}</a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                            <i class="las la-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route($prefix . '.deals.summary', $deal->id) }}">
                                                                    <i class="las la-eye me-2"></i> View
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item text-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteConfirmModal"
                                                                    onclick="setDeleteData({{ $deal->id }}, '{{ $deal->name }}')">
                                                                    <i class="las la-trash me-2"></i> Delete
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="mobile-deal-info">
                                                    <div class="mobile-deal-label">Deal stage</div>
                                                    <div class="mobile-deal-value">{{ $deal->deal_stage }}</div>

                                                    <div class="mobile-deal-label">Total in progress</div>
                                                    <div class="mobile-deal-value">
                                                        ${{ number_format($deal->total_in_progress, 2) }}
                                                        ({{ number_format($deal->progress_percentage, 2) }}%)
                                                    </div>

                                                    <div class="mobile-deal-label">Total accepted</div>
                                                    <div class="mobile-deal-value">{{ $deal->total_accepted }}</div>

                                                    <div class="mobile-deal-label">Raise target</div>
                                                    <div class="mobile-deal-value">
                                                        ${{ number_format($deal->raise_target, 0) }}</div>

                                                    <div class="mobile-deal-label">Distributions</div>
                                                    <div class="mobile-deal-value">{{ $deal->distributions }}</div>

                                                    <div class="mobile-deal-label">Investors</div>
                                                    <div class="mobile-deal-value"></div>

                                                    <div class="mobile-deal-label">Close date</div>
                                                    <div class="mobile-deal-value">{{ showDateTime($deal->close_date) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted text-center py-3">
                                                {{ __($emptyMessage ?? 'No deals found') }}</div>
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

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Delete Deal')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>@lang('Are you sure you want to delete') <span class="fw-bold deal-name"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="button" onclick="deleteDeal()" class="btn btn--danger">@lang('Delete')</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDealEntityModal" tabindex="-1" aria-labelledby="confirmDealEntityModalLabel"
            aria-hidden="true" x-data="dashboard()">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header bg-primary text-white justify-content-between">
                        <h5 class="modal-title" id="confirmDealEntityModalLabel">@lang('Confirm Deal Entity')</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body entity-confirm-model-body p-4">
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="las la-exclamation-triangle me-2"></i>
                            <div>
                                <strong class="text-danger">Warning:</strong> Are you sure you want to confirm the entity
                                for this deal?
                            </div>
                        </div>
                        <ul class="list-group list-group-flush mb-3 shadow-sm">
                            <li class="list-group-item d-flex justify-content-between"><strong>Deal Name:</strong> <span
                                    x-text="ConfirmationForm.deal_name || 'N/A'"></span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Entity Name:</strong> <span
                                    x-text="ConfirmationForm.entity_name || 'N/A'"></span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Entity Type:</strong> <span
                                    x-text="ConfirmationForm.entity_type || 'N/A'"></span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Address:</strong> <span
                                    x-text="ConfirmationForm.address || 'N/A'"></span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Controller Address:</strong>
                                <span x-text="ConfirmationForm.controller_address || 'N/A'"></span></li>
                            <li class="list-group-item d-flex justify-content-between"><strong>State Registration:</strong>
                                <span x-text="ConfirmationForm.state_registration || 'N/A'"></span></li>
                        </ul>
                        <div class="error-message text-danger mt-3" x-text="errors.confirmation"></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-dark"
                            data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="button" class="btn btn-success position-relative" x-bind:disabled="loading"
                            @click="submitConfirmationForm(ConfirmationForm)">
                            <span class="button-text text-white" x-show="!loading && !errors.confirmation"><i
                                    class="las la-check"></i> Approve Entity</span>
                            <span class="button-loading" x-show="loading">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                            <span class="button-success d-none"
                                x-show="errors.confirmation === 'Entity confirmed successfully'"><i
                                    class="las la-check-circle"></i> Approved!</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade custom--modal" id="detailModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Investment Details')</h5>
                        <button class="close-btn" type="button" data-bs-dismiss="modal">
                            <i class="las fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-form__header">
                            <ul class="list-group userData mb-2 list-group-flush"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Structure -->
        <div class="deal-modal modal right fade" id="addDealModal" tabindex="-1" aria-labelledby="addDealModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content px-2">
                    <div class="modal-header row">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="stepper-wrapper">
                            <div class="stepper-item"
                                :class="{ 'active': currentStep === 'deal', 'completed': completedSteps.includes('deal') }">
                                <div class="step-counter">1</div>
                                <div class="step-name">Deal</div>
                            </div>
                            <div class="stepper-item"
                                :class="{
                                    'active': currentStep === 'assets',
                                    'completed': completedSteps.includes(
                                        'assets')
                                }">
                                <div class="step-counter">2</div>
                                <div class="step-name">Assets</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        {{-- Deal Form Body --}}
                        <div x-show="currentStep === 'deal'">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Deal Name*</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    x-on:input="dealErrors.name = ''" x-model="dealForm.name" required>
                                <span x-show="dealErrors.name" x-text="dealErrors.name" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Deal Type</label>
                                <select id="type" name="type" class="form-select" x-model="dealForm.type">
                                    <option value="Direct syndication">Direct syndication (most common)</option>
                                    <option value="Fund syndication">Fund syndication</option>
                                    <option value="Customized fund of funds">Customized fund of funds</option>
                                    <option value="SPV">SPV Fund</option>
                                    <option value="SPV">Flex fund</option>
                                    <option value="Angel Investment">Angel Investment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Deal Stage*</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="raising" name="deal_stage"
                                            value="Raising capital" x-on:input="dealErrors.deal_stage = ''"
                                            x-model="dealForm.deal_stage">
                                        <label class="form-check-label" for="raising">Raising capital</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="asset" name="deal_stage"
                                            value="Asset managing" x-on:input="dealErrors.deal_stage = ''"
                                            x-model="dealForm.deal_stage">
                                        <label class="form-check-label" for="asset">Asset managing</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="liquidated" name="deal_stage"
                                            value="Liquidated" x-on:input="dealErrors.deal_stage = ''"
                                            x-model="dealForm.deal_stage">
                                        <label class="form-check-label" for="liquidated">Liquidated</label>
                                    </div>
                                </div>
                                <span x-show="dealErrors.deal_stage" x-text="dealErrors.deal_stage"
                                    class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="sec_type" class="form-label">SEC Type*</label>
                                {{-- <input type="text" id="sec_type" name="sec_type" class="form-control" required> --}}
                                <select id="sec_type" name="sec_type" class="form-select"
                                    x-on:input="dealErrors.sec_type = ''" x-model="dealForm.sec_type" required>
                                    >
                                    <option value="">Select SEC Type</option>
                                    <option value="506(b)">506(b)</option>
                                    <option value="506(c)">506(c)</option>
                                    <option value="Intra-state">Intra-state</option>
                                    <option value="Joint Venture">Joint Venture</option>
                                    <option value="Lending">Lending</option>
                                    <option value="Reg A+">Reg A+</option>
                                    <option value="Reg CF">Reg CF</option>
                                    <option value="Reg D">Reg D</option>
                                    <option value="Reg S">Reg S</option>
                                    <option value="Regulation A">Regulation A</option>
                                </select>
                                <span x-show="dealErrors.sec_type" x-text="dealErrors.sec_type"
                                    class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label for="close_date" class="form-label">Close Date</label>
                                <input type="date" id="close_date" name="close_date" class="form-control"
                                    x-model="dealForm.close_date">
                            </div>
                            <div class="mb-3">
                                <label for="owning_entity_name" class="form-label">Owning Entity Name*</label>
                                <input type="text" id="owning_entity_name" name="owning_entity_name"
                                    class="form-control" x-on:input="dealErrors.owning_entity_name = ''"
                                    x-model="dealForm.owning_entity_name" required>
                                <span x-show="dealErrors.owning_entity_name" x-text="dealErrors.owning_entity_name"
                                    class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label>Funds must be received before GP countersigns</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="funds_yes"
                                            name="funds_received_before_gp_countersigns" value="1"
                                            x-model="dealForm.funds_received_before_gp_countersigns"
                                            :disabled="dealForm.send_funding_instructions_after_gp_countersigns == '1'">
                                        <label class="form-check-label" for="funds_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="funds_no"
                                            name="funds_received_before_gp_countersigns" value="0"
                                            x-model="dealForm.funds_received_before_gp_countersigns"
                                            :disabled="dealForm.send_funding_instructions_after_gp_countersigns == '1'">
                                        <label class="form-check-label" for="funds_no">No (most common)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Automatically send funding instructions after GP countersigns</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="instructions_yes"
                                            name="send_funding_instructions_after_gp_countersigns" value="1"
                                            x-model="dealForm.send_funding_instructions_after_gp_countersigns"
                                            :disabled="dealForm.funds_received_before_gp_countersigns == '1'">
                                        <label class="form-check-label" for="instructions_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="instructions_no"
                                            name="send_funding_instructions_after_gp_countersigns" value="0"
                                            x-model="dealForm.send_funding_instructions_after_gp_countersigns"
                                            :disabled="dealForm.funds_received_before_gp_countersigns == '1'">
                                        <label class="form-check-label" for="instructions_no">No (most common)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2"
                                    data-bs-dismiss="modal">Cancel</button>
                                <span class="btn btn-primary deal-save" @click="submitDealForm(dealForm)"
                                    style="width:100px;">Next</span>
                            </div>
                        </div>
                        {{-- Assets Form Body --}}
                        <div x-show="currentStep === 'assets'">
                            <div x-show="!assetList">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Property Name*</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        x-model="assetForm.name" required>
                                    <span x-show="assetErrors.name" x-text="assetErrors.name" class="text-danger"></span>
                                </div>
                                {{-- Address --}}
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address*</label>
                                    <input type="text" id="address" name="address" class="form-control"
                                        x-model="assetForm.address" required>
                                </div>
                                <div class="row">
                                    {{-- City --}}
                                    <div class="mb-3 col-6">
                                        <label for="city" class="form-label">City*</label>
                                        <input type="text" id="city" name="city" class="form-control"
                                            x-model="assetForm.city" required>
                                    </div>
                                    {{-- State --}}
                                    <div class="mb-3 col-6">
                                        <label for="state" class="form-label">State*</label>
                                        <input type="text" id="state" name="state" class="form-control"
                                            x-model="assetForm.state" required>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- Zip --}}
                                    <div class="mb-3 col-6">
                                        <label for="zip" class="form-label">Zip*</label>
                                        <input type="text" id="zip" name="zip" class="form-control"
                                            x-model="assetForm.zip" required>
                                    </div>
                                    {{-- Country --}}
                                    <div class="mb-3 col-6">
                                        <label for="country" class="form-label">Country*</label>
                                        <input type="text" id="country" name="country" class="form-control"
                                            x-model="assetForm.country" required>
                                    </div>
                                </div>
                                {{-- Property Type --}}
                                <div class="mb-3">
                                    <label for="property_type" class="form-label mb-2">Property Type*</label>
                                    <select id="property_type" name="property_type" class="form-select"
                                        x-model="assetForm.property_type" required>
                                        <option value="">Select Property Type</option>
                                        <option value="Angel Investment">Angel Investment</option>
                                        <option value="ATM">ATM</option>
                                        <option value="Build to rent">Build to rent</option>
                                        <option value="Car Wash">Car Wash</option>
                                        <option value="Crypto">Crypto</option>
                                        <option value="Franchise">Franchise</option>
                                        <option value="Ground up development">Ground up development</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Hedge Fund">Hedge Fund</option>
                                        <option value="Hospitality">Hospitality</option>
                                        <option value="Industrial">Industrial</option>
                                        <option value="Land">Land</option>
                                        <option value="Logistics">Logistics</option>
                                        <option value="Mixed use">Mixed use</option>
                                        <option value="Mobile home park">Mobile home park</option>
                                        <option value="Multifamily">Multifamily</option>
                                        <option value="Office">Office</option>
                                        <option value="Oil and gas">Oil and gas</option>
                                        <option value="Private credit">Private credit</option>
                                        <option value="Retail">Retail</option>
                                        <option value="RV park">RV park</option>
                                        <option value="Senior living">Senior living</option>
                                        <option value="elf-storage">Self-storage</option>
                                        <option value="Single family">Single family</option>
                                        <option value="Start-up">Start-up</option>
                                        <option value="Stocks">Stocks</option>
                                        <option value="Trucking">Trucking</option>
                                        <option value="Vacation rental">Vacation rental</option>
                                        <option value="other">other</option>
                                    </select>
                                </div>
                                {{-- Property Class --}}
                                <div class="mb-3">
                                    <label for="property_class" class="form-label mb-2">Property Class*</label>
                                    <select id="property_class" name="property_class" class="form-select"
                                        x-model="assetForm.property_class" required>
                                        <option value="">Select Property Class</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                                {{-- Number of Units --}}
                                <div class="mb-3">
                                    <label for="number_of_units" class="form-label mb-2">Number of Units*</label>
                                    <input type="number" id="number_of_units" name="number_of_units"
                                        class="form-control" x-model="assetForm.number_of_units" required>
                                </div>
                                {{-- Type of Units --}}
                                <div class="mb-3">
                                    <label for="type_of_units" class="form-label mb-2">Type of Units*</label>
                                    <select id="type_of_units" name="type_of_units" class="form-select"
                                        x-model="assetForm.type_of_units" required>
                                        <option value="Square Feet">Square Feet</option>
                                        <option value="Units">Units</option>
                                        <option value="Rooms">Rooms</option>
                                        <option value="Beds">Beds</option>
                                        <option value="Parking Spaces">Parking Spaces</option>
                                        <option value="Pads">Pads</option>
                                        <option value="Acres">Acres</option>
                                        <option value="Wells">Wells</option>
                                        <option value="Properties">Properties</option>
                                        <option value="Contracts">Contracts</option>
                                        <option value="Lots">Lots</option>
                                    </select>
                                </div>
                                <div class="row">
                                    {{-- Acquisition Date --}}
                                    <div class="mb-3 col-6">
                                        <label for="acquisition_date" class="form-label mb-2">Acquisition Date*</label>
                                        <input type="date" id="acquisition_date" name="acquisition_date"
                                            class="form-control" x-model="assetForm.acquisition_date" required>
                                    </div>
                                    {{-- Acquisition Price --}}
                                    <div class="mb-3 col-6">
                                        <label for="acquisition_price" class="form-label mb-2">Acquisition Price*
                                            ($)</label>
                                        <input type="text" x-on:input="moneyFormat($el)" id="acquisition_price"
                                            name="acquisition_price" class="form-control"
                                            x-model="assetForm.acquisition_price" required>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- Exit Date --}}
                                    <div class="mb-3 col-6">
                                        <label for="exit_date" class="form-label mb-2">Exit Date*</label>
                                        <input type="date" id="exit_date" name="exit_date" class="form-control"
                                            x-model="assetForm.exit_date" required>
                                    </div>
                                    {{-- Exit Price --}}
                                    <div class="mb-3 col-6">
                                        <label for="exit_price" class="form-label mb-2">Exit Price* ($)</label>
                                        <input type="text" x-on:input="moneyFormat($el)" id="exit_price"
                                            name="exit_price" class="form-control" x-model="assetForm.exit_price"
                                            required>
                                    </div>
                                </div>
                                {{-- Year Built --}}
                                <div class="mb-3">
                                    <label for="year_built" class="form-label mb-2">Year Built*</label>
                                    <input type="number" id="year_built" name="year_built" class="form-control"
                                        x-model="assetForm.year_built" required>
                                </div>

                                {{-- Upload Images --}}

                                <div class="mb-3">
                                    <label for="property_images" class="form-label">Property Images</label>
                                    <div class="file-uploader">
                                        <input type="file" id="property_images" name="property_images[]"
                                            class="form-control" accept="image/*" multiple @change="handleFiles($event)"
                                            hidden>
                                        <div class="drop-zone" @drop.prevent="handleDrop($event)"
                                            @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                                            :class="{ 'drag-over': dragOver }">
                                            <p class="drop-zone-text">Drag & drop files here or click to upload</p>
                                            <button type="button" class="btn btn-primary"
                                                @click="document.getElementById('property_images').click()">Select
                                                Files</button>
                                        </div>
                                        <div class="file-list mt-3">
                                            <div class="row">
                                                <template x-for="file in files" :key="file.name">
                                                    <div class="col-4 position-relative p-2">
                                                        <div class="square">
                                                            <img :src="URL.createObjectURL(file)" alt=""
                                                                class="img-thumbnail w-100 h-100">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                                @click="removeFile(file)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary me-2"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <span class="btn btn--base deal-save" @click="submitAssetForm(assetForm)">Save</span>
                                </div>
                            </div>
                            <div x-show="assetList">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fs-2">Assets</span>
                                        <button type="button" class="btn btn--base" @click="assetList = false">
                                            <i class="fas fa-plus"></i> Add Asset
                                        </button>
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="asset in assetsAdded" :key="asset.id">
                                                <tr>
                                                    <td x-text="asset.name"></td>
                                                    <td x-text="asset.address"></td>
                                                    <td x-text="asset.city"></td>
                                                    <td x-text="asset.state"></td>
                                                    <td>
                                                        <button type="button" class="btn" @click="assetEdit(asset)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn"
                                                            @click="assetDelete(asset)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-secondary me-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" @click="resetDealandAsset()"
                                            style="width:100px;">Done</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('script-lib')
        <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
        <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
    @endpush

    @push('style-lib')
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    @endpush
    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/stepper.css') }}">
    @endpush
    @push('script')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            var prefix = window.location.pathname.includes('partner') ? 'partner' : 'admin';

            function alpineHelpers() {
                return {
                    moneyFormat(el) {
                        let value = el.value;
                        // Remove non-numeric characters except for the decimal point
                        value = value.replace(/[^\d.]/g, '');

                        // Remove leading zeros
                        value = value.replace(/^0+(?=\d)/, '');
                        // If there's more than one decimal point, keep only the first one
                        const parts = value.split('.');
                        if (parts.length > 2) {
                            value = parts[0] + '.' + parts.slice(1).join('');
                        }
                        // Limit the decimal part to two digits
                        if (parts[1]) {
                            parts[1] = parts[1].slice(0, 2);
                            value = parts.join('.');
                        }
                        // Add commas for thousands separator
                        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        // Ensure that the value starts with $ and no other non-numeric characters
                        if (value !== '') {
                            el.value = '$' + value;
                        } else {
                            el.value = '$0';
                        }
                    },
                    percentFormat(el) {
                        let value = el.value;
                        // Remove non-numeric characters except for the decimal point
                        value = value.replace(/[^\d.]/g, '');

                        // Remove leading zeros
                        value = value.replace(/^0+(?=\d)/, '');
                        // If there's more than one decimal point, keep only the first one
                        const parts = value.split('.');
                        if (parts.length > 2) {
                            value = parts[0] + '.' + parts.slice(1).join('');
                        }
                        // Limit the decimal part to two digits
                        if (parts[1]) {
                            parts[1] = parts[1].slice(0, 2);
                            value = parts.join('.');
                        }
                        // Ensure that the value ends with % and no other non-numeric characters
                        if (value !== '') {
                            el.value = value + '%';
                        } else {
                            el.value = '0%';
                        }
                        el.setSelectionRange(el.value.length - 1, el.value.length - 1);
                    }
                }
            }
        </script>
        <script>
            var csrf = '{{ csrf_token() }}';
        
            function dashboard() {
                return {
                    ...alpineHelpers(),
                    currentStep: 'deal',
                    completedSteps: [],
                    errors: {},
                    dealErrors: {},
                    assetErrors: {},
                    loading: false,

                    dealForm: {
                        _token: csrf,
                        name: '',
                        type: 'Direct syndication',
                        deal_stage: '',
                        sec_type: '',
                        close_date: '',
                        owning_entity_name: '',
                        funds_received_before_gp_countersigns: '0',
                        send_funding_instructions_after_gp_countersigns: '0',
                    },
                    assetForm: {
                        _token: csrf,
                        name: '',
                        address: '',
                        city: '',
                        state: '',
                        zip: '',
                        country: '',
                        property_type: '',
                        property_class: '',
                        number_of_units: '',
                        type_of_units: '',
                        acquisition_date: '',
                        acquisition_price: '',
                        exit_date: '',
                        exit_price: '',
                        year_built: '',
                        deal_id: '',
                    },
                    assetsAdded: [],
                    assetErrors: {},
                    assetList: false,
                    files: [],
                    dragOver: false,
                    ConfirmationForm: {
                        _token: csrf,
                        deal_id: null, 
                        deal_ach_setting_id: null,
                        deal_name: '',
                        entity_name: '',
                        entity_type: '',
                        address: '',
                        controller_id: '',
                        controller_address: '',
                        state_registration: '',
                        verify_confirmation: 'approved'
                    },
                    async submitConfirmationForm(data) {
                        this.loading = true;
                        let url = "{{ route('admin.deals.approveEntity', ':deal_id') }}".replace(':deal_id', this
                            .ConfirmationForm.deal_id);
                        if (prefix === 'partner') {
                            url = "{{ route('partner.deals.approveEntity', ':deal_id') }}".replace(':deal_id', this
                                .ConfirmationForm.deal_id);
                        }

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    _token: this.ConfirmationForm._token,
                                    deal_ach_setting_id: this.ConfirmationForm.deal_ach_setting_id,
                                    verify_confirmation: this.ConfirmationForm.verify_confirmation
                                })
                            });

                            this.loading = false;

                            if (response.status === 422) {
                                const responseData = await response.json();
                                this.errors.confirmation = responseData.errors?.verify_confirmation?.[0] ||
                                    'Validation failed.';
                                return;
                            }

                            const responseData = await response.json();
                            if (response.status === 200) {
                                this.errors.confirmation = 'Entity confirmed successfully';
                                setTimeout(() => {
                                    $('#confirmDealEntityModal').modal('hide');
                                    window.location.reload();
                                }, 1000); // Close modal after 1 second
                            } else {
                                this.errors.confirmation = responseData.message || 'Failed to confirm entity.';
                            }
                        } catch (error) {
                            this.loading = false;
                            this.errors.confirmation = 'An error occurred while approving the entity.';
                            console.error('Error:', error);
                        }
                    },
                    handleFiles(event) {
                        const inputElement = event.target;
                        const selectedFiles = Array.from(inputElement.files);
                        const imageFiles = selectedFiles.filter(file => file.type.startsWith('image/'));

                        if (selectedFiles.length > imageFiles.length) {
                            cosyAlert('<strong>Error</strong><br />Only image files are allowed.', 'error');
                        }

                        this.files = imageFiles;

                        // Reset the input element's value to allow selecting the same file again
                        inputElement.value = '';
                    },

                    handleDrop(event) {
                        const droppedFiles = Array.from(event.dataTransfer.files);
                        const imageFiles = droppedFiles.filter(file => file.type.startsWith('image/'));

                        if (droppedFiles.length > imageFiles.length) {
                            cosyAlert('<strong>Error</strong><br />Only image files are allowed.', 'error');
                        }

                        this.files = [...this.files, ...imageFiles];

                        // Reset the dragOver state
                        this.dragOver = false;
                    },
                    removeFile(fileToRemove) {
                        this.files = this.files.filter(file => file !== fileToRemove);
                    },
                    async submitDealForm(data) {
                        this.loading = true;
                        debugger;
                        let url = "{{ route('admin.deals.store') }}";
                        if (prefix == 'partner') {
                            url = "{{ route('partner.deals.store') }}";
                        }

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(data)
                            });

                            this.loading = false;

                            if (response.status === 422) {
                                const responseData = await response.json();
                                // update errors in alpine data
                                this.dealErrors = responseData.errors;
                                return;
                            }

                            const responseData = await response.json();
                            if (response.status === 200) {
                                this.currentStep = 'assets';
                                this.completedSteps.push('deal');
                                this.assetForm.deal_id = responseData.deal.id;
                            } else {
                                // alert(responseData.message);
                                console.log(responseData);
                            }

                        } catch (error) {
                            console.error('Error:', error);
                        }
                    },

                    async submitAssetForm(data) {
                        this.loading = true;
                        let url = "{{ route('admin.assets.store') }}";
                        if (prefix == 'partner') {
                            url = "{{ route('partner.assets.store') }}";
                        }




                        try {

                            // TODO: Add the media array to upload Images in the request
                            // make form data to send files
                            let formData = new FormData();
                            for (const key in data) {
                                if (data.hasOwnProperty(key)) {
                                    formData.append(key, data[key]);
                                }
                            }

                            this.files.forEach(file => {
                                formData.append('property_images[]', file);
                            });


                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                },
                                body: formData

                            });

                            this.loading = false;

                            if (response.status === 422) {
                                const responseData = await response.json();
                                // update errors in alpine data
                                this.assetErrors = responseData.errors;
                                return;
                            }

                            const responseData = await response.json();
                            if (response.status === 200) {
                                this.assets = responseData.assets;
                                const modalElement = document.querySelector('.modal.show');
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                cosyAlert('<strong>Success</strong><br />Asset created Successfully!', 'success');
                                this.completedSteps.push('assets');
                                this.assetsAdded.push(responseData.asset);
                                this.assetList = true;
                                // reset asset form except deal_id
                                this.assetForm = {
                                    _token: csrf,
                                    name: '',
                                    address: '',
                                    city: '',
                                    state: '',
                                    zip: '',
                                    country: '',
                                    property_type: '',
                                    property_class: '',
                                    number_of_units: '',
                                    type_of_units: '',
                                    acquisition_date: '',
                                    acquisition_price: '',
                                    exit_date: '',
                                    exit_price: '',
                                    year_built: '',
                                    deal_id: '',
                                };


                            } else {
                                console.error('Error:', responseData);
                            }

                        } catch (error) {
                            console.error('Error:', error);
                        }
                    },
                    resetDealandAsset() {
                        this.currentStep = 'deal';
                        this.completedSteps = [];
                        this.errors = {};
                        this.loading = false;
                        this.dealForm = {
                            _token: csrf,
                            name: '',
                            type: '',
                            deal_stage: '',
                            sec_type: '',
                            close_date: '',
                            owning_entity_name: '',
                            funds_received_before_gp_countersigns: '0',
                            send_funding_instructions_after_gp_countersigns: '0',
                        };
                        this.assetForm = {
                            _token: csrf,
                            name: '',
                            address: '',
                            city: '',
                            state: '',
                            zip: '',
                            country: '',
                            property_type: '',
                            property_class: '',
                            number_of_units: '',
                            type_of_units: '',
                            acquisition_date: '',
                            acquisition_price: '',
                            exit_date: '',
                            exit_price: '',
                            year_built: '',
                            deal_id: '',
                        };
                        this.assetsAdded = [];
                        this.assetErrors = {};
                        this.assetList = false;
                        this.files = [];
                        this.dragOver = false;
                    },
                    init() {

                    },


                };
            }
        </script>

        <script>
            window.setDealId = function(dealId, deal) {
                // Get the Alpine.js scope of the modal
                const modal = document.getElementById('confirmDealEntityModal');
                const alpineInstance = Alpine.$data(modal);

                // Update ConfirmationForm with deal data
                alpineInstance.ConfirmationForm.deal_id = dealId;
                alpineInstance.ConfirmationForm.deal_name = deal.name || 'N/A';
                alpineInstance.ConfirmationForm.entity_name = deal.achsettings?.entity_name || 'N/A';
                alpineInstance.ConfirmationForm.entity_type = deal.achsettings?.entity_type || 'N/A';
                alpineInstance.ConfirmationForm.address = deal.achsettings?.address || 'N/A';
                alpineInstance.ConfirmationForm.controller_id = deal.achsettings?.controller_id || 'N/A';
                alpineInstance.ConfirmationForm.controller_address = deal.achsettings?.controller_address || 'N/A';
                alpineInstance.ConfirmationForm.state_registration = deal.achsettings?.state_registration || 'N/A';
                alpineInstance.ConfirmationForm.deal_ach_setting_id = deal.achsettings?.id || null;

                // Update modal HTML (optional, for display purposes)
                $('#confirmDealEntityForm').attr('data-deal-id', dealId);
            };
        </script>
    @endpush

    @push('script')
        <script>
            (function($) {
                "use strict";
                $('.detailBtn').on('click', function() {
                    let modal = $('#detailModal');
                    let deal = $(this).data('deal');
                    let curSymbol = '{{ gs('cur_sym') }}';
                    let html = '';
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Property')</span>
                            <span class="list--group-desc"><strong>${deal.name}</strong></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Total Invest Amount')</span>
                            <span class="list--group-desc"><strong>${deal.type}</strong></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Paid Amount')</span>
                            <span class="list--group-desc">${deal.sec_type}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="list--group-text">@lang('Next Profit Date')</span>
                            <span class="list--group-desc">${deal.close_date}</span>
                        </li>`;

                    modal.find('.userData').html(html);
                    modal.modal('show');
                });

                function formatTime(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                }
            })(jQuery);
        </script>
    @endpush
    @push('script')
        <script>
            (function($) {
                "use strict";

                let deleteId = null;

                window.setDeleteData = function(id, name) {
                    deleteId = id;
                    $('.deal-name').text(name);
                }

                window.deleteDeal = function() {
                    if (!deleteId) return;

                    let url = '{{ url('admin/deals') }}/' + deleteId;
                    if (prefix === 'partner') {
                        url = '{{ url('partner/deals/') }}/' + deleteId;
                    }

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#deleteConfirmModal').modal('hide');
                                window.location.reload();
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while deleting the deal';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                };
            })(jQuery);
        </script>
    @endpush
