@extends('admin.layouts.app')

@push('style')
    <link rel="stylesheets" href="{{ asset('assets/admin/plugins/multipleselect/bootstrap-multiselect.min.css') }}"
        type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    <style>
        .custom-select {
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            background: none;
            border: none;
            border-bottom: 2px solid #007bff;
            padding: 5px 10px;
            font-size: 16px;
            font-weight: 500;
            color: #000;
            outline: none;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .custom-select:hover,
        .custom-select:focus {
            border-bottom-color: #071251;
        }


        .custom-select::-ms-expand {
            display: none;
        }

        .custom-select::-webkit-inner-spin-button,
        .custom-select::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .custom-select-icon {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .custom-select-icon::after {
            content: '';
            display: none;
        }
    </style>
    <style>
        /* Add your CSS styles here */
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #5897fb;
            color: black !important;
        }

        .class-info-block {
            'height': 50px;
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

        .w-full {
            width: 100%;
        }

        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .h64 {
            height: 16rem;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .inline-block {
            display: inline-block;
        }

        .relative {
            position: relative;
        }

        .my-2 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .p-1 {
            padding: 0.25rem;
        }

        border {
            border: 1px solid #e2e8f0;
        }

        .bg-white {
            background-color: #fff;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        .border-gray-200 {
            border-color: #edf2f7;

        }

        .flex-auto {
            flex: 1 1 auto;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        /* flex justify-center items-center m-1 font-medium py-1 px-2 rounded-full text-indigo-700 bg-indigo-100 border border-indigo-300  */
        .font-medium {
            font-weight: 500;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .text-indigo-700 {
            --tw-text-opacity: 1;
            color: rgba(79, 70, 229, var(--tw-text-opacity));
        }

        .bg-indigo-100 {
            --tw-bg-opacity: 1;
            background-color: rgba(224, 229, 245, var(--tw-bg-opacity));
        }

        .border-indigo-300 {
            --tw-border-opacity: 1;
            border-color: rgba(209, 213, 219, var(--tw-border-opacity));
        }

        .border {
            border-width: 1px;
        }

        .z-40 {
            z-index: 40;
        }

        .appearance-none {
            appearance: none;
            visibility: hidden;
        }

        .deal-class-box {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0.2, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .deal-class-box:hover {
            box-shadow: 0 5px 5px rgba(0.2, 0, 0, 0.3);
        }

        .main-content {
            box-shadow: 2px 1px 2px 1px rgba(0.2, 0.2, 0.2, 0.2);
            transition: box-shadow 0.3s ease;
        }

        .box-content-header {
            font-size: 12px;
        }

        .box-content-value {
            font-size: 16px;

        }

        .dropzone {
            border: 2px dashed rgb(109, 162, 219);
            cursor: pointer;
            transition: border-color 0.3s, color 0.3s;
        }

        .dropzone:hover {

            border-color: #0056b3;
            color: #0056b3;
        }

        .dropzone p {
            color: #007bff;
        }

        .header-1 {
            background-color: #F9F9F9;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .add-hurdle-link {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0056b3;
        }

        .add-hurdle-linked {
            font-size: 0.75rem;
            font-weight: bold;
            color: #627580;
            margin-left: 14px;
        }

        .add-hurdle-linked :hover {
            font-size: 1rem;
            font-weight: bold;
            margin-left: 14px;
        }

        .ml-10 {
            margin-left: 10px;
        }

        .class-hurdle-box {
            width: 100%;
            background-color: #F6F9FE;
            margin-top: 20px;
            border-left: 5px solid #0D6EFD;
            padding: 10px;
        }

        .hurdle-expanded {
            "background-color: #e8f7ff;
     height: 55px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .bucket-section-box {
            cursor: pointer;
            background-color: #e8f7ff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .ml-20 {
            margin-left: 20px;
        }

        .bucket-class-section-box {
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .bucket-class-hurdle-box {
            width: 100%;
            background-color: #F6F9FE;
            margin-top: 20px;
            border-left:
                5px solid #0D6EFD;
            padding: 10px;
        }

        .bucket-class-hurdle-header {
            cursor: pointer;
            background-color: #e8f7ff;
            height: 55px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .height-60 {
            height: 60px;
        }

        .height: {
            height: 80px;
        }

        .remove-icon-btn {
            cursor: pointer;
            color: red;
            transition: transform 0.2s ease;
        }

        .remove-icon-btn:hover {
            transform: scale(1.2);
        }

        .remove-icon-btn i {
            color: red;
            transition: transform 0.2s ease;
        }

        .remove-icon-btn i:hover {
            transform: scale(1.2);
        }

        .advance-btn {
            display: block;
            color: #8080f4;
            cursor: pointer;
            padding: 5px;
            margin: 10px 0px;
            font-size: 1rem;
            font-weight: 700;
        }

        .advance-btn:hover {
            display: block;
            color: blue;
            cursor: pointer;
            padding: 5px;
            margin: 10px 0px;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .hurdle-content-box {
            display: flex;
            align-items: center;
            gap: 2px;
            flex-wrap: wrap;
        }

        .xm-percent-inline {
            width: 80px;
            /* border: 0px; */
            border-top: 0px;
            border-left: 0px;
            border-right: 0px;
            /* margin-right: 2px; */
        }
    </style>
@endpush

@php
    if (auth('admin')->user()->hasRole('partner')) {
        $prefix = 'partner';
    } else {
        $prefix = 'admin';
    }
@endphp

@section('panel')
    <div class="edit-class" x-data="equityclass()" x-cloak>

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
                    onclick="window.location.href='{{ route($prefix . '.deals.summary', $deal->id) }}'">{{ $deal->name }}
                </li>
                <li class="breadcrumbs-items">></li>
                <li class="breadcrumbs-item">Edit Classes and Waterfalls</li>
            </ol>
        </nav>
        <hr>
        <div class="pb-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-center">
                        <a href="javascript:void(0);" class="btn btn-primary" onclick="window.history.back();">
                            <i class="las la-angle-left"></i> Back
                        </a>

                        <h5 class="ms-3">Edit Classes and Waterfalls</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Raise amount for ownership</h5>
                            <p class="card-text">$0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Raise amount for distribution</h5>
                            <p class="card-text">$0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total raised</h5>
                            <p class="card-text">$0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ownership percentage</h5>
                            <p class="card-text">0.00%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="buckets-classes-tab" data-bs-toggle="tab"
                    data-bs-target="#buckets-classes" type="button" role="tab" aria-controls="buckets-classes"
                    aria-selected="true">Buckets & Classes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="distribution-waterfalls-tab" data-bs-toggle="tab"
                    data-bs-target="#distribution-waterfalls" type="button" role="tab"
                    aria-controls="distribution-waterfalls" aria-selected="false">Distribution Waterfalls</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="buckets-classes" role="tabpanel"
                aria-labelledby="buckets-classes-tab">
                <!-- Content for Buckets & Classes -->
                <div class="card p-3 shadow-sm header-1">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Buckets & Classes</h5>
                        <div class="d-flex">
                            <button class="btn btn-primary me-2" @click="classes.push({...classForm})">Add New
                                Class</button>
                            <button class="btn btn-secondary" @click="buckets.push({...bucketForm})">Add New Bucket</button>
                        </div>
                    </div>
                    <!-- classes Section -->
                    <template x-for="(eqclass, index) in classes" :key="index">
                        <div class="card class-form-card main-content mb-4">
                            <div x-data="{ cexpanded: false }" class="card-body">
                                <div class="card-titles cursor-pointer class-header" @click="cexpanded = !cexpanded">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="d-flex deal-class-box  align-items-center">
                                                <div class=" text-primary cursor-pointer">
                                                    <i
                                                        :class="cexpanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                                </div>
                                                <template x-if="eqclass.equity_class_name == ''">
                                                    <h5 class="ms-3">New Class</h5>
                                                </template>
                                                <template x-if="eqclass.equity_class_name !== ''">
                                                    <div class="d-flex flex-column">
                                                        <h5 class="ms-3" x-text="eqclass.equity_class_name"></h5>
                                                        <template x-if="eqclass.class_type == 'LP'">
                                                            <a href="javascript:void(0);" class="add-hurdle-linked"
                                                                @click="addHurdle(index) ">+ Add Hurdle</a>
                                                        </template>
                                                    </div>
                                                </template>
                                                <div class="ms-auto d-flex align-items-center class-info-block">
                                                    <template
                                                        x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                        <div class="me-3">
                                                            <small class="box-content-header">Raise amount of
                                                                ownership</small>
                                                            <p class="mb-0 box-content-value"
                                                                x-text="(eqclass.raise_amount_ownership !== '') ? eqclass.raise_amount_ownership : '0'">
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                        <div class="me-3">
                                                            <small class="box-content-header">raise amount of
                                                                distribution</small>
                                                            <p class="mb-0 box-content-value"
                                                                x-text="(eqclass.raise_amount_distributions !== '') ? eqclass.raise_amount_distributions : '0'">
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'GP' || eqclass.class_type == 'Mezzanine'">
                                                        <div class="me-3">
                                                            <small class="box-content-header">Ownership of Entity</small>
                                                            <p class="mb-0 box-content-value"
                                                                x-text="(eqclass.entity_legal_ownership && eqclass.entity_legal_ownership !== '0') 
                                                            ? `${parseFloat(eqclass.entity_legal_ownership).toFixed(2)}%` : '0.00%'">
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'GP'">
                                                        <div>
                                                            <small class="box-content-header">Distribution Share</small>
                                                            <p class="mb-0 box-content-value"
                                                                x-text="(eqclass.distribution_share && eqclass.distribution_share !== '0') 
                                                            ? `${parseFloat(eqclass.distribution_share).toFixed(2)}%` : '0.00%'">
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                        <div class="ml-10">
                                                            <small class="box-content-header">Total Raised</small>
                                                            <p class="mb-0 box-content-value"
                                                                x-text="(eqclass.total_raised && eqclass.total_raised !== '0') 
                                                            ? `$${parseFloat(eqclass.total_raised).toFixed(2)}` : '$0'">
                                                            </p>
                                                        </div>
                                                    </template>
                                                    {{-- Delete Button --}}
                                                    <div class="ms-3">
                                                        <button class="btn remove-icon-btn" title="Delete Class"
                                                            @click="deleteConfirmation('class', null, index)">
                                                            <i class="las la-trash danger"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <div x-show="cexpanded" x-transition class="card-text row mt-4">
                                    <!-- Class Type -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="class_type">Class type <span class="text-danger">*</span></label>
                                            <select name="class_type" class="form-control custom-select"
                                                x-model="eqclass.class_type"
                                                @change="classTypeChanges('class', null, index)">
                                                <option value="">Select Class Type</option>
                                                <option value="Mezzanine">Mezzanine</option>
                                                <option value="GP">GP</option>
                                                <option value="LP">LP</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Equity Class Name -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="classCategory">Equity class name <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control"
                                                    aria-label="Text input with dropdown button"
                                                    x-model="eqclass.equity_class_name">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><span class="dropdown-item" role="button"
                                                            @click="eqclass.equity_class_name = 'Class A - Limited partners'">Class
                                                            A - Limited partners</span></li>
                                                    <li><span class="dropdown-item" role="button"
                                                            @click="eqclass.equity_class_name = 'Class B - General partners'">Class
                                                            B - General partners</span></li>
                                                    <li><span class="dropdown-item" role="button"
                                                            @click="eqclass.equity_class_name = 'Class C - Mezzanine'">Class
                                                            C - Mezzanine</span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Raise Amount for Ownership -->
                                    <template x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="raiseOwnership">Raise amount (for ownership) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" x-on:input="moneyFormat($el)" id="raiseOwnership"
                                                    name="raise_amount_ownership" class="form-control" placeholder="$0"
                                                    x-model="eqclass.raise_amount_ownership" x-init="$watch('eqclass.raise_amount_ownership', value => {
                                                        changeRaiseAmountOwner('class', null, index, value);
                                                        // Sync ownership and distributions when ownership changes
                                                        eqclass.raise_amount_distributions = value;
                                                    })">
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="raisedistributions">Raise amount (for distributions) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" x-on:input="moneyFormat($el)"
                                                    id="raisedistributions" name="raise_amount_distributions"
                                                    class="form-control" placeholder="$0"
                                                    x-model="eqclass.raise_amount_distributions" x-init="$watch('eqclass.raise_amount_distributions', value => {
                                                        // Ensure only distributions are updated when this field is changed
                                                        eqclass.raise_amount_ownership = eqclass.raise_amount_ownership; // No change to ownership
                                                    
                                                        // Update raise quota with the distribution amount
                                                        eqclass.raise_quota = value;
                                                    
                                                        // Make the raise quota readonly
                                                        eqclass.isRaiseQuotaReadonly = true;
                                                    })">
                                                <small class="form-text text-muted">Target amount, usually same as raise
                                                    amount for ownership.</small>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Raise Quota -->
                                    <template x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="raisequota">Raise quota (for billing)</label>
                                                <input type="text" x-on:input="moneyFormat($el)" id="raisequota"
                                                    name="raise_quota" class="form-control" placeholder="$0"
                                                    x-model="eqclass.raise_quota" :readonly="eqclass.isRaiseQuotaReadonly">
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Entity legal Ownership -->
                                    <template x-if="eqclass.class_type == 'GP' ">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="entitylegalownership">Entity legal Ownership <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" x-on:input="percentFormat($el)"
                                                    name="entity_legal_ownership" class="form-control" placeholder="%"
                                                    x-model="eqclass.entity_legal_ownership">
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Preferred return type -->
                                    <template x-if="eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="preferredreturntype">Preferred return type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="preferred_return_type"
                                                    x-model="eqclass.preferred_return_type">
                                                    <option value="">Select a preferred return type</option>
                                                    <option value="average_return">Average anual return</option>
                                                    <option value="cash_on_cash">Cash on cash</option>
                                                    <option value="irr">IRR</option>
                                                    <option value="roi">ROI</option>
                                                </select>
                                            </div>
                                        </div>
                                    </template>
                                    <template
                                        x-if="eqclass.preferred_return_type == 'irr' || eqclass.preferred_return_type == 'cash_on_cash' || eqclass.preferred_return_type == 'average_return' && eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <label for="preferred_return" class="form-label">Preferred return (%)</label>
                                            <input type="text" x-on:input="percentFormat($el)" placeholder="%"
                                                id="preferred_return" class="form-control"
                                                x-model="eqclass.preferred_return">
                                        </div>
                                    </template>
                                    <template
                                        x-if="eqclass.preferred_return_type == 'irr' || eqclass.preferred_return_type == 'cash_on_cash' || eqclass.preferred_return_type == 'average_return' && eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">

                                            <label for="preferred_return_accrues_on">Preferred return accrues on<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="preferred_return_accrues_on"
                                                x-model="eqclass.preferred_return_accrues_on">
                                                <option value="capital_balance">Capital balance (most common)</option>
                                                <option value="invested_amount">Invested amount</option>
                                            </select>
                                        </div>
                                    </template>
                                    <template
                                        x-if="eqclass.preferred_return_type == 'irr' || eqclass.preferred_return_type == 'cash_on_cash' || eqclass.preferred_return_type == 'average_return' && eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <label for="day_count">Day count convention<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="day_count" x-model="eqclass.day_count">
                                                <option value="actual_365">Actual/365 (most common)</option>
                                                <option value="actual_actual">Actual/Actual</option>
                                                <option value="actual_360">Actual/360</option>
                                                <option value="30_365">30/365</option>
                                                <option value="30_360">30/360</option>
                                            </select>
                                        </div>
                                    </template>
                                    <template
                                        x-if="eqclass.preferred_return_type == 'cash_on_cash' || eqclass.preferred_return_type == 'average_return' && eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <label for="start_date" class="form-label">Start date override</label>
                                            <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                id="start_date" class="form-control"
                                                placeholder="Enter override start date" x-model="eqclass.start_date">
                                        </div>
                                    </template>
                                    <template
                                        x-if="eqclass.preferred_return_type == 'cash_on_cash' || eqclass.preferred_return_type == 'average_return' && eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <label for="end_date" class="form-label">End date</label>
                                            <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                id="end_date" class="form-control" placeholder="Enter an end date"
                                                x-model="eqclass.end_date">
                                        </div>
                                    </template>
                                    <!-- Minimum Investment -->
                                    <template x-if="eqclass.class_type == 'LP'  || eqclass.class_type == 'Mezzanine'">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="mininvestment">Minimum investment <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="minimum_investment"
                                                    x-on:input="moneyFormat($el)" class="form-control" placeholder="$0"
                                                    x-model="eqclass.minimum_investment">
                                            </div>
                                        </div>
                                    </template>



                                    <div x-data="{ expanded: false }">
                                        <div class="d-flex align-items-center cursor-pointer"
                                            @click="expanded = ! expanded">
                                            <i :class="expanded ? 'bi bi-caret-down-fill text-primary' :
                                                'bi bi-caret-right-fill text-primary'"
                                                class="me-1"></i>
                                            <span class="advance-btn fw-bold">Advanced</span>
                                        </div>
                                        <div class="row" x-show="expanded" x-collapse>
                                            {{--  investment type  --}}
                                            <div class="col-md-4 d-flex flex-column">
                                                <div class="form-group">
                                                    <label for="investmenttype">Investment type <span
                                                            class="text-danger">*</span></label>
                                                    <select name="investment_type" id="investmenttype"
                                                        class="form-control custom-select"
                                                        x-model="eqclass.investment_type"
                                                        :disabled="eqclass.class_type == 'Mezzanine'">
                                                        <option value="">Select Investment Type</option>"
                                                        <option value="equity">Equity</option>
                                                        <option value="debt">Debt</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{--  distribution Share  --}}
                                            <template x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'GP'">
                                                <div class="col-md-4"class="col-md-4 d-flex flex-column">
                                                    <div class="form-group">
                                                        <label for="distributionShare">Distribution Share <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" x-on:input="percentFormat($el)"
                                                            name="distribution_share" class="form-control custom select"
                                                            placeholder="%" x-model="eqclass.distribution_share">
                                                    </div>
                                                </div>
                                            </template>
                                            {{--  Entity legal ownership  --}}
                                            <template x-if="eqclass.class_type == 'LP'">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="entitylegalownership">Entity legal Ownership <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" x-on:input="percentFormat($el)"
                                                                name="entity_legal_Ownership" class="form-control"
                                                                placeholder="%" x-model="eqclass.entity_legal_ownership">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            {{--  Maximum investment  --}}
                                            <template
                                                x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4 d-flex flex-column">
                                                    <div class="form-group">
                                                        <label for="maximuinvestment">Maximum investment</label>
                                                        <input type="text" x-on:input="moneyFormat($el)"
                                                            id="maximuinvestment" name="maximum_investment"
                                                            class="form-control custom select" placeholder="$0"
                                                            x-model="eqclass.maximum_investment">
                                                    </div>
                                                </div>
                                            </template>
                                            {{--  Price per unit  --}}
                                            <template
                                                x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4 d-flex flex-column">
                                                    <div class="form-group">
                                                        <label for="priceperunit">Price per unit <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" x-on:input="moneyFormat($el)"
                                                            id="priceperunit" name="price_per_unit"
                                                            class="form-control custom select" placeholder="$0"
                                                            x-model="eqclass.price_per_unit">
                                                    </div>
                                                </div>
                                            </template>
                                            {{--  Target IRR  --}}
                                            <template
                                                x-if="eqclass.class_type == 'LP' || eqclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4 d-flex flex-column">
                                                    <div class="form-group">
                                                        <label for="targetirr">Target IRR <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" x-on:input="percentFormat($el)"
                                                            name="target_irr" class="form-control custom select"
                                                            placeholder="%" x-model="eqclass.target_irr">
                                                    </div>
                                                </div>
                                            </template>

                                            {{--  Pref return start date  --}}
                                            <div class="col-md-4 d-flex flex-column">
                                                <div class="form-group">
                                                    <label for="prefreturnstartdate">Pref return start date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                        class="form-control custom select"
                                                        x-model="eqclass.pref_return_start_date">
                                                </div>
                                            </div>

                                            <div class="col-md-4 d-flex flex-column">
                                                <div class="form-group">
                                                    <label for="waitliststatus">Waitlist status <span
                                                            class="text-danger">*</span></label>
                                                    <select name="waitlist_status" id="waitliststatus"
                                                        class="form-control custom-select"
                                                        x-model="eqclass.waitlist_status">
                                                        <option value="0">Off (most common)</option>
                                                        <option value="1">ON (new Investments require Approval)
                                                        </option>
                                                        <option value="2">Automatic (enabled when target raise met)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Hurdles Block --}}
                                    {{-- Only show when equtity class type is 'LP' --}}
                                    <template x-if="eqclass.class_type == 'LP'">
                                        <div class="class-hurdle-box">
                                            <!-- Text Link to Show the Form -->
                                            {{--  <button class="btn mt-1 btn-primary h-6 " @click="if (eqclass.hurdles.length === 0) { eqclass.hurdles.push({...newHurdle}); } expanded = !expanded">+ Add hurdle & Show Hurdles</button>  --}}
                                            <!-- Hurdles Section -->
                                            <div id="hurdlesContainer" class="mt-3">
                                                <template x-for="(hurdle, hur_index) in eqclass.hurdles"
                                                    :key="`hur-${hur_index}`">
                                                    <div x-data="{ hexpanded: false }"
                                                        class="hurdle-section border p-3 rounded bg-light mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3 hurdle-expanded"
                                                            @click="hexpanded = ! hexpanded">
                                                            <div class="container mt-4">
                                                                <div class="row border-bottom pb-3">
                                                                    <div class="col d-flex">
                                                                        <div class="cursor-pointer  text-primary">
                                                                            <i
                                                                                :class="hexpanded ? 'bi bi-caret-down-fill' :
                                                                                    'bi bi-caret-right-fill'"></i>
                                                                        </div>
                                                                        <template x-if="hurdle.hurdle_name !== ''">
                                                                            <h5 class="ms-3"
                                                                                x-text="hurdle.hurdle_name"></h5>
                                                                        </template>
                                                                    </div>
                                                                    {{-- <div class="col">
                                                                    
                                                                </div> --}}
                                                                    <div class="col ">
                                                                        <div class="text-secondary small d-flex">Upside
                                                                            split &nbsp; <p
                                                                                x-text="`${Number(hurdle.upside_split.replace('%',''))}% / ${(100 - Number(hurdle.upside_split.replace('%','')))}%  `">
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="text-secondary small d-flex">Limit
                                                                            &nbsp; <p x-text="hurdle.upside_limit">
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="text-secondary small d-flex">Type:
                                                                            &nbsp;
                                                                            <p
                                                                                x-text="getNameTitle(hurdle.preferred_return_type)">
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Delete Hurdle -->
                                                            <button class="btn remove-icon-btn" title="Remove Hurdle"
                                                                @click="eqclass.hurdles.splice(hur_index, 1)">
                                                                <i class="las la-trash danger"></i>
                                                            </button>
                                                        </div>

                                                        <div x-show="hexpanded" x-collapse class="row">


                                                            <div class="mb-3 row align-items-center">
                                                                <div
                                                                    class="hurdle-content-box d-flex flex-wrap align-items-center gap-2 bg-light p-3 rounded shadow-sm">
                                                                    <!-- Upside Split Label & Input -->
                                                                    <label for="upside_split"
                                                                        class="fw-bold text-primary p-2 rounded shadow-sm">Upside
                                                                        Split</label>
                                                                    <input type="text" class="xm-percent-inline"
                                                                        id="upside_split" placeholder="100"
                                                                        x-on:input="percentFormat($el); checkGPRequiredError()"
                                                                        x-model="hurdle.upside_split">

                                                                    <!-- Error Message -->
                                                                    <span x-show="errors.upside_split"
                                                                        class="text-danger ms-2"
                                                                        x-text="errors.upside_split"></span>

                                                                    <!-- Conditional Display for Equity Class -->
                                                                    <template x-if="eqclass.equity_class_name">
                                                                        <span class="ms-2">to <strong
                                                                                class="text-primary"
                                                                                x-text="eqclass.equity_class_name"></strong>,</span>
                                                                    </template>

                                                                    <!-- Read-only Field with Negative Value -->
                                                                    <template x-if="gpClassName">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <input type="text"
                                                                                class="xm-percent-inline" readonly
                                                                                :value="`-${(100 - Number(hurdle.upside_split.replace('%', '')))}%`">
                                                                            <span>to</span>
                                                                            <h6 class="text-primary mb-0"
                                                                                x-text="gpClassName"></h6>
                                                                        </div>
                                                                    </template>

                                                                    <!-- Until Condition -->
                                                                    <span class="ms-2">, until</span>
                                                                    <template x-if="eqclass.equity_class_name">
                                                                        <h6 class="ms-1 text-primary mb-0"
                                                                            x-text="eqclass.equity_class_name"></h6>
                                                                    </template>

                                                                    <!-- Upside Limit Input -->
                                                                    <span>achieves</span>
                                                                    <input type="text" class="xm-percent-inline"
                                                                        id="upside_limit" placeholder="100"
                                                                        x-on:input="percentFormat($el)"
                                                                        x-model="hurdle.upside_limit">

                                                                    <!-- Preferred Return Type -->
                                                                    <span
                                                                        x-text="getNameTitle(hurdle.preferred_return_type)"></span>
                                                                    <span>return.</span>
                                                                </div>
                                                            </div>



                                                            <div class="col-md-4 d-flex flex-column">
                                                                <label for="hurdle_name"
                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Hurdle
                                                                    Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="hurdle_name" placeholder="Enter hurdle name"
                                                                    x-model="hurdle.hurdle_name">
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-column">
                                                                <label for="preferred_return_type"
                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Preferred
                                                                    Return Type <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="preferred-return-type"
                                                                    x-model="hurdle.preferred_return_type">
                                                                    <option value="">Select preferred return type
                                                                    </option>
                                                                    <option value="aar">Average Anual return</option>
                                                                    <option value="cash_on_cash">Cash on cash</option>
                                                                    <option value="irr">IRR</option>
                                                                    <option value="roi">ROI</option>
                                                                    <option value="return_of_capital">Return Of Capital
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-column">
                                                                <label for="final_hurdle"
                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Final
                                                                    Hurdle <span class="text-danger">*</span></label>
                                                                <select class="form-control" id="final_hurdle"
                                                                    x-model="hurdle.final_hurdle">
                                                                    <option value="no">No</option>
                                                                    <option value="yes">Yes</option>
                                                                </select>
                                                            </div>
                                                            <div x-data="{ expanded: false }">
                                                                <div class="d-flex align-items-center cursor-pointer"
                                                                    @click="expanded = ! expanded">
                                                                    <i :class="expanded ? 'bi bi-caret-down-fill text-primary' :
                                                                        'bi bi-caret-right-fill text-primary'"
                                                                        class="me-1"></i>
                                                                    <span class="advance-btn fw-bold">Advanced</span>
                                                                </div>
                                                                <div class="row" x-show="expanded" x-collapse>
                                                                    <template
                                                                        x-if="hurdle.preferred_return_type !== 'irr'">
                                                                        <div class="col-md-4 d-flex flex-column">
                                                                            <label for="catch_up"
                                                                                class="font-weight-bold text-dark bg-light p-2 rounded">Catch
                                                                                up on preferred returns<span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control" id="catch_up"
                                                                                x-model="hurdle.catch_up">
                                                                                <option value="yes">Yes</option>
                                                                                <option value="no">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </template>
                                                                    <div class="col-md-4 d-flex flex-column">
                                                                        <label for="honor_only"
                                                                            class="font-weight-bold text-dark bg-light p-2 rounded">Honor
                                                                            only on capital event<span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-control" id="honor_only"
                                                                            x-model="hurdle.honor_only">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                    <template
                                                                        x-if="hurdle.preferred_return_type !== 'roi' && hurdle.preferred_return_type !== 'return_of_capital'">
                                                                        <div class="col-md-4 d-flex flex-column">
                                                                            <label for="preferred_return"
                                                                                class="font-weight-bold text-dark bg-light p-2 rounded">Preferred
                                                                                return accrues on<span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control"
                                                                                id="preferred_return"
                                                                                x-model="hurdle.preferred_return">
                                                                                <option value="capital_balance">Capital
                                                                                    balance (most common)</option>
                                                                                <option value="invested_amount">Invested
                                                                                    amount</option>
                                                                            </select>
                                                                        </div>
                                                                    </template>
                                                                    <template
                                                                        x-if="hurdle.preferred_return_type !== 'roi' && hurdle.preferred_return_type !== 'return_of_capital'">
                                                                        <div class="col-md-4 d-flex flex-column">
                                                                            <label for="day_count"
                                                                                class="font-weight-bold text-dark bg-light p-2 rounded">Day
                                                                                count convention<span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-control" id="day_count"
                                                                                x-model="hurdle.day_count">
                                                                                <option value="actual_365">Actual/365 (most
                                                                                    common)</option>
                                                                                <option value="actual_actual">Actual/Actual
                                                                                </option>
                                                                                <option value="actual_360">Actual/360
                                                                                </option>
                                                                                <option value="30_365">30/365</option>
                                                                                <option value="30_360">30/360</option>
                                                                            </select>
                                                                        </div>
                                                                    </template>
                                                                    <template
                                                                        x-if="hurdle.preferred_return_type == 'aar' || hurdle.preferred_return_type == 'cash_on_cash'">
                                                                        <div class="row">
                                                                            <!-- Start date override -->
                                                                            <div class="col-md-6 mb-3">
                                                                                <label for="start_date"
                                                                                    class="form-label text-dark bg-light p-2 rounded">Start
                                                                                    date override</label>
                                                                                <input type="date"
                                                                                    onclick="this.showPicker()"
                                                                                    max="2999-12-31" id="start_date"
                                                                                    class="form-control"
                                                                                    placeholder="Enter override start date"
                                                                                    x-model="hurdle.start_date">
                                                                            </div>

                                                                            <!-- End date -->
                                                                            <div class="col-md-6 mb-3">
                                                                                <label for="end_date"
                                                                                    class="form-label text-dark bg-light p-2 rounded">End
                                                                                    date</label>
                                                                                <input type="date"
                                                                                    onclick="this.showPicker()"
                                                                                    max="2999-12-31" id="end_date"
                                                                                    class="form-control"
                                                                                    placeholder="Enter an end date"
                                                                                    x-model="hurdle.end_date">
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <a href="javascript:void(0);" class="add-hurdle-link"
                                                    @click="addHurdle(index)"
                                                    x-show="!eqclass.hurdles.length || eqclass.hurdles[eqclass.hurdles.length - 1].final_hurdle !== 'yes'">Add
                                                    Hurdle</a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                </div>
                </template>
                {{--  buckets section  --}}
                <template x-for="(eqbucket, b_index) in buckets" :key="b_index">
                    <div class="card class-form-card border border-primary main-content mb-4">
                        <div x-data="{ expanded: false }" class="card-body">
                            <div class="card-title class-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex bucket-section-box align-items-center"
                                            @click="expanded = !expanded">
                                            <div class=" text-primary">
                                                <i
                                                    :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                            </div>
                                            <div>
                                                <h5 class="ms-3">
                                                    <span
                                                        x-text="eqbucket.equity_bucket_name ? eqbucket.equity_bucket_name : 'New Bucket'"></span>
                                                </h5>
                                                <a class="add-hurdle-linked me-2 ml-20 mt-2"
                                                    @click="eqbucket.classes.push({...classForm})">
                                                    + Add Class
                                                </a>
                                            </div>

                                            <div class="ms-auto d-flex align-items-center class-info-block">
                                                <div>
                                                    <div class="me-3 ">
                                                        <small class="box-content-header">Raise amount of ownership</small>
                                                        <p class="mb-0 box-content-value"
                                                            x-text="(eqbucket.raise_amount_ownership !== '') ? eqbucket.raise_amount_ownership : '0'">
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="me-3 ">
                                                        <small class="box-content-header">raise amount of
                                                            distribution</small>
                                                        <p class="mb-0 box-content-value"
                                                            x-text="(eqbucket.raise_amount_distributions !== '') ? eqbucket.raise_amount_distributions : '0'">
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="me-3 ">
                                                        <small class="box-content-header">Ownership of Entity</small>
                                                        <p class="mb-0 box-content-value"
                                                            x-text="(eqbucket.entity_legal_ownership && eqbucket.entity_legal_ownership !== '0') 
                                                            ? `${parseFloat(eqbucket.entity_legal_ownership).toFixed(2)}%` : '0.00%'">
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="me-3 ">
                                                        <small class="box-content-header">Distribution Share</small>
                                                        <p class="mb-0 box-content-value"
                                                            x-text="(eqbucket.distribution_share && eqbucket.distribution_share !== '0') 
                                                            ? `${parseFloat(eqbucket.distribution_share).toFixed(2)}%` : '0.00%'">
                                                        </p>
                                                    </div>
                                                </div>
                                                {{-- Delete Button --}}
                                                <div class="ms-3">
                                                    <button class="btn remove-icon-btn" title="Delete Class"
                                                        @click="deleteConfirmation('bucket', b_index, null, null)">
                                                        <i class="las la-trash danger"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @csrf
                            <div x-show="expanded" x-transition>
                                <div class="card-text row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="classCategory">Equity bucket name <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control"
                                                    aria-label="Text input with dropdown button"
                                                    x-model="eqbucket.equity_bucket_name">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><span class="dropdown-item" role="button"
                                                            @click="eqbucket.equity_bucket_name = 'Bucket '+ (b_index+1) +'  - Limited partners'"
                                                            x-text="'Bucket '+ (b_index+1) +'  - Limited partners'"></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="raiseOwnership">Raise amount (for ownership) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" x-on:input="moneyFormat($el)" id="raiseOwnership"
                                                name="raise_amount_ownership" class="form-control" placeholder="$0"
                                                x-model="eqbucket.raise_amount_ownership">
                                        </div>
                                    </div>

                                    <!-- Raise Amount for Distributions -->

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="raisedistributions">Raise amount (for distributions) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" x-on:input="moneyFormat($el)" id="raisedistributions"
                                                name="raise_amount_distributions" class="form-control" placeholder="$0"
                                                x-model="eqbucket.raise_amount_distributions">
                                            <small class="form-text text-muted">Target amount, usually same as raise amount
                                                for ownership.</small>
                                        </div>
                                    </div>
                                    {{--  raising quota  --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="raisequota">Raise quota (for billing)</label>
                                            <input type="text" x-on:input="moneyFormat($el)" id="raisequota"
                                                name="raise_quota" class="form-control" placeholder="$0"
                                                x-model="eqbucket.raise_quota">
                                        </div>
                                    </div>
                                </div>
                                <div x-data="{ expanded: false }">
                                    <div class="d-flex align-items-center cursor-pointer" @click="expanded = ! expanded">
                                        <i :class="expanded ? 'bi bi-caret-down-fill text-primary' :
                                            'bi bi-caret-right-fill text-primary'"
                                            class="me-1"></i>
                                        <span class="advance-btn fw-bold">Advanced</span>
                                    </div>
                                    <div class="row" x-show="expanded" x-collapse>
                                        {{--  distribution Share  --}}
                                        <div class="col-md-4"class="col-md-4 d-flex flex-column">
                                            <div class="form-group">
                                                <label for="distributionShare">Distribution Share <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" max="100" x-on:input="percentFormat($el)"
                                                    id="bucket-distributionShare" name="distribution_share"
                                                    class="form-control custom select" placeholder="%"
                                                    x-model="eqbucket.distribution_share">
                                            </div>
                                        </div>
                                        {{--  Entity legal ownership  --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="entitylegalownership">Entity legal Ownership <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" x-on:input="percentFormat($el)"
                                                    name="entity_legal_Ownership" class="form-control" placeholder=""
                                                    x-model="eqbucket.entity_legal_ownership">
                                            </div>
                                        </div>
                                        {{--  waitlist status  --}}
                                        <div class="col-md-4 d-flex flex-column">
                                            <div class="form-group">
                                                <label for="waitliststatus">Waitlist status <span
                                                        class="text-danger">*</span></label>
                                                <select name="waitlist_status" id="waitliststatus"
                                                    class="form-control custom-select" x-model="eqbucket.waitlist_status">
                                                    <option value="0">Off (most common)</option>
                                                    <option value="1">ON (new Investments require Approval)</option>
                                                    <option value="2">Automatic (enabled when target raise met)
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- bucket classes Section -->
                            <template x-for="(eqbclass, bc_index) in eqbucket.classes" :key="bc_index">
                                <div class="card class-form-card main-content mb-4">
                                    <div x-data="{ expanded: false }" class="card-body">
                                        <div class="card-titles bucket-class-section-box class-header"
                                            @click="expanded = !expanded">
                                            <div class="row">
                                                <div class="col-md-12 deal-class-box">
                                                    <div class="d-flex align-items-center">
                                                        <div class=" text-primary cursor-pointer">
                                                            <i
                                                                :class="expanded ? 'bi bi-caret-down-fill' :
                                                                    'bi bi-caret-right-fill'"></i>
                                                        </div>
                                                        <template x-if="eqbclass.equity_class_name == ''">
                                                            <h5 class="ms-3">New Class</h5>
                                                        </template>
                                                        <template x-if="eqbclass.equity_class_name !== ''">
                                                            <div class="d-flex flex-column">
                                                                <h5 class="ms-3" x-text="eqbclass.equity_class_name">
                                                                </h5>
                                                                <template x-if="eqbclass.class_type == 'LP'">
                                                                    <a href="javascript:void(0);"
                                                                        class="add-hurdle-linked"
                                                                        @click="addBclassHurdle(b_index, bc_index)">+ Add
                                                                        Hurdle</a>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <div class="ms-auto d-flex align-items-center class-info-block">
                                                            {{--  <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                                <div class="me-3">
                                                                    <small class="box-content-header">Raise amount of ownership</small>
                                                                    <p class="mb-0" box-content-value" x-text="(eqbclass.raise_amount_ownership !== '') ? eqbclass.raise_amount_ownership : '0'"></p>
                                                                </div>
                                                            </template>
                                                            <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                                <div class="me-3">
                                                                    <small class="box-content-header">raise amount of distribution</small>
                                                                    <p class="mb-0" box-content-value" x-text="(eqbclass.raise_amount_distributions !== '') ? eqbclass.raise_amount_distributions : '0'"></p>
                                                                </div>
                                                            </template>
                                                            <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'GP' || eqbclass.class_type == 'Mezzanine'">
                                                                <div class="me-3">
                                                                    <small class="box-content-header">Ownership of Entity</small>
                                                                    <p class="mb-0" box-content-value" x-text="(eqbclass.entity_legal_ownership !== '') ? eqbclass.entity_legal_ownership : '0'"></p>
                                                                </div>
                                                            </template>
                                                            <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'GP'">
                                                                <div>
                                                                    <small class="box-content-header">Distribution Share</small>
                                                                    <p class="mb-0" box-content-value" x-text="(eqbclass.distribution_share !== '') ? eqbclass.distribution_share : '0'"></p>
                                                                </div>
                                                            </template>
                                                            <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                                <div>
                                                                    <small class="box-content-header">Total raised</small>
                                                                    <p class="mb-0" box-content-value" x-text="(eqbclass.total_raised !== '0') ? eqbclass.total_raised : '0'" placeholder="$0">0</p>
                                                                </div>
                                                            </template>  --}}
                                                            {{-- Delete Button --}}
                                                            <div class="ms-3">
                                                                <button class="btn remove-icon-btn" title="Delete Class"
                                                                    @click="deleteConfirmation('bucket_class', b_index, bc_index)">
                                                                    <i class="las la-trash danger"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @csrf
                                        <div x-show="expanded" x-transition class="card-text row mt-4">
                                            <!-- Class Type -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="class_type">Class type <span
                                                            class="text-danger">*</span></label>
                                                    <select name="class_type" class="form-control custom-select"
                                                        x-model="eqbclass.class_type"
                                                        @change="classTypeChanges('bucket_class', b_index, bc_index)">
                                                        <option value="">Select Class Type</option>
                                                        <option value="LP">LP</option>
                                                        <option value="Mezzanine">Mezzanine</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Equity Class Name -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="classCategory">Equity class name <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control"
                                                            aria-label="Text input with dropdown button"
                                                            x-model="eqbclass.equity_class_name">
                                                        <button class="btn btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false"></button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><span class="dropdown-item" role="button"
                                                                    @click="eqbclass.equity_class_name = 'Class A - Limited partners'">Class
                                                                    A - Limited partners</span></li>
                                                            <li><span class="dropdown-item" role="button"
                                                                    @click="eqbclass.equity_class_name = 'Class B - General partners'">Class
                                                                    B - General partners</span></li>
                                                            <li><span class="dropdown-item" role="button"
                                                                    @click="eqbclass.equity_class_name = 'Class C - Mezzanine'">Class
                                                                    C - Mezzanine</span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Raise Amount for Ownership -->
                                            {{--  <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="raiseOwnership">Raise amount (for ownership) <span class="text-danger">*</span></label>
                                                        <input type="number" x-mask:dynamic="$money($input)" id="raiseOwnership" name="raise_amount_ownership" class="form-control" placeholder="$0" x-model="eqbclass.raise_amount_ownership">
                                                    </div>
                                                </div>
                                            </template>  --}}
                                            <!-- Raise Amount for Distributions -->
                                            {{--  <template x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="raisedistributions">Raise amount (for distributions) <span class="text-danger">*</span></label>
                                                        <input type="number" x-mask:dynamic="$money($input)" id="raisedistributions" name="raise_amount_distributions" class="form-control" placeholder="$0" x-model="eqbclass.raise_amount_distributions">
                                                        <small class="form-text text-muted">Target amount, usually same as raise amount for ownership.</small>
                                                    </div>
                                                </div>
                                            </template>  --}}
                                            <!-- Entity legal Ownership -->
                                            {{--  <template x-if="eqbclass.class_type == 'GP' ">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="entitylegalownership">Entity legal Ownership <span class="text-danger">*</span></label>
                                                        <input type="number" x-mask:dynamic="$money($input)" id="entitylegalownership" name="entity_legal_ownership" class="form-control" placeholder="" x-model="eqbclass.entity_legal_ownership">
                                                    </div>
                                                </div>
                                            </template>  --}}
                                            <!-- Preferred return type -->
                                            <template x-if="eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="preferredreturntype">Preferred return type <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control" id="preferred_return_type"
                                                            x-model="eqbclass.preferred_return_type">
                                                            <option value="">Select a preferred return type</option>
                                                            <option value="average_return">Average anual return</option>
                                                            <option value="cash_on_cash">Cash on cash</option>
                                                            <option value="irr">IRR</option>
                                                            <option value="roi">ROI</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </template>
                                            <template
                                                x-if="eqbclass.preferred_return_type == 'irr' || eqbclass.preferred_return_type == 'cash_on_cash' || eqbclass.preferred_return_type == 'average_return'  || eqbclass.preferred_return_type == 'roi' && eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <label for="preferred_return" class="form-label">Preferred return
                                                        (%)</label>
                                                    <input type="text" x-on:input="percentFormat($el)" placeholder="%"
                                                        id="preferred_return" class="form-control"
                                                        x-model="eqbclass.preferred_return">
                                                </div>
                                            </template>
                                            <template
                                                x-if="eqbclass.preferred_return_type == 'irr' || eqbclass.preferred_return_type == 'cash_on_cash' || eqbclass.preferred_return_type == 'average_return' && eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">

                                                    <label for="preferred_return">Preferred return accrues on<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="preferred_return"
                                                        x-model="eqbclass.preferred_return">
                                                        <option value="capital_balance">Capital balance (most common)
                                                        </option>
                                                        <option value="invested_amount">Invested amount</option>
                                                    </select>
                                                </div>
                                            </template>
                                            <template
                                                x-if="eqbclass.preferred_return_type == 'irr' || eqbclass.preferred_return_type == 'cash_on_cash' || eqbclass.preferred_return_type == 'average_return' && eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <label for="day_count">Day count convention<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="day_count"
                                                        x-model="eqbclass.day_count">
                                                        <option value="actual_365">Actual/365 (most common)</option>
                                                        <option value="actual_actual">Actual/Actual</option>
                                                        <option value="actual_360">Actual/360</option>
                                                        <option value="30_365">30/365</option>
                                                        <option value="30_360">30/360</option>
                                                    </select>
                                                </div>
                                            </template>
                                            <template
                                                x-if="eqbclass.preferred_return_type == 'cash_on_cash' || eqbclass.preferred_return_type == 'average_return' && eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <label for="start_date" class="form-label">Start date override</label>
                                                    <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                        id="start_date" class="form-control"
                                                        placeholder="Enter override start date"
                                                        x-model="eqbclass.start_date">
                                                </div>
                                            </template>
                                            <template
                                                x-if="eqbclass.preferred_return_type == 'cash_on_cash' || eqbclass.preferred_return_type == 'average_return' && eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <label for="end_date" class="form-label">End date</label>
                                                    <input type="date" onclick="this.showPicker()" max="2999-12-31"
                                                        id="end_date" class="form-control"
                                                        placeholder="Enter an end date" x-model="eqbclass.end_date">
                                                </div>
                                            </template>
                                            <!-- Raise Quota -->
                                            {{--  <template x-if="eqbclass.class_type == 'LP'  || eqbclass.class_type == 'Mezzanine' ">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="raisequota">Raise quota (for billing)</label>
                                                        <input type="number" x-mask:dynamic="$money($input)" id="raisequota" name="raise_quota" class="form-control" placeholder="$0" x-model="eqbclass.raise_quota">
                                                    </div>
                                                </div>
                                            </template>  --}}
                                            <!-- Minimum Investment -->
                                            <template
                                                x-if="eqbclass.class_type == 'LP'  || eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mininvestment">Minimum investment ($) <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" x-on:input="moneyFormat($el)"
                                                            name="minimum_investment" class="form-control"
                                                            placeholder="$0" x-model="eqbclass.minimum_investment">
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- investment type -->
                                            <template
                                                x-if="eqbclass.class_type == 'LP'  || eqbclass.class_type == 'Mezzanine'">
                                                <div class="col-md-4 d-flex flex-column">
                                                    <div class="form-group">
                                                        <label for="investmenttype">Investment type <span
                                                                class="text-danger">*</span></label>
                                                        <select name="investment_type" id="investmenttype"
                                                            class="form-control custom-select"
                                                            x-model="eqbclass.investment_type"
                                                            :disabled="eqbclass.class_type == 'Mezzanine'">
                                                            <option value="">Select Investment Type</option>"
                                                            <option value="equity">Equity</option>
                                                            <option value="debt">Debt</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </template>



                                            <div x-data="{ expanded: false }">
                                                <div class="d-flex align-items-center cursor-pointer"
                                                    @click="expanded = ! expanded">
                                                    <i :class="expanded ? 'bi bi-caret-down-fill text-primary' :
                                                        'bi bi-caret-right-fill text-primary'"
                                                        class="me-1"></i>
                                                    <span class="advance-btn fw-bold">Advanced</span>
                                                </div>
                                                <div class="row" x-show="expanded" x-collapse>
                                                    {{--  investment type  --}}

                                                    {{--  Maximum investment  --}}
                                                    <template
                                                        x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                        <div class="col-md-4 d-flex flex-column">
                                                            <div class="form-group">
                                                                <label for="maximuinvestment">Maximum investment</label>
                                                                <input type="text" x-on:input="moneyFormat($el)"
                                                                    id="maximuinvestment" name="maximum_investment"
                                                                    class="form-control custom select" placeholder="$0"
                                                                    x-model="eqbclass.maximum_investment">
                                                            </div>
                                                        </div>
                                                    </template>
                                                    {{--  Price per unit  --}}
                                                    <template
                                                        x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                        <div class="col-md-4 d-flex flex-column">
                                                            <div class="form-group">
                                                                <label for="priceperunit">Price per unit <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" x-on:input="moneyFormat($el)"
                                                                    id="priceperunit" name="price_per_unit"
                                                                    class="form-control custom select" placeholder="$0"
                                                                    x-model="eqbclass.price_per_unit">
                                                            </div>
                                                        </div>
                                                    </template>
                                                    {{--  Target IRR  --}}
                                                    <template
                                                        x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                        <div class="col-md-4 d-flex flex-column">
                                                            <div class="form-group">
                                                                <label for="targetirr">Target IRR <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number" min="0" max="100"
                                                                    id="targetirr" name="target_irr"
                                                                    class="form-control custom select" placeholder="%"
                                                                    x-model="eqbclass.target_irr">
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{--  Pref return start date  --}}
                                                    <template
                                                        x-if="eqbclass.class_type == 'LP' || eqbclass.class_type == 'Mezzanine'">
                                                        <div class="col-md-4 d-flex flex-column">
                                                            <div class="form-group">
                                                                <label for="prefreturnstartdate">Pref return start date
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="date" onclick="this.showPicker()"
                                                                    max="2999-12-31" class="form-control custom select"
                                                                    x-model="eqbclass.pref_return_start_date">
                                                            </div>
                                                        </div>
                                                    </template>


                                                    {{--  <div class="col-md-4 d-flex flex-column">
                                                        <div class="form-group">
                                                            <label for="waitliststatus">Waitlist status <span class="text-danger">*</span></label>
                                                            <select name="waitlist_status"  id="waitliststatus" class="form-control custom-select" x-model="eqbclass.waitlist_status">
                                                                <option value="0">Off (most common)</option>
                                                                <option value="1">ON (new Investments require Approval)</option>
                                                                <option value="2">Automatic (enabled when target raise met)</option>
                                                            </select>
                                                        </div>
                                                    </div>  --}}
                                                </div>
                                            </div>
                                            {{-- Hurdles Block --}}
                                            {{-- Only show when equtity class type is 'LP' --}}
                                            <template x-if="eqbclass.class_type == 'LP'">
                                                <div class="bucket-class-hurdle-box">
                                                    <!-- Text Link to Show the Form -->

                                                    <!-- Hurdles Section -->
                                                    <div id="hurdlesContainer" class="mt-3">
                                                        <template x-for="(hurdle, hur_index) in eqbclass.hurdles"
                                                            :key="`hur-${hur_index}`">
                                                            <div x-data="{ adexpanded: false }"
                                                                class="hurdle-section border p-3 rounded bg-light mb-3"
                                                                id="hurdleTemplate">
                                                                <div class="d-flex justify-content-between align-items-center mb-3 bucket-class-hurdle-header"
                                                                    @click="adexpanded = !adexpanded">
                                                                    <div class="container mt-4">
                                                                        <div class="row border-bottom pb-3">
                                                                            <div class="col d-flex">
                                                                                <div class="cursor-pointer  text-primary">
                                                                                    <i
                                                                                        :class="adexpanded ?
                                                                                            'bi bi-caret-down-fill' :
                                                                                            'bi bi-caret-right-fill'"></i>
                                                                                </div>
                                                                                <template x-if="hurdle.hurdle_name !== ''">
                                                                                    <h5 class="ms-3"
                                                                                        x-text="hurdle.hurdle_name"></h5>
                                                                                </template>
                                                                            </div>
                                                                            <div class="col ">
                                                                                <div class="text-secondary small d-flex">
                                                                                    Upside
                                                                                    split &nbsp; <p
                                                                                        x-text="`${Number(hurdle.upside_split.replace('%',''))}% / ${(100 - Number(hurdle.upside_split.replace('%','')))}%  `">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col">
                                                                                <div class="text-secondary small d-flex">
                                                                                    Limit
                                                                                    &nbsp; <p x-text="hurdle.upside_limit">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col">
                                                                                <div class="text-secondary small d-flex">
                                                                                    Type:
                                                                                    &nbsp;
                                                                                    <p
                                                                                        x-text="getNameTitle(hurdle.preferred_return_type)">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Delete Hurdle -->
                                                                    <button class="btn remove-icon-btn"
                                                                        title="Remove Hurdle"
                                                                        @click="eqbclass.hurdles.splice(hur_index, 1)">
                                                                        <i class="las la-trash danger"></i>
                                                                    </button>
                                                                </div>

                                                                <div x-show="adexpanded" x-transition class="row">

                                                                    <div class="mb-3 row d-flex align-items-center">
                                                                        <div
                                                                            class="col-md-12 d-flex flex-wrap align-items-center gap-1 bg-light p-3 rounded shadow-sm">

                                                                            <!-- Label -->
                                                                            <label for="upside_split"
                                                                                class="font-weight-bold text-dark  p-2 rounded shadow-sm">
                                                                                Upside Split
                                                                            </label>

                                                                            <!-- Upside Split Input -->
                                                                            <input type="text"
                                                                                class="xm-percent-inline"
                                                                                id="upside_split" placeholder="100"
                                                                                x-on:input="percentFormat($el); checkGPRequiredError()"
                                                                                x-model="hurdle.upside_split">
                                                                            <span x-show="errors.upside_split"
                                                                                class="text-danger"
                                                                                x-text="errors.upside_split">
                                                                            </span>
                                                                            <span>to</span>
                                                                            <template x-if="eqbclass.equity_class_name">
                                                                                <h6 class="ms-1 mb-0 text-primary"
                                                                                    x-text="eqbclass.equity_class_name">
                                                                                </h6>
                                                                                <span> , </span>
                                                                            </template>

                                                                            <!-- GP Class Name + Dynamic Calculation -->
                                                                            <template x-if="gpClassName">
                                                                                <input type="text"
                                                                                    class="xm-percent-inline" readonly
                                                                                    :value="`${(100 - Number(hurdle.upside_split.replace('%', '')))}%`">
                                                                                <span>to</span>
                                                                                <div
                                                                                    class="d-flex align-items-center gap-1">
                                                                                    <h6 class="ms-1 mb-0 text-success"
                                                                                        x-text="gpClassName"></h6>
                                                                                </div>
                                                                            </template>

                                                                            <!-- Until Condition -->
                                                                            <span> until</span>
                                                                            <template x-if="eqbclass.equity_class_name">
                                                                                <h6 class="ms-1 mb-0 text-primary"
                                                                                    x-text="eqbclass.equity_class_name">
                                                                                </h6>
                                                                            </template>

                                                                            <!-- Achieves Condition -->
                                                                            <span>achieves</span>
                                                                            <input type="text"
                                                                                class="xm-percent-inline"
                                                                                id="upside_limit" placeholder="100"
                                                                                x-on:input="percentFormat($el)"
                                                                                x-model="hurdle.upside_limit">%

                                                                            <span
                                                                                x-text="getNameTitle(hurdle.preferred_return_type)"></span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4 d-flex flex-column">
                                                                        <label for="hurdl_ename"
                                                                            class="font-weight-bold text-dark bg-light p-2 rounded">Hurdle
                                                                            Name</label>
                                                                        <input type="text" class="form-control"
                                                                            id="hurdle_name"
                                                                            placeholder="Enter hurdle name"
                                                                            x-model="hurdle.hurdle_name">
                                                                    </div>
                                                                    <div class="col-md-4 d-flex flex-column">
                                                                        <label for="preferred_return_type"
                                                                            class="font-weight-bold text-dark bg-light p-2 rounded">Preferred
                                                                            Return Type <span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-control"
                                                                            id="preferred-return-type"
                                                                            x-model="hurdle.preferred_return_type">
                                                                            <option value="">Choose your preferred
                                                                                return type</option>
                                                                            <option value="aar">Average Anual return
                                                                            </option>
                                                                            <option value="cash_on_cash">Cash on cash
                                                                            </option>
                                                                            <option value="irr">IRR</option>
                                                                            <option value="roi">ROI</option>
                                                                            <option value="return_of_capital">Return Of
                                                                                Capital</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4 d-flex flex-column">
                                                                        <label for="final_hurdle"
                                                                            class="font-weight-bold text-dark bg-light p-2 rounded">Final
                                                                            Hurdle <span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-control" id="final_hurdle"
                                                                            x-model="hurdle.final_hurdle">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                    <div x-data="{ expanded: false }">
                                                                        <div class="d-flex align-items-center cursor-pointer"
                                                                            @click="expanded = ! expanded">
                                                                            <i :class="expanded ?
                                                                                'bi bi-caret-down-fill text-primary' :
                                                                                'bi bi-caret-right-fill text-primary'"
                                                                                class="me-1"></i>
                                                                            <span
                                                                                class="advance-btn fw-bold">Advanced</span>
                                                                        </div>
                                                                        <div class="row" x-show="expanded"
                                                                            x-collapse>
                                                                            <template
                                                                                x-if="hurdle.preferred_return_type !== 'irr'">
                                                                                <div class="col-md-4 d-flex flex-column">
                                                                                    <label for="catch_up"
                                                                                        class="font-weight-bold text-dark bg-light p-2 rounded">Catch
                                                                                        up on preferred returns<span
                                                                                            class="text-danger">*</span></label>
                                                                                    <select class="form-control"
                                                                                        id="catch_up"
                                                                                        x-model="hurdle.catch_up">
                                                                                        <option value="yes">Yes
                                                                                        </option>
                                                                                        <option value="no">No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </template>
                                                                            <div class="col-md-4 d-flex flex-column">
                                                                                <label for="honor_only"
                                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Honor
                                                                                    only on capital event<span
                                                                                        class="text-danger">*</span></label>
                                                                                <select class="form-control"
                                                                                    id="honor_only"
                                                                                    x-model="hurdle.honor_only">
                                                                                    <option value="no">No</option>
                                                                                    <option value="yes">Yes</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 d-flex flex-column">
                                                                                <label for="preferred_return"
                                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Preferred
                                                                                    return accrues on<span
                                                                                        class="text-danger">*</span></label>
                                                                                <select class="form-control"
                                                                                    id="preferred_return"
                                                                                    x-model="hurdle.preferred_return">
                                                                                    <option value="capital_balance">
                                                                                        Capital
                                                                                        balance (most common)</option>
                                                                                    <option value="invested_amount">
                                                                                        Invested amount</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 d-flex flex-column">
                                                                                <label for="day_count"
                                                                                    class="font-weight-bold text-dark bg-light p-2 rounded">Day
                                                                                    count convention<span
                                                                                        class="text-danger">*</span></label>
                                                                                <select class="form-control"
                                                                                    id="day_count"
                                                                                    x-model="hurdle.day_count">
                                                                                    <option value="actual_365">Actual/365
                                                                                        (most common)</option>
                                                                                    <option value="actual_actual">
                                                                                        Actual/Actual</option>
                                                                                    <option value="actual_360">Actual/360
                                                                                    </option>
                                                                                    <option value="30_365">30/365</option>
                                                                                    <option value="30_360">30/360</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="row">
                                                                                <!-- Start date override -->
                                                                                <div class="col-md-6 mb-3">
                                                                                    <label for="start_date"
                                                                                        class="form-label">Start date
                                                                                        override</label>
                                                                                    <input type="date"
                                                                                        onclick="this.showPicker()"
                                                                                        max="2999-12-31" id="start_date"
                                                                                        class="form-control"
                                                                                        placeholder="Enter override start date"
                                                                                        x-model="hurdle.start_date">
                                                                                </div>

                                                                                <!-- End date -->
                                                                                <div class="col-md-6 mb-3">
                                                                                    <label for="end_date"
                                                                                        class="form-label">End
                                                                                        date</label>
                                                                                    <input type="date"
                                                                                        onclick="this.showPicker()"
                                                                                        max="2999-12-31" id="end_date"
                                                                                        class="form-control"
                                                                                        placeholder="Enter an end date"
                                                                                        x-model="hurdle.end_date">
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <a href="javascript:void(0);" class="add-hurdle-link"
                                                            @click="addBclassHurdle(b_index, bc_index)"
                                                            x-show="!eqbclass.hurdles.length || eqbclass.hurdles[eqbclass.hurdles.length - 1].final_hurdle !== 'yes'">Add
                                                            Hurdle</a>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </div>
                        </div>
                </template>
            </div>
        </div>
        </template>

        <!-- Dropzone Section -->
        <div class="dropzone mt-4 p-4 bg-light text-center footer-save" role="button"
            @click="classes.push({...classForm})">
            <p class="mb-2">Click here to add a new class</p>
        </div>

        <!-- Submit Button -->
        <div id="save-container" class="sticky-bottom bg-light border-top shadow-lg text-end p-2 height-80">
            <button type="submit" id="save"
                class="btn btn-primary btn-xl rounded-pill shadow px-5 py-3 height-60" @click="submitForm">
                Save
            </button>
        </div>





    </div>
    </div>
    <div class="tab-pane fade" id="distribution-waterfalls" role="tabpanel"
        aria-labelledby="distribution-waterfalls-tab">
        @include('admin.deals.waterfalls.waterfalls_tab')
    </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center deletconText">Are you sure you want to delete this?</p>
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal"
                            @click="resetdelete()">Close</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Water fall Modal --}}

    <div class="deal-modal modal right fade" id="addWaterfallModal" tabindex="-1"
        aria-labelledby="addWaterfallModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <!-- Modal Header -->
                <div class="modal-header row bg-primary text-white">
                    <h5 class="modal-title col text-white">Add Waterfall</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <div>
                        @csrf
                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="waterfall_name" class="form-label">Name*</label>
                            <input type="text" name="waterfall_name" class="form-control"
                                placeholder="Enter name" x-model="newwaterfallForm.waterfall_name" required>
                        </div>

                        <!-- Set as Default -->
                        <div class="mb-3">
                            <label for="is_default" class="form-label">Set as default*</label>
                            <div class="form-check">
                                <input type="radio" id="defaultYes" name="default" class="form-check-input"
                                    value="1" x-model="newwaterfallForm.is_default" required>
                                <label for="defaultYes" class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="defaultNo" name="default" class="form-check-input"
                                    value="0" x-model="newwaterfallForm.is_default" required>
                                <label for="defaultNo" class="form-check-label">No</label>
                            </div>
                        </div>

                        <!-- Waterfall Template Dropdown -->
                        <div class="mb-3">
                            <label for="waterfall_template" class="form-label">Waterfall template</label>
                            <select id="waterfall_template" name="waterfall_template" class="form-select"
                                x-model="newwaterfallForm.waterfall_template">
                                <option value="no_template" selected>No template</option>
                                <template x-for="(waterfall, w_index) in waterfalls" :key="w_index">
                                    <option :value="waterfall.id" x-text="'Copy from ' + waterfall.waterfall_name">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <!-- Save and Cancel Buttons -->
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"
                                @click="submitNewWaterfallForm">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/plugins/multipleselect/bootstrap-multiselect.min.js') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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
                },
                getNameTitle(str) {
                    switch (str) {
                        case 'LP':
                            return 'Limited Partner';
                        case 'GP':
                            return 'General Partner';
                        case 'Mezzanine':
                            return 'Mezzanine';
                        case 'irr':
                            return 'Internal Rate of Return';
                        case 'roi':
                            return 'Return on Investment';
                        case 'cash_on_cash':
                            return 'Cash on Cash';
                        case 'aar':
                            return 'Average Anual Return';
                        case 'return_of_capital':
                            return 'Return of Capital';
                        default:
                            return str;
                    }
                },
                isValidJsonString(str) {
                    try {
                        JSON.parse(str);
                    } catch (e) {
                        return false;
                    }
                    return true;
                }
            }
        }
    </script>


    <script>
        var csrf = '{{ csrf_token() }}';

        function equityclass() {
            return {
                // class_type: '',
                ...alpineHelpers(),
                successMessage: '',
                loading: false,
                deleteType: null,
                classIndex: null,
                bucketIndex: null,
                hurdleIndex: null,
                classes: @json($classes),
                buckets: @json($buckets),
                waterfalls: @json($waterfalls),
                waterfall: @json($waterfall),
                selectedWaterfall: null,
                mergedClasses: [],
                classForm: {
                    class_type: '',
                    equity_class_name: '',
                    entity_legal_ownership: '',
                    preferred_return_type: '',
                    preferred_return: '',
                    raise_amount_ownership: '',
                    raise_amount_distributions: '',
                    raise_quota: '',
                    minimum_investment: '',
                    hurdles: [],
                    investment_type: 'equity',
                    distribution_share: '',
                    maximum_investment: '',
                    price_per_unit: '',
                    target_irr: '',
                    pref_return_start_date: '',
                    waitlist_status: 'off',
                    preferred_return_accrues_on: 'capital_balance',
                    day_count: '',
                    start_date: '',
                    end_date: '',

                },
                newHurdle: {
                    upside_split: '',
                    hurdle_name: '',
                    preferred_return_type: 'cash_on_cash',
                    final_hurdle: 'no',
                    catch_up: 'yes',
                    honor_only: 'no',
                    preferred_return: 'capital_balance',
                    day_count: 'actual_365',
                    start_date: '',
                    end_date: '',
                },
                bucketForm: {
                    equity_bucket_name: '',
                    raise_amount_ownership: '',
                    raise_amount_distributions: '',
                    raise_quota: '',
                    classes: [],
                    waitlist_status: 'off',
                },
                waterfallForm: {
                    hurdle_type: '',
                    split: '',
                    included_class: "[]",
                    classes_values: [],
                    splits: [],
                    cumulated_return_reach: '',
                    day_count: 'actual_365',
                    compounding_frequency: '',
                    start_date: '',
                    end_date: '',
                    duration: '',
                    accrues_on: 'capital_balance',
                    payment_towards: 'other',
                    payment_type_towards: 'preferred_return',
                    split_unpayed: 'unpaid_accrual',
                    accrual_cadence: 'daily',
                    notes: '',

                },
                newwaterfallForm: {
                    waterfall_name: '',
                    is_default: '',
                    waterfall_template: '',
                },
                errors: {},
                setGPClassName() {
                    let gpClass = this.classes.find(c => c.class_type === 'GP');
                    if (!gpClass) {
                        gpClass = this.buckets.find(b => b.classes.some(c => c.class_type === 'GP'));
                        if (gpClass) {
                            gpClass = gpClass.classes.find(c => c.class_type === 'GP');
                        }
                    }
                    this.gpClassName = gpClass ? gpClass.equity_class_name : '';
                },
                init() {
                    this.setGPClassName();
                    let bucketsClasses = [];
                    this.buckets.map(bucket => {
                        return bucketsClasses = [...bucketsClasses, ...bucket.classes];
                    });
                    this.mergedClasses = [...this.classes, ...bucketsClasses];
                    setTimeout(() => {
                        this.selectedWaterfall = this.waterfalls.findIndex(waterfall => waterfall.is_default === 1);
                        this.initSelect2();
                    }, 1000);

                },

                checkGPRequiredError() {

                    let gpClassExist = this.classes.find(c => c.class_type === 'GP') ?? false;
                    if (!gpClassExist) {
                        gpClassExist = this.buckets.find(c => c.class_type === 'GP') ?? false;
                    }
                    let gpRequired = false;
                    // if any class has hurdle less tha 100 upside_split
                    this.classes.some(c => {
                        return c.hurdles.some(h => {
                            if (Number(h.upside_split.replace('%', '')) < 100) {
                                gpRequired = true;
                                return true; // Exit the loop early
                            }
                        });
                    });

                    if (!gpRequired) {
                        this.buckets.some(b => {
                            return b.classes.some(c => {
                                return c.hurdles.some(h => {
                                    if (Number(h.upside_split.replace('%', '')) < 100) {
                                        gpRequired = true;
                                        return true; // Exit the loop early
                                    }
                                });
                            });
                        });
                    }

                    if (gpRequired && !gpClassExist) {
                        this.errors = {
                            "upside_split": 'Please create GP class first!',
                        };
                    } else {
                        this.errors.upside_split = '';
                    }

                },
                initSelect2() {
                    $('.included_class').select2();
                    this.waterfall.hurdles.forEach((hurdle, index) => {
                        let valueToSet = this.isValidJsonString(hurdle.included_class) ? JSON.parse(hurdle
                            .included_class) : hurdle.included_class;
                        $(`#included_class_${index}`).val(valueToSet).trigger('change');
                    });

                    $('.included_class').on('change', function() {
                        var selected = $(this).val();
                        let id = $(this).attr('id');
                        let hurdle_index = id.split('_')[2];

                        console.log(selected);
                        // find alpine component by id
                        let AlpineObj = document.querySelector('[x-data="equityclass()"]');
                        let dataStack = AlpineObj._x_dataStack[0];

                        dataStack.waterfall['hurdles'][hurdle_index]['included_class'] = selected;
                        console.log(dataStack.waterfall['hurdles'][hurdle_index]['included_class']);

                        // // if(dataStack.waterfall['hurdles'][hurdle_index]['classes_values'].length == selected.length) {
                        // //     return;
                        // // }
                        let values = dataStack.waterfall['hurdles'][hurdle_index]['classes_values'];
                        if (values.length !== selected.length) {
                            values = [];
                            selected.forEach((element, elm_index) => {
                                if (values[elm_index] == undefined) {
                                    values[elm_index] = {
                                        id: element,
                                        value: 0
                                    };
                                }
                            });
                        } else {
                            selected.forEach((element, elm_index) => {
                                if (values[elm_index] == undefined) {
                                    values[elm_index] = {
                                        id: element,
                                        value: 0
                                    };
                                }
                            });
                        }

                        // let values = [];

                        // selected.forEach((element, elm_index) => {
                        //     values.push({
                        //         id: element,
                        //         value: 0
                        //     });
                        // });
                        dataStack.waterfall['hurdles'][hurdle_index]['classes_values'] = values;
                    });
                },
                changeSplitType(index) {
                    this.initSelect2();
                    let splitType = $(`#split_type_${index}`).val();
                    if (this.waterfall.hurdles[index].split == 'Yes') {
                        this.waterfall.hurdles[index].splits = [{
                                value: '50%',
                            },
                            {
                                value: '50%',
                            }
                        ];
                    } else {
                        this.waterfall.hurdles[index].splits = [];
                    }
                },
                async submitForm() {
                    // console.log(this.classes);

                    this.distributionShare = this.validatedistributionShare();
                    if (this.distributionShare === false) {
                        return;
                    }
                    //if any mergedlClasses hurdle create it is important to create gp class
                    let gpClassExist = this.classes.find(c => c.class_type === 'GP') ?? false;
                    if (!gpClassExist) {
                        gpClassExist = this.buckets.find(c => c.class_type === 'GP') ?? false;
                    }
                    let gpRequired = false;
                    // if any class has hurdle less tha 100 upside_split
                    this.classes.some(c => {
                        return c.hurdles.some(h => {
                            if (Number(h.upside_split.replace('%', '')) < 100) {
                                gpRequired = true;
                                return true; // Exit the loop early
                            }
                        });
                    });

                    if (!gpRequired) {
                        this.buckets.some(b => {
                            return b.classes.some(c => {
                                return c.hurdles.some(h => {
                                    if (Number(h.upside_split.replace('%', '')) < 100) {
                                        gpRequired = true;
                                        return true; // Exit the loop early
                                    }
                                });
                            });
                        });
                    }

                    debugger

                    if (gpRequired && !gpClassExist) {
                        cosyAlert('<strong>Warning</strong><br />Please create GP class first!', 'error');
                        return;
                    }


                    // return;
                    this.loading = true;
                    let url = '';
                    debugger;

                    // Convert the PHP boolean into a JavaScript boolean variable
                    let isPartner = {{ auth('admin')->user()->hasRole('partner') ? 'true' : 'false' }};

                    if (isPartner) {
                        url = "{{ route('partner.deals.class.store', $deal->id) }}";
                    } else {
                        url = "{{ route('admin.deals.class.store', $deal->id) }}";
                    }
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                classes: this.classes,
                                buckets: this.buckets,
                            })
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // alert(responseData.message);
                            console.log(responseData);
                            this.waterfalls = responseData.deal.waterfalls;
                            this.successMessage = 'Updated Successfully';
                            // $('.alert-success').alert();
                            cosyAlert('<strong>Success</strong><br />Updated data succefully!', 'success');

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                async submitWaterfallForm() {

                    this.waterfall.hurdles = this.waterfall.hurdles.map((hurdle, index) => {
                        return {
                            ...hurdle,
                            included_class: $(`#included_class_${index}`).val()
                        }
                    });
                    this.loading = true;
                    let url = "";
                    let isPartner = {{ auth('admin')->user()->hasRole('partner') ? 'true' : 'false' }};
                    if (isPartner) {
                        url = "{{ route('partner.waterfalls.store', $deal->id) }}";
                    } else {
                        url = "{{ route('admin.waterfalls.store', $deal->id) }}";
                    }

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                waterfall: this.waterfall,

                            })
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // alert(responseData.message);
                            console.log(responseData);
                            this.successMessage = 'Updated Successfully';
                            // $('.alert-success').alert();
                            cosyAlert('<strong>Success</strong><br />Updated data succefully!', 'success');

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                async submitNewWaterfallForm() {

                    this.loading = true;
                    let url = "";
                    let isPartner = {{ auth('admin')->user()->hasRole('partner') ? 'true' : 'false' }};
                    if (isPartner) {
                        url = "{{ route('partner.waterfalls.new.store', $deal->id) }}";
                    } else {
                        url = "{{ route('admin.waterfalls.new.store', $deal->id) }}";
                    }

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.newwaterfallForm)
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // alert(responseData.message);
                            console.log(responseData);
                            this.successMessage = 'Updated Successfully';
                            // $('.alert-success').alert();
                            cosyAlert('<strong>Success</strong><br />Updated data succefully!', 'success');
                            window.location.reload();
                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                addHurdle(index) {

                    this.classes[index].hurdles.push({
                        ...this.newHurdle
                    });
                    console.log('Hurdle Added');
                },
                addBclassHurdle(bucktIndex, classIndex) {
                    this.buckets[bucktIndex].classes[classIndex].hurdles.push({
                        ...this.newHurdle
                    });
                    console.log('Hurdle Added');
                },
                deleteConfirmation(type, bucket_index, class_index, hurdle_index, wfh_index = null) {
                    this.deleteType = type;
                    this.classIndex = class_index;
                    this.bucketIndex = bucket_index;
                    this.hurdleIndex = hurdle_index;
                    window.wfh_index = wfh_index;
                    // convert classType to title case remeve '_' and capitalize first letter
                    let classType = type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    $('.deletconText').text('Are you sure you want to delete ' + classType + '.?');
                    $('#deleteConfirmationModal').modal('show');
                },
                removeNestentedHurdle(hurdle, s_index, nh_index) {
                    console.log(hurdle, s_index, nh_index);
                    hurdle.paths[s_index].hurdles.splice(nh_index, 1);
                    // hurdle
                    // find this nested hurdle form all hurdles and there nested hurdles

                },
                confirmDelete() {
                    if (this.deleteType === 'class') {
                        this.classes.splice(this.classIndex, 1);
                    } else if (this.deleteType === 'bucket' && this.classIndex === null) {
                        this.buckets.splice(this.bucketIndex, 1);
                    } else if (this.deleteType === 'bucket_class') {
                        this.buckets[this.bucketIndex].classes.splice(this.classIndex, 1);
                    } else if (this.deleteType === 'bucket_class_hurdle') {
                        this.buckets[this.bucketIndex].classes[this.classIndex].hurdles.splice(this.hurdleIndex, 1);
                    } else if (this.deleteType === 'class_hurdle') {
                        this.classes[this.classIndex].hurdles.splice(this.hurdleIndex, 1);
                    } else if (this.deleteType === 'waterfallhurdle') {
                        this.waterfall.hurdles.splice(window.wfh_index);
                    }

                    $('#deleteConfirmationModal').modal('hide');
                },
                classTypeChanges(type, bucket_index, class_index) {
                    if (type === 'bucket_class') {
                        if (this.buckets[bucket_index].classes[class_index].class_type === 'Mezzanine') {
                            this.buckets[bucket_index].classes[class_index].investment_type = 'debt';
                        } else {
                            this.buckets[bucket_index].classes[class_index].investment_type = 'equity';
                        }
                    } else {
                        if (this.classes[class_index].class_type === 'Mezzanine') {
                            this.classes[class_index].investment_type = 'debt';
                        } else {
                            this.classes[class_index].investment_type = 'equity';
                        }
                    }
                },
                changeRaiseAmountOwner(type, bucket_index, class_index, value) {
                    // if(type === 'class') {
                    //     if (value <= this.classes[class_index].raise_amount_distributions) {
                    //         console.log(type);
                    //         this.classes[class_index].raise_amount_distributions = value;
                    //     }
                    // }else{

                    // }
                },
                resetdelete() {
                    this.deleteType = null;
                    this.classIndex = null;
                    this.bucketIndex = null;
                    this.hurdleIndex = null;
                },
                validatedistributionShare() {
                    // Classes Distribution Share
                    let totalDistributionShare = this.classes.reduce((acc, eqdistributionhurdle) => {
                        return acc + (parseFloat(eqdistributionhurdle.distribution_share) || 0);
                    }, 0);
                    // Buckets Distribution Share
                    let totalBucketDistributionShare = this.buckets.reduce((acc, eqbucket) => {
                        return acc + (parseFloat(eqbucket.distribution_share) || 0);
                    }, 0);
                    // Total Distribution Share
                    totalDistributionShare += totalBucketDistributionShare;

                    if (totalDistributionShare !== 100) {
                        cosyAlert('Total distribution share must be exactly 100%. Current total is ' +
                            totalDistributionShare + '%.', 'error');
                        return false;
                    }

                    let totalEntityLegalOwnership = this.classes.reduce((acc, eqdistributionhurdle) => {
                        return acc + (parseFloat(eqdistributionhurdle.entity_legal_ownership) || 0);
                    }, 0);

                    let totalBucketEntityLegalOwnership = this.buckets.reduce((acc, eqbucket) => {
                        return acc + (parseFloat(eqbucket.entity_legal_ownership) || 0);
                    }, 0);

                    totalEntityLegalOwnership += totalBucketEntityLegalOwnership;

                    if (totalEntityLegalOwnership !== 100) {
                        cosyAlert('Total entity legal ownership must be exactly 100%. Current total is ' +
                            totalEntityLegalOwnership + '%.', 'error');
                        return false;
                    }


                },
                waterfallChanged(value) {
                    this.waterfall = this.waterfalls[value];
                    setTimeout(() => {
                        $('.included_class').select2();
                        this.waterfall.hurdles.forEach((hurdle, index) => {
                            $(`#included_class_${index}`).val(
                                JSON.parse(this.waterfall.hurdles[index].included_class)
                            ).trigger('change');
                        });
                    }, 500);


                    if (value === 'basic_waterfall') {
                        // Logic to display class and bucket hurdles
                        this.cbhurdle = true;
                    } else {
                        this.cbhurdle = false;
                    }

                },
                calculate() {
                    console.log('Calculating!!');
                },
                setClasses(data, type = null, index = null) {
                    console.log(data);
                    if (type === 'wfhurdle') {
                        this.waterfall.hurdles[index].classes = data.classes;
                    }
                },
                newWaterfallHurdle() {
                    this.waterfall.hurdles.push({
                        ...this.waterfallForm
                    });
                    setTimeout(() => {
                        this.initSelect2();
                    }, 500);
                },
                getClassName(id) {
                    let name = '';
                    this.classes.forEach((cls) => {
                        if (cls.id == id) {
                            name = cls.equity_class_name;
                        }
                    });
                    if (name == '') {
                        this.buckets.forEach((bucket) => {
                            bucket.classes.forEach((cls) => {
                                if (cls.id == id) {
                                    name = cls.equity_class_name;
                                }
                            });
                        });
                    }
                    return name;
                },
                addSplitPath(index) {
                    this.waterfall.hurdles[index].splits.push({
                        value: 0
                    });
                },
                removeSplitPath(index, splitIndex) {
                    this.waterfall.hurdles[index].splits.splice(splitIndex, 1);
                    // rearrange indexes of splits
                    this.waterfall.hurdles[index].splits = this.waterfall.hurdles[index].splits.map((split, index) => {
                        return {
                            ...split,
                            index: index
                        }
                    });
                },
                hurdleTitle(hurdle, index) {
                    console.log(hurdle);
                    let str = '';
                    if (index === 0) {
                        str += ' Distribute to ';
                    } else {
                        str += ' then ';
                    }

                    if (hurdle.hurdle_type == 'management_fee') {
                        str = ` Pay `;
                    }

                    if ((hurdle.hurdle_type == 'split' && hurdle.split == 'No') || hurdle.hurdle_type == 'cash_on_cash' ||
                        hurdle.hurdle_type == 'irr' || hurdle.hurdle_type == 'roi' || hurdle.hurdle_type ==
                        'return_of_capital' || hurdle.hurdle_type == 'cumulative_return' || hurdle.hurdle_type == 'interest'
                    ) {
                        hurdle.classes_values?.forEach((cls, index) => {
                            if (index > 0) {
                                str += ', ';
                            }
                            str += `<span class="text-primary">${this.getClassName(cls.id)} </span>`;
                            if (hurdle.hurdle_type == 'cash_on_cash') {
                                str += `until it achieves ${this.getNameTitle(hurdle.hurdle_type)} of ${cls.value}`;
                                if (hurdle.upside_limit) {
                                    str += ` with an upside limit of ${hurdle.upside_limit}`;
                                }
                            } else if (hurdle.hurdle_type == 'irr' || hurdle.hurdle_type == 'roi') {
                                str += `until it achieves ${this.getNameTitle(hurdle.hurdle_type)} of ${cls.value}`;
                            } else if (hurdle.hurdle_type == 'return_of_capital' || hurdle.hurdle_type ==
                                'interest') {
                                str +=
                                    `until it receives ${this.getNameTitle(hurdle.hurdle_type)} of initial capital`;
                            } else if (hurdle.hurdle_type == 'split') {
                                // TODO - Add split logic with another loop
                                str += `until it achieves ${cls.value}`;
                            }
                        });
                    }

                    if (hurdle.hurdle_type == 'split' && hurdle.split == 'Yes') {
                        str = ' Distribute ';
                        hurdle.splits.forEach((split, index) => {
                            if (index > 0) {
                                str += ', ';
                            }
                            str += `${split.value} to Path ${index + 1}`;
                        });
                    }

                    return str;
                },
                addNestedHurdle(parentHurdle, pathIndex) {
                    if (!parentHurdle.paths) {
                        parentHurdle.paths = [];
                    }
                    if (!parentHurdle.paths[pathIndex]) {
                        parentHurdle.paths[pathIndex] = {
                            hurdles: []
                        };
                    }
                    parentHurdle.paths[pathIndex].hurdles.push({
                        ...this.waterfallForm
                    });

                    // this.waterfall.hurdles[pathIndex].splits.push({
                    //     value: 0,
                    //     hurdles: []
                    // });
                },
                // addNestedHurdle(pathIndex, splitIndex) {
                //     this.waterfall.hurdles[pathIndex].splits[splitIndex].hurdles.push({...this.waterfallForm});
                // }
            };
        }
    </script>

    <script>
        // document.getElementById('distribution-select').addEventListener('change', function(event) {
        //     if (event.target.value === 'add-new') {
        //         const addWaterfallModal = new bootstrap.Modal(document.getElementById('addWaterfallModal'));
        //         addWaterfallModal.show();
        //          event.target.value = "";
        //      }
        //  });
        //   $(document).ready(function() {
        //    $('.js-example-basic-single').select2(
        //      theme: 'bootstrap4',
        //    );
        // });
    </script>
@endpush
