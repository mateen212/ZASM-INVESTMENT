@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->

@push('style')
    <link href="{{ asset('assets/admin/plugins/summernote/summernote-bs5.min.css') }}" rel="stylesheet">
    <script defer src="{{ asset('assets/admin/plugins/summernote/summernote-bs5.min.js') }}"></script>
    <style>
        .secondary-assets {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            background-color: #f6f7f7;
        }

        .text-sm {
            font-size: .75rem;
        }

        .options {
            color: black;
        }

        .search-bar i {
            right: 10px;
            top: 50%;
            transform:
                translateY(-50%) scaleX(-1);
            pointer-events: none;
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
    </style>

    {{--  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />  --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
@endpush

@php
    if(auth('admin')->user()->hasRole('partner')){
        $prefix = 'partner';
    }else{
        $prefix = 'admin';
    }
@endphp

@section('panel')
    <div class="card">
        <div class="card-body">
            <div class="admin-offering-detail" x-data="offeringDetail()" x-cloak>

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
                        <li class="breadcrumbs-item "
                            onclick="window.location.href='{{ route($prefix . '.deals.offerings.offering_detail', [$deal->id, $offering->id]) }}'">
                            {{ $offering->name }}</li>
                    </ol>
                </nav>
                <hr>
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <h2 class="mb-0 fw-semibold" style="font-size: 24px;">{{ $offering->name }}</h2>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary" id="manage-deal"
                            onclick="window.location.href='{{ route($prefix . '.deals.offerings.offering_manage', [$deal->id, $offering->id]) }}'">
                            Manage Offering
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="manage-deal"
                            onclick="window.location.href='{{ route($prefix . '.deals.offerings.offering_preview', [$deal->id, $offering->id]) }}'">
                            Preview Offering
                        </button>
                    </div>
                </div>

                <!-- Offering Detail Boxes -->
                <div class="row text-center my-3">
                    <div class="col">
                        <div class="border p-3">
                            <p>Offering sights </p>
                            <h4>{{$offering->offering_size}}</h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p>Minimum investment </p>
                            @foreach ($offering->classes as $class)
                                <h4 value="{{ $class->id }}">
                                    {{ $class->minimum_investment }}</h4>
                            @endforeach
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p>SEC type</p>
                            <h4>{{ $deal->sec_type }}</h4>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border p-3">
                            <p>Status</p>
                            <h4>{{$offering->status_text}}</h4>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mb-3">

                </div>
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="deal-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#overviews" data-bs-toggle="tab">Overview</a></li>
                    <li class="nav-item"><a class="nav-link" href="#key_metrics" data-bs-toggle="tab">Key metrics</a></li>
                    <li class="nav-item"><a class="nav-link" href="#summarys" data-bs-toggle="tab">Summary</a></li>
                    <li class="nav-item"><a class="nav-link" href="#assets" data-bs-toggle="tab">Asset</a></li>
                    <li class="nav-item"><a class="nav-link" href="#offering_documents" data-bs-toggle="tab">Offering
                            Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="#e_sign_templates" data-bs-toggle="tab">E-sign
                            templates</a></li>
                    <li class="nav-item"><a class="nav-link" href="#funding_info" data-bs-toggle="tab">Funding info</a></li>
                    <li class="nav-item"><a class="nav-link" href="#insights" data-bs-toggle="tab">Insights</a></li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content mt-4">
                    {{--  Overview  --}}
                    <div class="tab-pane fade show active" id="overviews" role="tabpanel" aria-labelledby="overviews-tab">
                        <div>
                            <div class="row">
                                <div class="col-12">
                                    <p>Click <a href="#">here</a> to learn more about how to customize your investment
                                        funnel to fit your specific deal structure.</p>
                                    <div class="card shadow-sm p-4">
                                        <!-- Offering Page Link -->
                                        <div class="mb-3 row align-items-center">
                                            <label class="col-md-3 col-form-label">Offering page link</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control" {{-- value="{{ route('user.offerings.offering', $offering->id) }}" --}}
                                                    value="{{ route('public.offering', ['encryptedId' => $offering->uuid]) }}"
                                                    readonly>
                                                <button type="button" class="btn btn-outline-primary ms-2"
                                                    @click="copyToClipboard('{{ route('public.offering', ['encryptedId' => $offering->uuid]) }}')">Copy
                                                    link</button>
                                            </div>
                                        </div>

                                        <!-- Visibility -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Visibility <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select class="form-select" x-model="overviewForm.visibility"
                                                    @change="saveOfferingDetail()">
                                                    >
                                                    <option value="show_on_dashboard">Show on dashboard</option>
                                                    <option value="show_on_deal_investor_dashboard">Show on deal investor's
                                                        Dashboard</option>
                                                    <option value="only_visible_on_link">Only Visible to Link</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select class="form-select" x-model="overviewForm.status"
                                                    @change="saveOfferingDetail()">
                                                    <option value="1">Draft (hidden to investors only)</option>
                                                    <option value="2">Open to soft commits</option>
                                                    <option value="3">Open to hard commits</option>
                                                    <option value="4">Open to investments</option>
                                                    <option value="5">WaitList (New investment require approval)
                                                    </option>
                                                    <option value="6">Closed (No longer accepting investments)
                                                    </option>
                                                    <option value="7">Past (hidden)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <template x-if="overviewForm.status == 3">
                                            <!-- Offering Classes -->
                                            <div class="mb-3 row">
                                                <label class="col-md-3 col-form-label">Hard commited percentage (%) <span
                                                        class="text-danger">*</span></label>
                                                <div class="col-md-7">
                                                    <input type="number" min="1" max="100" step="0.1"
                                                        x-on:input="percentFormat(el)" class="form-control"
                                                        x-model="overviewForm.hard_committed_percent"
                                                        @input.debounce.500ms="
                                                        if (parseInt($event.target.value) > 100) {
                                                            $event.target.value = 100;
                                                        }
                                                        saveOfferingDetail()">
                                                    <small class="text-muted">This is the percentage of the offering that
                                                        has been hard committed. This will be used to calculate the
                                                        remaining amount available for investment.</small>
                                                </div>
                                            </div>
                                        </template>
                                        {{--  @foreach ($deal->offerings as $offering)
                                                <option value="{{ $class->id }}">
                                                {{ $class->equity_class_name }}</option>
                                            @endforeach  --}}

                                        <!-- Offering Name -->
                                        <div class="mb-3 row">
                                            <label for="name" class="col-md-3 col-form-label">Offering name <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" name="offering_name"
                                                    x-model="overviewForm.name" value="{{ $offering->name }}">
                                            </div>
                                        </div>

                                        <!-- Internal Name -->
                                        <div class="mb-3 row">
                                            <label for="type" class="col-md-3 col-form-label">Internal name</label>
                                            <div class="col-md-7">
                                                <input type="text" id="internal_name" name="internal_name"
                                                    class="form-control" x-model="overviewForm.internal_name"
                                                    @input.debounce.500ms="saveOfferingDetail()">
                                            </div>
                                        </div>

                                        {{-- TODO: Add multiselect in this field as tags --}}
                                        <!-- Offering Classes -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Offering classes <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select id="offering-classes"
                                                    class="form-select color-black js-example-basic-multiple"
                                                    name="offering_classes[]" multiple="multiple">
                                                    @foreach ($deal->classes as $class)
                                                        <option value="{{ $class->id }}">
                                                            {{ $class->equity_class_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Offering assets <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-7">
                                                <select id="offering-assets" class="form-select js-example-basic-multiple"
                                                    name="offering_assets[]" multiple="multiple">
                                                    @foreach ($deal->assets as $asset)
                                                        <option value="{{ $asset->id }}">
                                                            {{ $asset->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Offering Size -->
                                        <div class="mb-3 row">
                                            <label for="offering_size" class="col-md-3 col-form-label">Offering size <span
                                                    class="text-danger">*</span>($)</label>
                                            <div class="col-md-7">
                                                <input type="text" x-on:input="moneyFormat($el)" id="offering_size"
                                                    name="offering_size" class="form-control"
                                                    x-model="overviewForm.offering_size"
                                                    @input.debounce.500ms="saveOfferingDetail()">
                                            </div>
                                        </div>

                                        <!-- Minimum Investment -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Minimum Investment</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control"
                                                    value="{{ $class->minimum_investment }}" readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.class', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">price per unit</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control"
                                                    value="{{ $class->equity_class_name . '  |  ' . $class->price_per_unit }}"
                                                    readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.class', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <!-- Deal Type -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Deal type</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control" value="{{ $deal->type }}"
                                                    readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.edit', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <!-- SEC type -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">SEC type</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control" value="{{ $deal->sec_type }}"
                                                    readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.edit', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <!-- Investment type -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Investment type</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control"
                                                    value="{{ $offering->effective_investment_type }}" readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.class', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <!-- Close date -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Close date</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="text" class="form-control"
                                                    value="{{ $deal->close_date }}" readonly>
                                                <button type="button" class="btn btn-outline-secondary ms-2"
                                                    onclick="window.location.href='{{ route($prefix . '.deals.edit', [$deal->id]) }}'">Edit</button>
                                            </div>
                                        </div>

                                        <!-- Video URL -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Video URL</label>
                                            <div class="col-md-7 d-flex">
                                                <input type="url" class="form-control"
                                                    x-model="overviewForm.video_url"
                                                    @input.debounce.500ms="saveOfferingDetail()"
                                                    placeholder="Enter Youtube Or Vimeo Video Link">
                                            </div>
                                        </div>

                                        {{-- TODO: Need to add multiselect as tags in this input --}}
                                        <!-- Overview metrics -->
                                        <div class="mb-3 row">
                                            <label class="col-md-3 col-form-label">Overview metrics</label>
                                            <div class="col-md-7 d-flex">
                                                {{-- <input type="text" class="form-control" x-model="overviewForm.overview_metrics" @input.debounce.500ms="saveOfferingDetail()"> --}}
                                                <select id="overview-matrics" class="form-select" multiple>
                                                    <option value="Close date">Close date</option>
                                                    <option value="Deal type">Deal type</option>
                                                    <option value="Investment type">Investment type</option>
                                                    <option value="Offering size">Offering size</option>
                                                    <option value="Requested contribution">Requested contribution</option>
                                                    <option value="SEC type">SEC type</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-sm-3">
                                                <label class=" col-form-label">Public Offering</label>

                                            </div>
                                            <div class="col-sm-8">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="public_offering"
                                                        :value="1"
                                                        x-on:input="offeringErrors.public_offering = ''"
                                                        x-model="overviewForm.public_offering"
                                                        @input.debounce.500ms="saveOfferingDetail()">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="public_offering"
                                                        :value="0"x-on:input="offeringErrors.public_offering = ''"
                                                        x-model="overviewForm.public_offering"
                                                        @input.debounce.500ms="saveOfferingDetail()">
                                                    No
                                                </label>
                                            </div>
                                            <p>
                                                By selecting "Yes", your offering is made public and will be visible to all
                                                potential investors.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Key Metrics -->
                    <div class="tab-pane fade" id="key_metrics" role="tabpanel" aria-labelledby="key_metrics-tab">
                        <div x-data="KeyMetricFormHandler()" x-init="init()">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-outline-primary" @click="addKeyMetric()">+ Add Key Metric</button>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered mt-3" id="metrics-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Label</th>
                                            @foreach ($offering->classes as $class)
                                                <th>{{ $class->equity_class_name }}</th>
                                            @endforeach
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="metricTable">
                                        <template x-for="(metric, id) in metrics" :key="id">
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" x-model="metric.label"
                                                        :disabled="metric.canDel !== '1'">
                                                </td>
                                                @foreach ($offering->classes as $class)
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            x-model="metric[{{ $class->id }}]">
                                                    </td>
                                                @endforeach
                                                <td x-show="metric.canDel === '1'">
                                                    <i class="fas fa-trash" role="button" title="Delete Section"
                                                        @click="delKeySection(id)"></i>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3" @click="submitMetricForm()">Save</button>
                        </div>
                    </div>
                    {{--  Summary  --}}
                    <div class="tab-pane fade" id="summarys" role="tabpanel" aria-labelledby="summarys-tab">
                        {{--  <div class="container mt-5" x-data="wysiwygEditor()" x-init="initialize()">  --}}
                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="mb-4">Summary for all investors</h2>
                                    <div class="card shadow-sm p-4">
                                        <!-- Tip Section -->
                                        <div class="mb-3">
                                            <p class="text-muted">
                                                Tip: when writing a summary, keep in mind that bright colors are harder to
                                                read. Black text usually looks the most professional. Consider using bold
                                                text to draw attention to key points.
                                            </p>
                                        </div>

                                        <!-- WYSIWYG Toolbar -->
                                        <textarea id="offering-summary" class="summernote">
                                            {!! $offering->summary !!}
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-12 mt-4" x-show="overviewForm.secondary_summary_visibility">
                                    <h2 class="mb-4">Summary for logged in investors</h2>
                                    <div class="card shadow-sm p-4">
                                        <!-- Tip Section -->
                                        <div class="mb-3">
                                            <p class="text-muted">
                                                Tip: when writing a summary, keep in mind that bright colors are harder to
                                                read. Black text usually looks the most professional. Consider using bold
                                                text to draw attention to key points.
                                            </p>
                                        </div>
                                        <!-- WYSIWYG Toolbar -->
                                        <textarea id="offering-logged-summary" class="summernote">
                                            {!! $offering->logged_summary !!}
                                        </textarea>

                                    </div>
                                </div>
                                {{-- Create save button to the right side of the row --}}
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="summaryCheckbox"
                                            x-model="overviewForm.secondary_summary_visibility">
                                        <label for="summaryCheckbox" class="form-check-label">Show secondary summary to
                                            only logged in investors</label>
                                    </div>
                                    <button @click="saveSummary()" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--  Asset  --}}
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
                    {{--  Funding info  --}}
                    <div class="tab-pane fade" id="funding_info" role="tabpanel"aria-labelledby="offering_info-tab">
                        <div>
                            @include('admin.deals.offerings.offering_funding_info')
                        </div>
                    </div>
                    {{--  offering_documents  --}}
                    <div class="tab-pane fade" id="offering_documents" role="tabpanel"
                        aria-labelledby="offering_documents-tab">
                        @include('admin.deals.offerings.offering_documents')
                    </div>
                    {{--  Insights  --}}
                    <div class="tab-pane fade" id="insights" role="tabpanel" aria-labelledby="insights-tab">
                        @include('admin.deals.offerings.offering_insights')
                    </div>
                    {{--  E-sign templates  --}}
                    <div class="tab-pane fade" id="e_sign_templates" role="tabpanel"
                        aria-labelby="e_sign_templates-tab">
                        @include('admin.deals.offerings.offeringe_sign_templates')
                    </div>
                </div>
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
                                        <span class="btn btn-primary deal-save"
                                            @click="submitAssetForm(assetForm)">Save</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn {
            white-space: nowrap;
        }

        .square {
            position: relative;
            width: 100%;
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
        }

        .square img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .funding_method .btn-check+.btn {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            border-radius: 2rem;
            transition: background-color 0.2s;
        }

        .funding_method .btn-check:checked+.btn {
            background-color: #28a745;
            border-color: #28a745;
        }

        .funding-left {
            width: 30%;
        }

        .funding-right {
            width: 70%;
        }

        .funding {
            display: flex;
            direction: row;
        }
    </style>
@endsection

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/js/fundingInfo.js'])
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
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
        var newFundingForm = {
            receiving_bank: "",
            bank_address: '',
            routing_no: '',
            account_no: '',
            account_type: 'checking',
            beneficiary_account_name: '',
            beneficiary_address: '',
            reference_info: '',
            instruction_info: '',
            mail_address: '',
            mail_beneficiary: '',
            mail_beneficiary_address: '',
            mail_instructions: '',
            funding_methods: {
                wireTransfer: true,
                check: false,
                achPayment: false
            }
        }

        function offeringDetail() {
            return {
                ...alpineHelpers(),
                loading: false,
                errors: {},
                offeringErrors: {},
                overviewForm: {
                    visibility: "{{ $offering->visibility }}",
                    secondary_summary_visibility: ("{{ $offering->secondary_summary_visibility }}" === '1') ? true : false,
                    status: "{{ $offering->status }}",
                    hard_committed_percent: "{{ $offering->hard_committed_percent }}",
                    internal_name: "{{ $offering->internal_name }}",
                    public_offering: "{{ $offering->public_offering }}",
                    offering_classes: "{{ $offering->classes->pluck('id')->implode(',') }}",
                    offering_assets: "{{ $offering->assets->pluck('id')->implode(',') }}",
                    offering_size: "{{ $offering->offering_size }}",
                    video_url: "{{ $offering->video_url }}",
                    name: "{{ $offering->name }}",
                    overview_metrics: @json($offering->overview_metrics),
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
                assetErrors: {},
                fundingForm: (@json($offering->funding_info) == null) ? newFundingForm : @json($fundingInfo),
                assets: @json($offering->assets),
                insightForm: @json($offering->insight),
                assetImages: @json($assetImages),
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


                async saveOfferingDetail() {
                    this.errors = {};
                    this.loading = true;

                    // Validate Video URL (Allow only direct YouTube & Vimeo videos)
                    const url = this.overviewForm.video_url.trim();
                    const youtubePattern = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+/;
                    const vimeoPattern = /^(https?:\/\/)?(www\.)?(vimeo\.com\/)(\d+)/; // Matches only Vimeo video IDs

                    if (url && !youtubePattern.test(url) && !vimeoPattern.test(url)) {
                        cosyAlert('Only direct YouTube or Vimeo video links are allowed.', 'error');
                        this.loading = false;
                        return;
                    }

                    if (this.overviewForm.status == 3) {
                        if (this.overviewForm.hard_committed_percent == '' || this.overviewForm
                            .hard_committed_percent == null) {
                            // cosyAlert('Please enter hard commited percentage', 'error');
                            this.loading = false;
                            return;
                        }
                    }
                    await fetch("{{ route($prefix . '.deals.offerings.update', [$offering->deal->id, $offering->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify({
                                visibility: this.overviewForm.visibility,
                                status: this.overviewForm.status,
                                hard_committed_percent: this.overviewForm.hard_committed_percent,
                                internal_name: this.overviewForm.internal_name,
                                public_offering: this.overviewForm.public_offering,
                                name: this.overviewForm.name,
                                offering_classes: $('#offering-classes').val(),
                                offering_assets: $('#offering-assets').val(),
                                offering_size: this.removeMoneyFormat(this.overviewForm.offering_size),
                                video_url: this.overviewForm.video_url,
                                // overview_metrics: this.overviewForm.overview_metrics,
                                overview_metrics: $('#overview-matrics').val(),
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cosyAlert('Offering details updated successfully', 'success');
                            } else {
                                cosyAlert('Failed to update offering details', 'error');
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            if (error.response && error.response.status == 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.error('Error:', error);
                            }
                            this.loading = false;
                        });
                },

                async saveSummary() {
                    this.errors = {};
                    this.loading = true;
                    await fetch("{{ route($prefix . '.deals.offerings.update', [$offering->deal->id, $offering->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify({
                                visibility: this.overviewForm.visibility,
                                secondary_summary_visibility: this.overviewForm
                                    .secondary_summary_visibility,
                                status: this.overviewForm.status,
                                offering_size: this.overviewForm.offering_size,
                                name: this.overviewForm.name,
                                summary: $('#offering-summary').summernote('code'),
                                logged_summary: $('#offering-logged-summary').summernote('code')
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cosyAlert('Summary updated successfully', 'success');
                            } else {
                                cosyAlert('Failed to update summary', 'error');
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            if (error.response && error.response.status == 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.error('Error:', error);
                            }
                            this.loading = false;
                        });
                },
                removeMoneyFormat(value) {
                    return value.replace(/[^0-9.]/g, '');
                },
                addNewAsset() {
                    this.assets.push({
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
                        net_asset_value: '',
                        acquisition_price: '',
                        acquisition_date: '',
                        exit_price: '',
                        exit_date: '',
                        year_built: '',
                        year_renovated: '',
                        images: []
                    });
                },
                removeAsset(index) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to remove this asset?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.assets.splice(index, 1);
                        }
                    });
                },
                async updateAssets() {
                    this.loading = true;
                    let url = "{{ route($prefix . '.assets.update.offering', $offering->id) }}";
                    await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify({
                                assets: this.assets
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cosyAlert('Assets updated successfully', 'success');
                            } else {
                                cosyAlert('Failed to update assets', 'error');
                            }
                            this.loading = false;
                        }).catch(error => {
                            console.error('Error:', error);
                            this.loading = false;
                        });
                },
                async submitFundingForm() {
                    this.errors = {};
                    this.loading = true;
                    await fetch("{{ route($prefix . '.deals.offerings.funding', [$offering->deal->id, $offering->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify(this.fundingForm)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cosyAlert('Offering Funding Info updated successfully', 'success');
                            } else {
                                this.errors = data.error;
                                cosyAlert('Failed to update offering funding info', 'error');
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            if (error.response && error.response.status == 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.error('Error:', error);
                            }
                            this.loading = false;
                        });

                },
                copyToClipboard(text) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(function() {
                            cosyAlert('Copied to clipboard', 'success');
                        }, function(err) {
                            console.error('Async: Could not copy text: ', err);
                        });
                    } else {
                        console.error('Clipboard API not supported');
                    }
                },
                async submitInsightForm() {
                    this.loading = true;
                    await fetch("{{ route($prefix . '.deals.offerings.insight.update', [$offering->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify(this.insightForm)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cosyAlert('Offering Insights Info updated successfully', 'success');
                            } else {
                                this.errors = data.error;
                                cosyAlert('Failed to updtae offering insights info', 'error');
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            if (error.response && error.response.status == 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.error('Error:', error);
                            }
                            this.loading = false;
                        });

                },

            };
        }

        function KeyMetricFormHandler() {
            return {
                metrics: {},
                loading: false,
                init() {
                    @foreach ($offering->key_metrics as $key_metric)
                        this.metrics[{{ $key_metric->id }}] = {
                            id: "{{ $key_metric->id }}",
                            label: "{{ $key_metric->metric_label }}",
                            canDel: "{{ $key_metric->can_del }}",
                            @foreach ($offering->classes as $class)
                                {{ $class->id }}: "{{ $key_metric->classes()->where('deal_classes.id', $class->id)->first()?->pivot->value ?? '' }}",
                            @endforeach
                        };
                    @endforeach
                },
                // Add a new key metric
                addKeyMetric() {
                    const newMetricId = Date.now(); // Temporary ID
                    this.metrics[newMetricId] = {
                        label: '',
                        @foreach ($offering->classes as $class)
                            {{ $class->id }}: '',
                        @endforeach
                        canDel: '1'
                    };
                },
                // Delete a key metric
                delKeySection(id) {
                    const url = `{{ url('/admin/deals/offerings/' . $offering->id . '/delMetric') }}/${id}`;
                    console.log(url);
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                        })
                        .then((response) => {
                            if (response.ok) {
                                delete this.metrics[id];
                            } else {}
                        });
                },
                // Submit the form
                submitMetricForm() {
                    this.loading = true;
                    const payload = {
                        metrics: this.metrics
                    };
                    fetch("{{ route($prefix . '.deals.offerings.createMetric', [$offering->deal->id, $offering->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf // Assuming csrf is defined elsewhere in your script
                            },
                            body: JSON.stringify(payload),
                        })
                        .then((response) => {
                            if (response.ok) {

                            } else {}
                        });
                },
            };
        }
    </script>
    <script>
        // Jquery document load
        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                height: 300,
                placeholder: 'Write your summary here...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('.summerLogNote').summernote({
                height: 300,
                placeholder: 'Write your summary here...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Initialize Select2
            $('#offering-classes').select2({
                placeholder: 'Select Classes',
            });
            $('#offering-assets').select2({
                placeholder: 'Select Assets',
            });
            $('#overview-matrics').select2({
                placeholder: 'Select Metrics',
            });

            $('#offering-classes').val(
                @json($offering->classes->pluck('id'))
            ).trigger('change');

            $('#offering-assets').val(
                @json($offering->assets->pluck('id'))
            ).trigger('change');

            $('#overview-matrics').val(
                @json($offering->overview_metrics)
            ).trigger('change');

            $('#offering-classes').on('change', function() {
                let AlipineObj = document.querySelector('[x-data="offeringDetail()"]');
                AlipineObj._x_dataStack[0].saveOfferingDetail();
            });

            $('#offering-assets').on('change', function() {
                let AlipineObj = document.querySelector('[x-data="offeringDetail()"]');
                AlipineObj._x_dataStack[0].saveOfferingDetail();
            });
            $('#overview-matrics').on('change', function() {
                let AlipineObj = document.querySelector('[x-data="offeringDetail()"]');
                AlipineObj._x_dataStack[0].saveOfferingDetail();
            });
        });

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
