@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->

@push('style')
    <style>
        .square {
            position: relative;
            width: 100%;
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
        }

        .summary-header {
            font-size: 10px;
            font-weight: bold;
        }

        .summary-header-content {
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
        }

        .square img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

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
            <div class="deal-summary" x-data="dealSummary()" x-cloak>
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
                        {{--  <li class="breadcrumbs-item active" aria-current="page">Copy of Testing Offering 1</li>  --}}
                    </ol>
                </nav>
                <hr>
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <h2 class="mb-0 fw-semibold" style="font-size: 24px;">Deal summary</h2>
                        <a href="{{ route($prefix . '.deals.edit', $deal->id) }}" class="text-primary fw-bold ms-3"
                            style="font-size: 14px;">{{ $deal->deal_stage }}</a>
                    </div>
                    <div>
                        <a href="{{ route($prefix . '.deals.edit', $deal->id) }}" class="btn btn-outline-primary"
                            id="manage-deal">
                            Manage Deal
                        </a>
                        <button class="btn btn-outline-primary" id="add-investment" onclick="initSelect2()"
                            data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                            + Add Investment
                        </button>
                    </div>
                </div>

                <!-- Deal Summary Boxes -->
                <div class="row text-center my-3">
                    <div class="col">
                        <div class="border p-3">
                            <p class="summary-header">Investments started</p>
                            <h4 class="summary-header-content">$<span
                                    class="summary-header-content id="investments-started">100,000</span></h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p class="summary-header">Documents signed</p>
                            <h4 class="summary-header-content">$<span
                                    class="summary-header-content id="documents-signed">0</span></h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p class="summary-header">Total funded</p>
                            <h4 class="summary-header-content">$<span
                                    class="summary-header-content id="total-funded">0</span></h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p class="summary-header">Total accepted</p>
                            <h4 class="summary-header-content">$<span
                                    class="summary-header-content id="total-accepted">0</span></h4>
                        </div>
                    </div>
                </div>


                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mt-5" id="deal-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#offerings" data-bs-toggle="tab">Offerings</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#classes" data-bs-toggle="tab">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#investments" data-bs-toggle="tab">Investments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#assets" data-bs-toggle="tab">Assets</a></li>
                    <li class="nav-item"><a class="nav-link" href="#distributions" data-bs-toggle="tab">Distributions</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#documents" data-bs-toggle="tab">Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="#valuation_form" data-bs-toggle="tab">Valuation forms</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#updates" data-bs-toggle="tab">Updates</a></li>
                    <li class="nav-item"><a class="nav-link" href="#members" data-bs-toggle="tab">Partners</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kpis" data-bs-toggle="tab">KPIs</a></li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content mt-4">
                    {{--  Investment content   --}}
                    <div class="tab-pane fade show active" id="offerings">
                        <h3>Offerings</h3>
                        <div class="d-flex justify-content-between">
                            <div class="search-bar position-relative">
                                <input type="text" name="search" id="search-offerings"
                                    class="form-control form-control-sm" placeholder="Search offerings..."
                                    style="padding-right: 2.5rem;" />
                                <i class="la la-search position-absolute"></i>
                            </div>
                            <template
                                x-if="(classes.length === 0 && buckets.every(bucket => bucket.classes.length === 0))">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#createClassModal">Add Offering
                                </button>
                            </template>

                            <template x-if="(classes.length > 0 || buckets.some(bucket => bucket.classes.length > 0))">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addOfferingModal">Add Offering
                                </button>
                            </template>

                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered mt-3" id="offerings-table">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="offering_name">@lang('Offering Name')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="internal_name">@lang('Internal Name')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="offering_size">@lang('Offering Size')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="status">@lang('Status')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="visiblity">@lang('Visiblity')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="type">@lang('Type')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <!-- No sort icons for the Actions column -->
                                        <th>@lang('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deal->offerings as $offering)
                                        <tr>
                                            <td><a
                                                    href="{{ route($prefix . '.deals.offerings.offering_detail', [$deal->id, $offering->id]) }}">{{ $offering->name }}</a>
                                            </td>
                                            <td>{{ $offering->internal_name ?? '-' }}</td>
                                            <td>{{ $offering->offering_size }}</td>
                                            <td>{{ $offering->status_text ?? 'Draft' }}</td>
                                            <td>{{ $offering->visibility_text }}</td>
                                            <td>{{ $offering->effective_investment }}</td>
                                            <td>
                                                <span role="button" title="delete"
                                                    onclick="confirmOfferingDelete('{{ route($prefix . '.deals.offerings.destroyOffering', ['deal' => $deal->id, 'offering' => $offering->id]) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <template x-if="offerings.length === 0">
                                        <tr>
                                            <td colspan="7" class="text-center">No offering available</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{--  classes table  --}}
                    <div class="tab-pane fade" id="classes">
                        <div class="mb-5">
                            <h3>Classes</h3>
                            <div class="d-flex justify-content-between">
                                <div class="search-bar position-relative">
                                    <input type="text" name="search" id="search-classes"
                                        class="form-control form-control-sm" placeholder="Search classes..."
                                        style="padding-right: 2.5rem;" />
                                    <i class="la la-search position-absolute"></i>
                                </div>
                                <button onclick="window.location.href='{{ route($prefix . '.deals.class', $deal->id) }}'"
                                    class="btn btn-outline-primary text-center">Edit Classes</button>
                            </div>
                            <!-- Making the table responsive with Bootstrap's .table-responsive -->
                            <div class="table-responsive mt-3" id="classes-table-container">
                                <table class="table table-bordered" id="classes-table">
                                    <thead>
                                        <tr>
                                            <th class="sortable" data-sort="name">@lang('Name')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="preferred_return">@lang('Preferred Return')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="class_bucket">@lang('Class Bucket')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="raise_amount_ownership">@lang('Raise Amount for Ownership')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="raise_amount_distribution">@lang('Raise Amount for Distribution')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="total_raised">@lang('Total Raised')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="ownership_entity">@lang('Ownership of Entity')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                            <th class="sortable" data-sort="distribution_share">@lang('Distribution Share')
                                                <span class="sort-icons">
                                                    <i class="fas fa-sort-up"></i>
                                                    <i class="fas fa-sort-down"></i>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($deal->classes as $class)
                                            <tr>
                                                <td><a
                                                        href="{{ route($prefix . '.deals.class.showClass', [$deal->id, $class->id] ) }}">{{ $class->equity_class_name }}</a>
                                                </td>
                                                <td>{{ $class->preferred_return }}</td>
                                                <td>{{ optional($class->bucket)->equity_bucket_name ?? '--' }}</td>
                                                <td>{{ $class->raise_amount_ownership }}</td>
                                                <td>{{ $class->raise_amount_distributions }}</td>
                                                <td>{{ $class->total_investments }}</td>
                                                <td>{{ $class->entity_legal_ownership }}</td>
                                                <td>{{ $class->distribution_share }}</td>
                                            </tr>
                                        @endforeach
                                        <template x-if="classes.length === 0">
                                            <tr>
                                                <td colspan="8" class="text-center">No classes available</td>
                                            </tr>
                                        </template>
                                        <!-- More rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h3>Distribution waterfalls</h3>
                            <div class="d-flex justify-content-between">
                                <div class="search-bar position-relative">
                                    <input type="text" name="search" id="search-waterfalls"
                                        class="form-control form-control-sm" placeholder="Search waterfalls..."
                                        style="padding-right: 2.5rem;" />
                                    <i class="la la-search position-absolute"></i>
                                </div>
                                <div class="buttons">
                                    <button onclick="window.location.href='{{ route($prefix . '.deals.class', $deal->id) }}'"
                                        class="btn btn-outline-primary text-center">Edit Waterfalls</button>
                                    <button class="btn btn-outline-primary text-center" data-bs-toggle="modal"
                                        data-bs-target="#setDefaultWaterfallModal">Set Default Waterfall</button>
                                </div>
                            </div>
                            <!-- Making the table responsive with Bootstrap's .table-responsive -->
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="waterfalls-table">
                                    <thead>
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-sort="name">@lang('Name')
                                                    <span class="sort-icons">
                                                        <i class="fas fa-sort-up"></i>
                                                        <i class="fas fa-sort-down"></i>
                                                    </span>
                                                </th>
                                                <th class="sortable" data-sort="classes">@lang('Classes')
                                                    <span class="sort-icons">
                                                        <i class="fas fa-sort-up"></i>
                                                        <i class="fas fa-sort-down"></i>
                                                    </span>
                                                </th>
                                                <th>@lang('Actions')</th>
                                            </tr>
                                        </thead>

                                    </thead>
                                    <tbody>

                                        @foreach ($deal->waterfalls as $waterfall)
                                            <tr>
                                                <td><a
                                                        href="{{ route($prefix . '.deals.class', $deal->id) }}">{{ $waterfall->waterfall_name }}</a>
                                                    @if ($waterfall->is_default)
                                                        <span class="badge bg-primary ms-2">Default</span>
                                                    @endif
                                                </td>
                                                <td>{{ $class->equity_class_name ?? 'NA' }}</td>
                                                <td>
                                                    <span role="button" title="view" class="me-2">
                                                        <a href="{{ route($prefix . '.deals.class', $deal->id) }}"><i
                                                                class="far fa-eye"></i></a>
                                                    </span>
                                                    @if (!$waterfall->is_basic)
                                                        <span role="button" title="Delete"
                                                            onclick="confirmWaterfallDelete('{{ route($prefix . '.deals.waterfalls.destroy', ['deal' => $deal->id, 'waterfall' => $waterfall->id]) }}')">
                                                            <i class="far fa-trash-alt text-danger"></i>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Optional message when no classes exist -->
                                        <template x-if="classes.length === 0">
                                            <tr>
                                                <td colspan="3" class="text-center">No waterfall available</td>
                                            </tr>
                                        </template>
                                        <!-- More rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{--  assets table  --}}
                    <div class="tab-pane fade" id="assets">
                        <h3>Assets</h3>
                        <div class="d-flex justify-content-between">
                            <div class="search-bar position-relative">
                                <input type="text" name="search" id="search-Assets"
                                    class="form-control form-control-sm" placeholder="Search Assets..."
                                    style="padding-right: 2.5rem;" />
                                <i class="la la-search position-absolute"></i>
                            </div>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#addAssetModal">Add Assets</button>
                        </div>
                        <div class="table-responsive mt-3" x-data>
                            <table class="table table-bordered mt-3" id="offerings-table">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="name">
                                            @lang('Name')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="address">
                                            @lang('Address')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="units">
                                            @lang('Units')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="year_built">
                                            @lang('Year Built')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="images">
                                            @lang('Images')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th>
                                            @lang('Actions')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deal->assets as $asset)
                                        <tr id="asset-row-{{ $asset->id }}" x-data="{ show: true }" x-show="show">
                                            <td>
                                                <a
                                                    href="{{ route($prefix . '.deals.assets.asset_detail', [$deal->id, $asset->id]) }}">
                                                    {{ $asset->name }}
                                                </a>
                                            </td>
                                            <td>{{ $asset->address ?? '-' }}</td>
                                            <td>{{ $asset->number_of_units ?? '-' }}</td>
                                            <td>{{ $asset->year_built ?? '-' }}</td>
                                            <td>
                                                {{ is_countable($asset->assetMedia) ? count($asset->assetMedia) : 0 }}
                                                {{ is_countable($asset->assetMedia) && count($asset->assetMedia) === 1 ? 'Image' : 'Images' }}
                                            </td>

                                            <td>
                                                <!-- Delete button -->
                                                <button class="text-danger delete-icon"
                                                    data-asset-id="{{ $asset->id }}"
                                                    onclick="confirmAssetDelete('{{ route($prefix . '.assets.destroy', [$asset->id]) }}', this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <template x-if="assets.length === 0">
                                        <tr>
                                            <td colspan="6" class="text-center">No assets available</td>
                                        </tr>
                                    </template>
                                </tbody>

                            </table>
                        </div>
                    </div>

                    {{--  documents tab  --}}
                    <div class="tab-pane fade" id="documents">
                        @include('admin.deals.documents')
                    </div>
                    <div class="tab-pane fade" id="members">
                        @include('admin.deals.summary.members')
                    </div>
                    {{--  Investment content   --}}
                    <div class="tab-pane fade" id="investments">
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
                                    @foreach ($deal->investments as $investment)
                                        <tr>
                                            <td class="sticky-left">
                                                {{ $investment->investor->investor_fname . ' ' . $investment->investor->investor_lname }}
                                            </td>
                                            <td>{{ $investment->investor->investor_email }}</td>
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
                                                    onclick="confirmInvestmentDelete(this, '{{ route($prefix . '.investments.deleteInvestment', ['deal' => $deal->id, 'id' => $investment->id]) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <template x-if="investments.length === 0">
                                        <tr>
                                            <td colspan="29" class="text-center">No investment available</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>


                    </div>

                    <!-- Distributions Tab Content -->
                    <div class="tab-pane fade" id="distributions">
                        <h3>Distributions</h3>
                        <div class="d-flex justify-content-between">
                            <div class="search-bar position-relative">
                                <input type="text" name="search" id="search-Distributions"
                                    class="form-control form-control-sm" placeholder="Search Distributions..."
                                    style="padding-right: 2.5rem;" />
                                <i class="la la-search position-absolute"></i>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addexportDistributionModal">Export distribution</button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addDistributionModal">Add Distribution</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered mt-3" id="offerings-table">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="memo">
                                            @lang('Memo')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="source">
                                            @lang('Source')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="type">
                                            @lang('Type ?')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="deducts_from">
                                            @lang('Deducts from ?')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="distribution_payments">
                                            @lang('Distribution payments')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="period_dates">
                                            @lang('Period dates')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="payment_date">
                                            @lang('Payment date')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th class="sortable" data-sort="visibility">
                                            @lang('Visibility')
                                            <span class="sort-icons">
                                                <i class="fas fa-sort-up"></i>
                                                <i class="fas fa-sort-down"></i>
                                            </span>
                                        </th>
                                        <th>
                                            @lang('Actions')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deal->distributions as $distribution)
                                        <tr>
                                            <td>{{ $distribution->memo }}</td>
                                            <td>{{ $distribution->source }}</td>
                                            <td>{{ $distribution->distribution_type }}</td>
                                            <td>{{ $distribution->count_toward_text }}</td>
                                            <td>{{ 'Payments' }}</td>
                                            <td>{{ $distribution->start_date->format('m/d/Y') . ' - ' . $distribution->end_date->format('m/d/Y') }}
                                            </td>
                                            <td>{{ $distribution->start_date->format('m/d/Y') }}</td>
                                            <td>
                                                <button type="button"
                                                    @click="toggleVisibility({{ $distribution->id }}, $event.target.closest('button'))"
                                                    class="toggle-btn {{ $distribution->is_visible ? 'on' : 'off' }}">
                                                    <span>{{ $distribution->is_visible ? 'On' : 'Off' }}</span>
                                                </button>
                                            </td>
                                            <td>
                                                <span role="button" title="delete"
                                                    onclick="confirmDistributionDelete(this, '{{ route($prefix . '.distributions.destroy', ['id' => $distribution->id]) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <template x-if="distributions.length === 0">
                                        <tr>
                                            <td colspan="9" class="text-center">No distribution available</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kpis">
                        @include('admin.deals.kpi')
                    </div>
                    <div class="tab-pane fade" id="valuation_form">
                        <h3>Investment valuation and NAV statement</h3>
                        <p class="pt-3 pb-2">For more information on NAV statements, check out <a href="#">this
                                support article</a></p>
                        <div class="d-flex justify-content-between">
                            <input type="text" class="form-control w-25" placeholder="Search Valuations..."
                                id="search-Valuations">
                            <div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addValuationModal">+ Add Valuation</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered mt-3" id="valuation_form-table">
                                <thead>
                                    <tr>
                                        <th>Valuation date</th>
                                        <th>NAV per share</th>
                                        <th>Deal equity multiple</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--  @foreach ($deal->distributions as $distribution)  --}}
                                    <tr>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>
                                            <span role="button" title="delete">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    {{--  @endforeach  --}}
                                </tbody>
                            </table>
                        </div>

                        {{--  SREO  --}}
                        <h3 class="pt-6">SREO</h3>
                        <div class="d-flex justify-content-between">
                            <input type="text" class="form-control w-25" placeholder="Search snapshots..."
                                id="search-snapshots">
                            <div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addSnapshotModal">+ Add Snapshot</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered mt-3" id="offerings-table">
                                <thead>
                                    <tr>
                                        <th>Snapshot date</th>
                                        <th>SREO</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--  @foreach ($deal->distributions as $distribution)  --}}
                                    <tr>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>
                                            <span role="button" title="delete">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    {{--  @endforeach  --}}
                                </tbody>
                            </table>
                        </div>

                        {{--  FMV forms  --}}
                        <h3 class="pt-6">FMV forms</h3>
                        <p class="pt-3 pb-2">Create templates to generate FMV forms for your IRA investors.</p>
                        <div class="d-flex justify-content-between">
                            <input type="text" class="form-control w-25" placeholder="Search forms..."
                                id="search-forms">
                            <div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addFMVFormModal">Add FMV Form</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered mt-3" id="offerings-table">
                                <thead>
                                    <tr>
                                        <th>FMV name</th>
                                        <th>Signature date</th>
                                        <th>Visibility</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--  @foreach ($deal->distributions as $distribution)  --}}
                                    <tr>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>
                                            <span role="button" title="delete">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    {{--  @endforeach  --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <style>
                    /* Base Button Style */
                    .toggle-btn {
                        border: none;
                        border-radius: 50px;
                        width: 70px;
                        height: 30px;
                        font-size: 14px;
                        font-weight: bold;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
                        cursor: pointer;
                        transition: background-color 0.4s;
                        overflow: hidden;
                        padding: 0 10px;
                    }

                    /* Circle Inside Button */
                    .toggle-btn::before {
                        content: "";
                        width: 24px;
                        height: 24px;
                        background-color: white;
                        border-radius: 50%;
                        position: absolute;
                        transition: all 0.4s;
                    }

                    /* Text Inside Button */
                    .toggle-btn span {
                        position: absolute;
                        font-size: 13px;
                        font-weight: bold;
                        color: white;
                        z-index: 1;
                    }

                    /* OFF State */
                    .toggle-btn.off {
                        background-color: #b5b5b5;
                        /* Gray */
                    }

                    .toggle-btn.off::before {
                        left: 4px;
                    }

                    .toggle-btn.off span {
                        right: 10px;
                        content: "Off";
                    }

                    /* ON State */
                    .toggle-btn.on {
                        background-color: #4CAF50;
                        /* Green */
                    }

                    .toggle-btn.on::before {
                        left: 42px;
                    }

                    .toggle-btn.on span {
                        left: 10px;
                        content: "On";
                    }

                    /* Style each option-card as clickable */
                    .search-bar i {
                        right: 10px;
                        top: 50%;
                        transform:
                            translateY(-50%) scaleX(-1);
                        pointer-events: none;
                    }

                    .option-card {
                        display: block;
                        padding: 20px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        margin-bottom: 15px;
                        cursor: pointer;
                        transition: box-shadow 0.3s ease, border-color 0.3s ease;
                    }

                    /* Apply blue shadow and border on hover */
                    .option-card:hover {
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
                        border-color: #007bff;
                    }

                    /* Apply border and shadow to the entire option-card when selected */
                    .option-card input[type="radio"]:checked+.option-card {
                        border-color: #007bff;
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
                    }

                    /* Hide default radio button */
                    .option-card input[type="radio"] {
                        display: none;
                    }

                    /* Blue border on the entire card when selected */
                    .option-card.selected {
                        border: 2px solid #007bff;
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
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

                    /* Add blue shadow on hover */
                    .card-hover:hover {
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
                        /* Blue shadow */
                        border-color: #007bff;
                        /* Blue border on hover */
                    }
                </style>
                {{--  style for investment table   --}}
                <style>
                    /* Make the first column sticky */
                    #investments-table tbody tr td:first-child,
                    #investments-table th:first-child {
                        position: sticky;
                        left: 0;
                        background-color: white;
                        /* Optional: To keep background color consistent */
                        z-index: 1;
                    }

                    /* Make the last column sticky */
                    #investments-table tbody tr td:last-child,
                    #investments-table th:last-child {
                        position: sticky;
                        right: 0;
                        background-color: white;
                        /* Optional: To keep background color consistent */

                    }
                </style>
                {{--  end of style for classes table  --}}

                <style>
                    /* Shared table styles for Classes and Waterfall tables */
                    #classes-table,
                    #waterfalls-table {
                        width: 100%;
                        table-layout: fixed;
                        border-collapse: collapse;
                    }

                    #classes-table th,
                    #waterfalls-table th {
                        font-size: 0.875rem;
                        /* Match nav-tabs font size */
                        padding: 12px 8px;
                        background-color: #f8f9fa;
                        border-bottom: 2px solid #dee2e6;
                        font-weight: 500;
                        text-align: left;
                        color: #495057;
                        line-height: 1.2;
                        height: auto;
                        white-space: normal;
                        word-wrap: break-word;
                    }

                    #classes-table td,
                    #waterfalls-table td {
                        padding: 12px 8px;
                        vertical-align: middle;
                        line-height: 1.2;
                        white-space: normal;
                        word-wrap: break-word;
                    }

                    /* Classes table column widths */
                    #classes-table th:nth-child(1) {
                        width: 18%;
                    }

                    /* Name */
                    #classes-table th:nth-child(2) {
                        width: 10%;
                    }

                    /* Preferred Return */
                    #classes-table th:nth-child(3) {
                        width: 10%;
                    }

                    /* Class Bucket */
                    #classes-table th:nth-child(4) {
                        width: 14%;
                    }

                    /* Raise Amount for Ownership */
                    #classes-table th:nth-child(5) {
                        width: 14%;
                    }

                    /* Raise Amount for Distribution */
                    #classes-table th:nth-child(6) {
                        width: 12%;
                    }

                    /* Total Raised */
                    #classes-table th:nth-child(7) {
                        width: 11%;
                    }

                    /* Ownership of Entity */
                    #classes-table th:nth-child(8) {
                        width: 11%;
                    }

                    /* Distribution Share */

                    /* Waterfall table column widths */
                    #waterfalls-table th:nth-child(1) {
                        width: 40%;
                    }

                    /* Name */
                    #waterfalls-table th:nth-child(2) {
                        width: 40%;
                    }

                    /* Classes */
                    #waterfalls-table th:nth-child(3) {
                        width: 20%;
                    }

                    /* Actions */

                    /* Remove horizontal scroll ONLY for Classes table */
                    #classes-table-container {
                        overflow-x: visible !important;
                    }

                    /* Keep horizontal scroll for other tables */
                    .table-responsive:not(#classes-table-container) {
                        overflow-x: auto !important;
                    }

                    /* Ensure table cells wrap text properly */
                    #classes-table th,
                    #classes-table td,
                    #waterfalls-table th,
                    #waterfalls-table td {
                        overflow-wrap: break-word;
                        word-wrap: break-word;
                        -ms-word-break: break-word;
                        word-break: break-word;
                    }

                    /* Badge styling */
                    .badge.bg-primary {
                        font-size: 0.75rem;
                        padding: 0.25rem 0.5rem;
                    }

                    /* Link styling */
                    #classes-table a,
                    #waterfalls-table a {
                        color: #0d6efd;
                        text-decoration: none;
                    }

                    #classes-table a:hover,
                    #waterfalls-table a:hover {
                        text-decoration: underline;
                    }
                </style>
                {{--  end of style for classes table  --}}

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
                </style>

                <style>
                    /* Mobile styles for Classes table only */
                    @media (max-width: 768px) {
                        #classes-table {
                            overflow-x: auto;
                        }
                    }
                </style>
                {{--  end of style for classes table  --}}
                <div class="modal fade" id="createClassModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Classes required</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Before creating an offering, you must first add at least one LP or Mezzanine class.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary"
                                    onclick="window.location.href='{{ route($prefix . '.deals.class', [$deal->id]) }}'">Add
                                    Class</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Offering Modal Structure -->
                <div class="deal-modal modal right fade" id="addOfferingModal" tabindex="-1"
                    aria-labelledby="addOfferingModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white">
                                <h5 class="modal-title col text-white">Add Offering</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    @csrf
                                    <div x-show="offerings.length !== 0">
                                        <h3>Offering type</h3>
                                        <div class="mb-3">
                                            <label for="offering_capital_call" class="form-label">Capital Call*</label>
                                            <select id="offering_capital_call" name="offering_capital_call" class="form-select"
                                                x-on:input="offeringErrors.offering_capital_call = ''"
                                                x-model="offeringForm.offering_capital_call" required>
                                                <option value="no">No</option>
                                                <option value="yes">Yes</option>
                                            </select>
                                            {{--  <span x-show="offeringErrors.offering_capital_call"
                                                x-text="offeringErrors.offering_capital_call" class="text-danger"></span>  --}}
                                        </div>
                                    </div>
                                    <h3>Offering details</h3>
                                    <p>Add an offering to share with investors to start raising equity.</p>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Offering Name*</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            x-on:input="offeringErrors.name = ''" x-model="offeringForm.name" required>
                                        <span x-show="offeringErrors.name" x-text="offeringErrors.name"
                                            class="text-danger"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Internal Name</label>
                                        <input type="text" id="internal_name" name="internal_name"
                                            class="form-control" x-model="offeringForm.internal_name">
                                    </div>
                                    <div class="mb-3">
                                        <label>Assets*</label>
                                        {{-- Select Box with multiple select --}}
                                        <select id="assets" name="assets" class="form-select"
                                            x-on:input="offeringErrors.assets = ''" x-model="offeringForm.assets"
                                            required>
                                            <option value="">Select Assets</option>
                                            @foreach ($deal->assets as $asset)
                                                <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                            @endforeach
                                        </select>
                                        <span x-show="offeringErrors.assets" x-text="offeringErrors.assets"
                                            class="text-danger"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sec_type" class="form-label">Offering classes*</label>
                                        {{-- Select Box with multiple select --}}
                                        <select id="offering_classes" name="offering_classes" class="form-select"
                                            x-on:input="offeringErrors.offering_classes = ''"
                                            x-model="offeringForm.offering_classes" required>
                                            <option value="">Select Offering Classes</option>
                                            @foreach ($deal->classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->equity_class_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span x-show="offeringErrors.offering_classes"
                                            x-text="offeringErrors.offering_classes" class="text-danger"></span>
                                    </div>

                                    <div class="mb-3">
                                        <label for="offering_size" class="form-label">Offering Size*($)</label>
                                        <input type="text" x-on:input="moneyFormat($el)" id="offering_size"
                                            name="offering_size" class="form-control" placeholder="$"
                                            x-model="offeringForm.offering_size" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="verify_investor_accreditation" class="form-label">Verify investor
                                            accreditation*</label>
                                        <div class="d-flex align-items-center">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input"
                                                    name="verify_investor_accreditation" :value="1"
                                                    x-on:input="offeringErrors.verify_investor_accreditation = ''"
                                                    x-model="offeringForm.verify_investor_accreditation">
                                                Yes (most common)
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input"
                                                    name="verify_investor_accreditation"
                                                    :value="0"x-on:input="offeringErrors.verify_investor_accreditation = ''"
                                                    x-model="offeringForm.verify_investor_accreditation">
                                                No
                                            </label>
                                        </div>
                                        <span x-show="offeringErrors.verify_investor_accreditation"
                                            x-text="offeringErrors.verify_investor_accreditation"
                                            class="text-danger"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="public_offering" class="form-label">Public Offering*</label>
                                        <p class="mb-3">by doing yes this offering show on all users dashboard</p>
                                        <div class="d-flex align-items-center">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input"
                                                    name="public_offering" :value="1"
                                                    x-on:input="offeringErrors.public_offering = ''"
                                                    x-model="offeringForm.public_offering">
                                                Yes
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input"
                                                    name="public_offering"
                                                    :value="0"x-on:input="offeringErrors.public_offering = ''"
                                                    x-model="offeringForm.public_offering">
                                                No
                                            </label>
                                        </div>
                                        <span x-show="offeringErrors.public_offering"
                                            x-text="offeringErrors.public_offering"
                                            class="text-danger"></span>
                                    </div>
                                    {{-- Upload Images --}}
                                    <div class="mb-3">
                                        <label for="property_images" class="form-label">Upload Images</label>
                                        <div class="file-uploader">
                                            <input type="file" id="property_images" name="property_images[]"
                                                class="form-control" multiple @change="handleFiles($event)" hidden>
                                            <div class="drop-zone" @drop.prevent="handleDrop($event)"
                                                @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                                                :class="{ 'drag-over': dragOver }">
                                                <p class="drop-zone-text">Drag & drop files here or click to upload</p>
                                                <button type="button" class="btn btn-primary"
                                                    @click="document.getElementById('property_images').click()">Select
                                                    Files</button>
                                            </div>
                                            <div class="file-list mt-3">
                                                <template x-for="file in files" :key="file.name">
                                                    <div class="file-item">
                                                        <span x-text="file.name"></span>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            @click="removeFile(file)">Remove</button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    {{--  here add a box in which show past created offering and write copy from + offering->name and by that copy of all the data of offering   --}}

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary me-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <span class="btn btn-primary deal-save" @click="submitOfferingForm(offeringForm)">
                                            Save
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--  modal structure for assets  --}}
                <div class="deal-modal modal right fade" id="addAssetModal" tabindex="-1"
                    aria-labelledby="addAssetModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white">
                                <h5 class="modal-title col text-white">Add Asset</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Property Name*</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            x-on:input="assetErrors.name = ''" x-model="assetForm.name" required>
                                        <span x-show="assetErrors.name" x-text="assetErrors.name"
                                            class="text-danger"></span>
                                    </div>
                                    {{-- Country --}}
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country*</label>
                                        <input type="text" id="country" name="country" class="form-control"
                                            x-model="assetForm.country" required>
                                    </div>
                                    {{-- Address --}}
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address*</label>
                                        <input type="text" id="address" name="address" class="form-control"
                                            x-model="assetForm.address" required>
                                    </div>


                                    {{-- City --}}
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City*</label>
                                        <input type="text" id="city" name="city" class="form-control"
                                            x-model="assetForm.city" required>
                                    </div>
                                    {{-- State --}}
                                    <div class="mb-3">
                                        <label for="state" class="form-label">State*</label>
                                        <input type="text" id="state" name="state" class="form-control"
                                            x-model="assetForm.state" required>
                                    </div>


                                    {{-- Zip --}}
                                    <div class="mb-3">
                                        <label for="zip" class="form-label">Zip*</label>
                                        <input type="text" id="zip" name="zip" class="form-control"
                                            x-model="assetForm.zip" required>
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
                                        <input type="text" id="property_class" name="property_class"
                                            class="form-control" x-model="assetForm.property_class" required>
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
                                            <label for="acquisition_date" class="form-label mb-2">Acquisition
                                                Date*</label>
                                            <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                id="acquisition_date" name="acquisition_date" class="form-control"
                                                x-model="assetForm.acquisition_date" required>
                                        </div>
                                        {{-- Acquisition Price --}}
                                        <div class="mb-3 col-6">
                                            <label for="acquisition_price" class="form-label mb-2">Acquisition
                                                Price*($)</label>
                                            <input type="text" x-on:input="moneyFormat($el)" id="acquisition_price"
                                                name="acquisition_price" class="form-control"
                                                x-model="assetForm.acquisition_price" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- Exit Date --}}
                                        <div class="mb-3 col-6">
                                            <label for="exit_date" class="form-label mb-2">Exit Date*</label>
                                            <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                id="exit_date" name="exit_date" class="form-control"
                                                x-model="assetForm.exit_date" required>
                                        </div>
                                        {{-- Exit Price --}}
                                        <div class="mb-3 col-6">
                                            <label for="exit_price" class="form-label mb-2">Exit Price*($)</label>
                                            <input type="text" x-on:input="moneyFormat($el)" id="exit_price"
                                                name="exit_price" class="form-control" x-model="assetForm.exit_price"
                                                required>
                                        </div>
                                    </div>
                                    {{-- Year Built --}}
                                    <div class="mb-3">
                                        <label for="year_built" class="form-label mb-2">Year Built*</label>
                                        <input type="number" x-mask="9999" placeholder="YYYY" id="year_built"
                                            name="year_built" class="form-control" x-model="assetForm.year_built"
                                            required>
                                    </div>

                                    {{-- Upload Images --}}
                                    <div class="mb-3">
                                        <label for="property_images" class="form-label">Property Images</label>
                                        <div class="file-uploader">
                                            <input type="file" id="property_images" name="property_images[]"
                                                class="form-control" accept="image/*" multiple
                                                @change="handleFiles($event)" hidden>
                                            <div class="drop-zone" @drop.prevent="handleDrop($event)"
                                                @dragover.prevent="dragOver = true"
                                                @dragleave.prevent="dragOver = false" :class="{ 'drag-over': dragOver }">
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
                                        <span class="btn btn-primary deal-save"
                                            @click="submitAssetForm(assetForm)">Save</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--  distribution method  --}}
                <div class="deal-modal modal right fade" id="addDistributionModal" tabindex="-1"
                    aria-labelledby="addDistributionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <!-- Modal Header -->
                            <div class="modal-header row bg-primary text-white"
                                style="padding: 30px 20px; height: 90px;">
                                <h5 class="modal-title col text-white">Add Distribution</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Step Navigation -->
                                <div class="step-container">
                                    <div class="step completed"></div>
                                </div>

                                @include('admin.deals.distribution.distribution_form')
                            </div>
                        </div>
                    </div>
                </div>
                {{--  export distribution  --}}
                <div class="deal-modal modal right fade" id="addexportDistributionModal" tabindex="-1"
                    aria-labelledby="addexportDistributionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <!-- Modal Header -->
                            <div class="modal-header row bg-primary text-white"
                                style="padding: 30px 20px; height: 150px;">
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div>
                                    <h2 class="modal-title col text-white">Export total distributions
                                        <p>Generate an Excel file with total distributions paid to each investment within
                                            the selected time span</p>
                                    </h2>
                                </div>

                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="time_span">Time span <span class="text-danger">*</span></label>
                                    <select class="form-control" id="time_span">
                                        <option value="last_year">Last Year</option>
                                        <option value="this_year">This Year</option>
                                        <option value="last_quarters">Last 4 quarters</option>
                                        <option value="length_of_deal">Length of deal (All distributions)</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>

                                <!-- From and To date fields -->
                                <div id="custom-date-fields" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="time_from">From <span class="text-danger">*</span></label>
                                        <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                            class="form-control" id="time_from">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="time_to">To <span class="text-danger">*</span></label>
                                        <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                            class="form-control" id="time_to">
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex mt-5 justify-content-between">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                        aria-label="Close">Cancel</button>
                                    <button type="submit" class="btn btn-secondary">Export distribution</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                {{--  delete modal for assets  --}}
                <div class="modal" id="addDeleteModal" tabindex="-1" aria-labelledby="addDeleteModalLabel"
                    aria-hidden="true" x-data="{ showModal: false, assetUrl: '' }" x-show="showModal" x-cloak>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDeleteModalLabel">Delete Asset</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    @click="showModal = false"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-4 text-lg">Are you sure you want to delete this asset?</p>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary me-2"
                                        data-bs-dismiss="modal" @click="showModal = false">No</button>
                                    <button class="btn btn-danger"
                                        @click="deleteAsset(assetUrl, () => showModal = false)">Yes, Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--  investment modal  --}}
                @include ('admin.deals.investment_deal')
                {{--  Fund admin models  --}}
                @include ('admin.deals.valuation_form')

                {{-- Set Default Waterfall Model --}}
                <!-- Set Default Waterfall Modal -->
                <div class="modal fade" id="setDefaultWaterfallModal" tabindex="-1"
                    aria-labelledby="setDefaultWaterfallModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="setDefaultWaterfallModalLabel">Set Default Waterfall</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="defaultWaterfall" class="form-label">Select Waterfall</label>
                                    <select id="defaultWaterfall" class="form-select"
                                        x-model="selectedDefaultWaterfall">
                                        <option value="">Select Waterfall</option>
                                        @foreach ($deal->waterfalls as $waterfall)
                                            <option value="{{ $waterfall->id }}"
                                                @if ($waterfall->is_default) selected @endif>
                                                {{ $waterfall->waterfall_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="setDefaultWaterfall()">Set as
                                    Default</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoU4olE2B6fMxyn6e+5m5iNqXG3yjZrM+2V7ef5azJ0z3Qf" crossorigin="anonymous">
    </script>


    <!-- Custom JS for Dynamic Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handling tab navigation (Bootstrap requires data-bs-toggle)
            const tabs = document.querySelectorAll('.nav-link');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Search functionality for offerings and classes
            const searchOfferingsInput = document.getElementById('search-offerings');
            searchOfferingsInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#offerings-table tbody tr');
                rows.forEach(row => {
                    const nameCell = row.cells[0];
                    const name = nameCell.textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            const searchWaterfallsInput = document.getElementById('search-waterfalls');
            searchWaterfallsInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#waterfalls-table tbody tr');
                rows.forEach(row => {
                    const nameCell = row.cells[0];
                    const name = nameCell.textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            const searchClassesInput = document.getElementById('search-classes');
            searchClassesInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#classes-table tbody tr');
                rows.forEach(row => {
                    const nameCell = row.cells[0];
                    const name = nameCell.textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            const searchAssetsInput = document.getElementById('search-assets');
            searchAssetsInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#assets-table tbody tr');
                rows.forEach(row => {
                    const nameCell = row.cells[0];
                    const name = nameCell.textContent.toLowerCase();
                    if (name.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            const searchInvestmentsInput = document.getElementById('search-investments');
            searchInvestmentsInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#investments-table tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? "" : "none";
                });
            });


            // Action buttons (for managing deals and adding investments)
            document.getElementById('manage-deal').onclick = function() {
                window.location.href = "{{ route($prefix . '.deals.class', $deal) }}";
            };

            addInvestmentButton.addEventListener('click', function() {
                alert('Adding investment...');
            });
        });
    </script>
@endsection

@push('script')
    <!-- jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <script type="text/javascript" src="{{ asset('assets/admin/plugins/multipleselect/bootstrap-multiselect.min.js') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
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

        function dealSummary() {
            return {
                ...alpineHelpers(),
                errors: {},
                DistributionForm: [],
                loading: false,
                offeringErrors: {},
                classes: @json($deal->classes),
                buckets: @json($deal->buckets),
                offerings: @json($deal->offerings),
                assets: @json($deal->assets),
                investments: @json($deal->investments),
                distributions: @json($deal->distributions),
                waterfalls: @json($deal->waterfalls),
                offeringForm: {
                    _token: csrf,
                    name: '',
                    internal_name: '',
                    assets: '',
                    offering_classes: '',
                    offering_size: '',
                    offering_media: [],
                    visibility: 'show_on_dashboard',
                    status: '1',
                    verify_investor_accreditation: '',
                    public_offering: false,
                    offering_capital_call: 'no',
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
                distributionForm: {
                    _token: csrf,
                    distribution_source: '',
                    distribution_type: '',
                    deduct_from: '',
                    distribution_amount: '',
                    included_class: '',
                    amount: '',
                    period_start_date: '',
                    period_end_date: '',
                    distribution_date: '',
                    memo: '',
                    visibility: 'visible , hidden',
                    included_class: '',

                },

                selectedDefaultWaterfall: '',

                assetsAdded: [],
                assetErrors: {},
                assetList: false,
                files: [],
                dragOver: false,
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
                async submitAssetForm(data) {
                    this.loading = true;
                    let url = "{{ route($prefix . '.assets.store') }}";

                    data.deal_id = "{{ $deal->id }}";



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
                            modalInstance.hide();
                            cosyAlert('<strong>Success</strong><br />Asset created Successfully!', 'success');
                            window.location.reload();
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
                                deal_id: "{{ $deal->id }}",
                            };


                        } else {
                            console.error('Error:', responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                //submit investment
                //submit investor
                //submit profile
                async submitOfferingForm(data) {
                    this.loading = true;
                    let url = "{{ route($prefix . '.deals.offerings.store', $deal->id) }}";

                    try {

                        let formData = new FormData();
                        for (const key in data) {
                            if (data.hasOwnProperty(key)) {
                                formData.append(key, data[key]);
                            }
                        }

                        this.files.forEach(file => {
                            formData.append('offering_media[]', file);
                        });

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.offeringErrors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();

                            // Reload the page
                            window.location.href = window.location.pathname + '#classes';
                            window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                resetOffering() {
                    this.errors = {};
                    this.loading = false;
                    this.offeringForm = {
                        _token: csrf,
                        name: '',
                        internal_name: '',
                        assets: '',
                        offering_classes: '',
                        offering_size: '',
                        offering_media: [],
                        visibility: 'show_on_dashboard',
                    };
                    this.files = [];
                    this.dragOver = false;
                },
                init() {

                },

                // setDefaultWaterfall() {
                //     if (!this.selectedDefaultWaterfall) {
                //         cosyAlert('<strong>Error</strong><br />Please select a waterfall to set as default.', 'error');
                //         return;
                //     }

                //     const url = "{{ route($prefix . '.deals.waterfalls.default', $deal) }}";
                //     const data = {
                //         _token: csrf,
                //         waterfall_id: this.selectedDefaultWaterfall
                //     };

                //     fetch(url, {
                //             method: 'POST',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //             },
                //             body: JSON.stringify(data),
                //         })
                //         .then(response => response.json())
                //         .then(data => {
                //             if (data.success) {
                //                 cosyAlert('<strong>Success</strong><br />Default waterfall set successfully!', 'success');
                //                 window.location.reload();
                //             } else {
                //                 cosyAlert('<strong>Error</strong><br />' + data.message, 'error');
                //             }
                //         })
                //         .catch(error => {
                //             console.error('Error:', error);
                //             cosyAlert('<strong>Error</strong><br />An error occurred while setting the default waterfall.', 'error');
                //         });
                // },
                toggleVisibility(distributionId, button) {
                    this.loading = true; // Show loader

                    const isVisible = button.classList.contains('on');
                    const newVisibility = !isVisible;

                    fetch(`/admin/distributions/toggle-visibility/${distributionId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                is_visible: newVisibility
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                button.classList.toggle('on', newVisibility);
                                button.classList.toggle('off', !newVisibility);
                                button.querySelector('span').textContent = newVisibility ? 'On' : 'Off';
                            } else {
                                alert('Failed to update visibility');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred');
                        })
                        .finally(() => {
                            this.loading = false; // Hide loader
                        });
                }




            };
        }
    </script>
    <script>
        function confirmWaterfallDelete(url) {
            Swal.fire({
                title: 'Delete Waterfall Distribution',
                text: "Are you sure you want to delete this waterfall distribution? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteWaterfall(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Waterfall distribution has been deleted successfully.',
                            'success'
                        );
                    });
                }
            });
        }

        function deleteWaterfall(url, onSuccess) {
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
                        if (onSuccess) onSuccess();
                        window.location.href = window.location.pathname + '#classes';
                        window.location.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the waterfall distribution.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the waterfall distribution.',
                        'error'
                    );
                });
        }

        function deleteAsset(url) {
            Swal.fire({
                title: 'Delete Asset',
                text: "Are you sure you want to delete this Asset? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAsset(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Asset has been deleted successfully.',
                            'success'
                        );
                    });
                }
            });
        }

        function deleteAsset(url, onSuccess) {
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
                        if (onSuccess) onSuccess();
                        window.location.href = window.location.pathname + '#classes';
                        window.location.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the Asset.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the Asset.',
                        'error'
                    );
                });
        }

        function setDefaultWaterfall() {
            const selectedDefaultWaterfall = document.getElementById('defaultWaterfall').value;
            if (!selectedDefaultWaterfall) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a waterfall to set as default.'
                });
                return;
            }

            const url = "{{ route($prefix . '.deals.waterfalls.default', $deal) }}";
            const data = {
                _token: '{{ csrf_token() }}',
                waterfall_id: selectedDefaultWaterfall
            };

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cosyAlert('<strong>Success</strong><br />Default waterfall set successfully!', 'success');
                        $('#setDefaultWaterfallModal').modal('hide');
                        window.location.reload();
                    } else {
                        cosyAlert('<strong>Error</strong><br />' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while setting the default waterfall.'
                    });
                });

        }

        function confirmDistributionDelete(element, url) {
            Swal.fire({
                title: 'Delete Distribution',
                text: "Are you sure you want to delete this Distribution? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDistribution(element, url);
                }
            });
        }

        function deleteDistribution(element, url) {
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
                            'Distribution has been deleted successfully.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the Distribution.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the Distribution.',
                        'error'
                    );
                });
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('classes-table');
            const headers = table.querySelectorAll('th.sortable');

            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    const isAsc = !header.classList.contains('asc');

                    // Remove all sort classes
                    headers.forEach(h => {
                        h.classList.remove('asc', 'desc');
                    });

                    // Add appropriate sort class
                    header.classList.add(isAsc ? 'asc' : 'desc');

                    // Sort the table
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    rows.sort((a, b) => {
                        let aVal = a.querySelector(`td:nth-child(${header.cellIndex + 1})`)
                            .textContent.trim();
                        let bVal = b.querySelector(`td:nth-child(${header.cellIndex + 1})`)
                            .textContent.trim();

                        // Handle numeric values (including currency and percentages)
                        if (!isNaN(aVal.replace(/[$,%]/g, ''))) {
                            aVal = parseFloat(aVal.replace(/[$,%]/g, ''));
                            bVal = parseFloat(bVal.replace(/[$,%]/g, ''));
                        }

                        if (aVal < bVal) return isAsc ? -1 : 1;
                        if (aVal > bVal) return isAsc ? 1 : -1;
                        return 0;
                    });

                    // Reorder the rows
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>

    <script>
        function initSelect2() {
            $('#select_investment_tags').select2({
                // allowClear: true,
                tags: true,
                insertTag: function(data, tag) {
                    // Insert the tag at the end of the results
                    data.push(tag);
                },
                dropdownParent: $('#addInvestmentModal'),
                tokenSeparators: [',', ' ']
            });
            $('#select_investor_tags').select2({
                // allowClear: true,
                tags: true,
                insertTag: function(data, tag) {
                    // Insert the tag at the end of the results
                    data.push(tag);
                },
                dropdownParent: $('#addInvestorModal'),
                tokenSeparators: [',', ' ']
            });
        }
    </script>
@endpush

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Export Table to Excel
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

    <script>
        function confirmWaterfallDelete(url) {
            Swal.fire({
                title: 'Delete Waterfall Distribution',
                text: "Are you sure you want to delete this waterfall distribution? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteWaterfall(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Waterfall distribution has been deleted successfully.',
                            'success'
                        );
                    });
                }
            });
        }

        function deleteWaterfall(url, onSuccess) {
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
                        if (onSuccess) onSuccess();
                        window.location.href = window.location.pathname + '#classes';
                        window.location.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the waterfall distribution.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the waterfall distribution.',
                        'error'
                    );
                });
        }

        function confirmOfferingDelete(url) {
            Swal.fire({
                title: 'Delete Offering',
                text: "Are you sure you want to delete this Offering? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteOffering(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Offering has been deleted successfully.',
                            'success'
                        );
                    });
                }
            });
        }

        function deleteOffering(url, onSuccess) {
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
                        if (onSuccess) onSuccess();
                        window.location.href = window.location.pathname + '#classes';
                        window.location.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the Offering.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the Offering.',
                        'error'
                    );
                });
        }

        function setDefaultWaterfall() {
            const selectedDefaultWaterfall = document.getElementById('defaultWaterfall').value;
            if (!selectedDefaultWaterfall) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a waterfall to set as default.'
                });
                return;
            }

            const url = "{{ route($prefix . '.deals.waterfalls.default', $deal) }}";
            const data = {
                _token: '{{ csrf_token() }}',
                waterfall_id: selectedDefaultWaterfall
            };

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cosyAlert('<strong>Success</strong><br />Default waterfall set successfully!', 'success');
                        $('#setDefaultWaterfallModal').modal('hide');
                        window.location.reload();
                    } else {
                        cosyAlert('<strong>Error</strong><br />' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while setting the default waterfall.'
                    });
                });

        }

        function confirmAssetDelete(url, button) {
            Swal.fire({
                title: 'Delete Asset',
                text: "Are you sure you want to delete this Asset? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAsset(url, () => {
                        Swal.fire(
                            'Deleted!',
                            'Asset has been deleted successfully.',
                            'success'
                        );
                        // Remove the table row immediately.
                        let assetRow = button.closest('tr');
                        if (assetRow) {
                            // If Alpine is used, set its "show" value to false.
                            if (assetRow.__x) {
                                assetRow.__x.$data.show = false;
                            } else {
                                assetRow.remove();
                            }
                        }
                    });
                }
            });
        }

        function deleteAsset(url, onSuccess) {
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
                        if (onSuccess) onSuccess();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred while deleting the Asset.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the Asset.',
                        'error'
                    );
                });
        }
    </script>
@endpush
