@push('style')
    <style>
        .advanced-expandable {
            color: #007bff;
            cursor: pointer;
            margin-left: 20px;
            margin-top: 10px;
        }

        /* Design class for disabled input field .form-control */
        .form-control[disabled] {
            background-color: #f8f9fa;
            color: #373838;
            cursor: not-allowed;
        }

        .split-fields {
            padding-right: 30px;
        }

        .card-text {
            margin-bottom: 1.5rem;
            margin-right: 30px;
            margin-left: 30px;
        }

        .card-text:last-child {
            margin-bottom: 1.5rem;
        }
    </style>
@endpush
<template x-if="waterfall && waterfall.is_basic === 1">
    <div>
        <div class="col-md-12">
            <!-- Display as Heading -->
            <h3 id="waterfall_heading" class="heading-class" x-text="waterfall.waterfall_name">
            </h3>

            <!-- Editable Input Field -->

        </div>
        <div class="row">
            <span class="text-md py-2 mt-2">The distribution waterfall created from the deal's class structure. </span>
        </div>
        <template x-for="(eqclass, index) in classes" :key="index">
            <div>
                <template x-if="eqclass.distribution_share == 100">
                    <div>
                        <template x-if="eqclass.hurdles.length > 0">
                            <template x-for="(hurdle, index) in eqclass.hurdles" :key="index">
                                <div class="card class-form-card border border-primary mb-4" x-data="{ expanded: false }">
                                    <div class="card-header">
                                        <div class="col-md-12">
                                            <div class="d-flex">
                                                <div @click="expanded = !expanded" class=" text-primary"
                                                    style="cursor: pointer;">
                                                    <i
                                                        :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                                </div>
                                                <span class="ms-3">
                                                    Distribute to <span class="text-info"
                                                        x-text="eqclass.equity_class_name"></span> untill it achieves
                                                    <span
                                                        x-text="
                                                        hurdle.preferred_return_type === 'irr' ? 'IRR' : 
                                                        hurdle.preferred_return_type === 'roi' ? 'ROI' : 
                                                        hurdle.preferred_return_type === 'cash_on_cash' ? 'CoC' : 
                                                        hurdle.preferred_return_type === 'aar' ? 'AAR' : ''
                                                    "></span>
                                                    return of <span class="text-info"
                                                        x-text="hurdle.upside_limit "></span>
                                                    <template x-if="eqbucket && eqbucket.id === eqclass.id">
                                                        ,
                                                        <span class="ms-3"> <span class="text-info"
                                                                x-text="eqbucket.equity_bucket_name"></span> untill it
                                                            achieves
                                                            <span
                                                                x-text="
                                                                hurdle.preferred_return_type === 'irr' ? 'IRR' : 
                                                                hurdle.preferred_return_type === 'roi' ? 'ROI' : 
                                                                hurdle.preferred_return_type === 'cash_on_cash' ? 'CoC' : 
                                                                hurdle.preferred_return_type === 'aar' ? 'AAR' : '' && eqbclass
                                                            "></span>
                                                            return of <span class="text-info"
                                                                x-text="hurdle.upside_limit "></span>
                                                        </span>
                                                    </template>
                                                </span>


                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="expanded" x-transition class="card-text row mt-4">
                                        <div class="col-md-12 small m-3">
                                            The split hurdle defines how remaining distributable funds should be split
                                            among the included classes or paths. Learn more.
                                        </div>
                                        <div class="col-md-12">
                                            <div class="px-2">
                                                <label for="hurdle_type" class="form-label">Hurdle Type*</label>
                                                <input type="text" id="hurdle_type" class="form-control"
                                                    placeholder="Hurdle Type" :value="hurdle.preferred_return_type"
                                                    disabled>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12">
                                            <div class="px-2">
                                                <label for="split_paths" class="form-label">Split into multiple paths?*</label>
                                                <input type="text" id="split_paths" class="form-control" placeholder="Split into multiple Paths?" value="No" disabled>
                                            </div>
                                        </div> --}}
                                        {{-- Included classes in input field with tags --}}
                                        <div class="col-md-12">
                                            <div class="px-2">
                                                <label for="included_classes" class="form-label">Included
                                                    Classes*</label>
                                                <input type="text" id="included_classes" class="form-control"
                                                    placeholder="Included Classes" x-model="eqclass.equity_class_name"
                                                    disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="px-2">
                                                <label for="class_share" class="form-label"><span
                                                        x-text="eqclass.equity_class_name"></span></label>
                                                <input type="text" class="form-control" placeholder="class_share"
                                                    x-model="eqclass.distribution_share" disabled>
                                            </div>
                                        </div>
                                        <div class="row" x-data="{ expanded: false }">
                                            <span class="advanced-expandable"
                                                @click="expanded = !expanded">Advanced</span>
                                            <div x-show="expanded" x-transition class="card-text row mt-4">
                                                <div class="ms-3">
                                                    <div class="px-2 py-3">
                                                        <label for="payment_as" class="form-label">What should payments
                                                            toward this hurdle count as?*</label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Payment as" value="Other" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <div class="card class-form-card border border-primary mb-4" x-data="{ expanded: false }">
                            <div class="card-header">
                                <div class="col-md-12">
                                    <div class="d-flex">
                                        <div @click="expanded = !expanded" class=" text-primary"
                                            style="cursor: pointer;">
                                            <i
                                                :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                        </div>
                                        <span class="ms-3">Distribute 100% share to <span class="text-info"
                                                x-text="eqclass.equity_class_name"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div x-show="expanded" x-transition class="card-text row mt-4">
                                <div class="col-md-12 small m-3">
                                    The split hurdle defines how remaining distributable funds should be split among the
                                    included classes or paths. Learn more.
                                </div>
                                <div class="col-md-12">
                                    <div class="px-2">
                                        <label for="hurdle_type" class="form-label">Hurdle Type*</label>
                                        <input type="text" id="hurdle_type" class="form-control"
                                            placeholder="Hurdle Type" value="Split" disabled>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="px-2">
                                        <label for="split_paths" class="form-label">Split into multiple
                                            paths?*</label>
                                        <input type="text" id="split_paths" class="form-control"
                                            placeholder="Split into multiple Paths?" value="No" disabled>
                                    </div>
                                </div>
                                {{-- Included classes in input fileld tith tags --}}
                                <div class="col-md-12">
                                    <div class="px-2">
                                        <label for="included_classes" class="form-label">Included Classes*</label>
                                        <input type="text" id="included_classes" class="form-control"
                                            placeholder="Included Classes" x-model="eqclass.equity_class_name"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="px-2">
                                        <label for="class_share" class="form-label"><span
                                                x-text="eqclass.equity_class_name"></span></label>
                                        <input type="text" class="form-control" placeholder="class_share"
                                            x-model="eqclass.distribution_share" disabled>
                                    </div>
                                </div>
                                <div class="row" x-data="{ expanded: false }">
                                    <span class="advanced-expandable" @click="expanded = !expanded">Advanced</span>
                                    <div x-show="expanded" x-transition class="card-text row mt-4">
                                        <div class="ms-3">
                                            <div class="px-2 py-3">
                                                <label for="payment_as" class="form-label">What should payments toward
                                                    this hurdle count as?*</label>
                                                <input type="text" class="form-control" placeholder="Payment as"
                                                    value="Other" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Different share distribute in classes --}}
        <template x-if="!(classes[0].distribution_share == 100)">
            <div>
                <div class="card class-form-card border border-primary mb-4" x-data="{ expanded: false }">
                    <div class="card-header">
                        <div class="col-md-12">
                            <div class="d-flex">
                                <div @click="expanded = !expanded" class="text-primary" style="cursor: pointer;">
                                    <i :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                </div>
                                <span class="ms-3">
                                    Distribute
                                    <template x-for="(eqclass, index) in classes" :key="index">
                                        <span>
                                            <span x-text="eqclass.distribution_share "></span> to Path
                                            <span class="text-info" x-text="index + 1"></span>
                                            {{-- <span x-text="eqclass.equity_class_name"></span> --}}
                                            <template x-if="index < classes.length - 1">
                                                <span>, </span>
                                            </template>
                                        </span>
                                    </template>
                                    {{-- <span x-text="
                                        hurdle.preferred_return_type === 'irr' ? 'IRR' : 
                                        hurdle.preferred_return_type === 'roi' ? 'ROI' : 
                                        hurdle.preferred_return_type === 'cash_on_cash' ? 'CoC' : 
                                        hurdle.preferred_return_type === 'aar' ? 'AAR' : ''
                                    "></span> return of <span class="text-info" x-text="hurdle.upside_limit "></span> --}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div x-show="expanded" x-transition class="card-text row mt-4">
                        <div class="col-md-12 small m-3">
                            The split hurdle defines how remaining distributable funds should be split among the
                            included classes or paths. Learn more.
                        </div>
                        <div class="col-md-12">
                            <div class="px-2">
                                <label for="hurdle_type" class="form-label">Hurdle Type*</label>
                                <input type="text" id="hurdle_type" class="form-control"
                                    placeholder="Hurdle Type" :value="hurdle.preferred_return_type" disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="px-2">
                                <label for="split_paths" class="form-label">Split into multiple paths?*</label>
                                <input type="text" id="split_paths" class="form-control"
                                    placeholder="Split into multiple Paths?" value="Yes" disabled>
                            </div>
                        </div>
                        <div class="row"
                            style="border-left: 2px solid #007bff; margin-left: 30px; margin-top:30px; margin-bottom:30px; margin-right-30px;">
                            <template x-for="(eqclass, index) in classes" :key="index">

                                <div class="row">
                                    <div class="col-md-12 split-fields">
                                        <label for="included_classes" class="form-label">Path <span
                                                x-text="index + 1"></span> split percentage*</label>
                                        <input type="text" class="form-control" placeholder="class_share"
                                            x-model="eqclass.distribution_share" disabled>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <template x-for="(eqclass, index) in classes" :key="index">
                        <div :class="`col-md-${12/classes.length}`">
                            <h3 class="text-center">Path <span x-text="index + 1"></span>: <span
                                    x-text="eqclass.distribution_share "></span> of remaining funds</h3>
                            <div class="card class-form-card border border-primary mb-4" x-data="{ expanded: false }">
                                <div class="card-header">
                                    <div class="col-md-12">
                                        <div class="d-flex">
                                            <div @click="expanded = !expanded" class="text-primary"
                                                style="cursor: pointer;">
                                                <i
                                                    :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                            </div>
                                            <span class="ms-3">
                                                Path <span class="text-info" x-text="index + 1"></span> - <span
                                                    x-text="eqclass.distribution_share "></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="expanded" x-transition class="card-text row mt-4">
                                    <div class="col-md-12 small m-3">
                                        The split hurdle defines how remaining distributable funds should be split among
                                        the included classes or paths. Learn more.
                                    </div>
                                    <div class="col-md-12">
                                        <div class="px-2">
                                            <label for="hurdle_type" class="form-label">Hurdle Type*</label>
                                            <input type="text" id="hurdle_type" class="form-control"
                                                placeholder="Hurdle Type" value="Split" disabled>
                                        </div>
                                    </div>
                                    {{-- Split in --}}
                                    <div class="col-md-12">
                                        <div class="px-2">
                                            <label for="split_paths" class="form-label">Split into multiple
                                                paths?*</label>
                                            <input type="text" id="split_paths" class="form-control"
                                                placeholder="Split into multiple Paths?" value="No" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="px-2">
                                            <label for="included_classes" class="form-label">Included Classes*</label>
                                            <input type="text" id="included_classes" class="form-control"
                                                placeholder="Included Classes" x-model="eqclass.equity_class_name"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="px-2">
                                            <label for="class_share" class="form-label"><span
                                                    x-text="eqclass.equity_class_name"></span></label>
                                            <input type="text" class="form-control" placeholder="class_share"
                                                value="100" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div>
        </template>

    </div>
</template>

<template x-if="waterfall && waterfall.is_basic === 1">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-6" x-data="{ expanded: false }">
                <div class="card">
                    <div class="card-header">
                        <div class="col-md-12">
                            <div class="d-flex"
                                style="background-color: #e8f7ff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                                <div @click="expanded = !expanded" class="text-primary" style="cursor: pointer;">
                                    <i :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                </div>
                                <span class="ms-3">
                                    Distribute
                                    <template x-for="(eqclass, index) in classes" :key="index">
                                        <span>
                                            <span x-text="eqclass.distribution_share "></span> to Path
                                            <span class="text-info" x-text="index + 1"></span>
                                            {{-- <span x-text="eqclass.equity_class_name"></span> --}}
                                            <template x-if="index < classes.length - 1">
                                                <span>, </span>
                                            </template>
                                        </span>
                                    </template>
                                    {{-- <span x-text="
                                            hurdle.preferred_return_type === 'irr' ? 'IRR' : 
                                            hurdle.preferred_return_type === 'roi' ? 'ROI' : 
                                            hurdle.preferred_return_type === 'cash_on_cash' ? 'CoC' : 
                                            hurdle.preferred_return_type === 'aar' ? 'AAR' : ''
                                        "></span> return of <span class="text-info" x-text="hurdle.upside_limit "></span> --}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div x-show="expanded" x-transition class="card-body" x-show="open" x-cloak>
                        <p>The split hurdle defines how remaining distributable funds should be split among the included
                            classes or paths. <a href="#">Learn more.</a></p>
                        <div>
                            <!-- Form fields -->
                            <div class="mb-3">
                                <label for="hurdleType1" class="form-label">Hurdle type *</label>
                                <select id="hurdleType1" class="form-select">
                                    <option>Split</option>
                                    <!-- Other options -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="splitPaths1" class="form-label">Split into multiple paths? *</label>
                                <select id="splitPaths1" class="form-select">
                                    <option>No</option>
                                    <!-- Other options -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="includedClasses1" class="form-label">Included classes *</label>
                                <input type="text" id="includedClasses1" class="form-control"
                                    placeholder="Next Class" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="nextClassSplit1" class="form-label">Next Class's split *</label>
                                <input type="text" id="nextClassSplit1" class="form-control" placeholder="100%">
                            </div>
                            <div class="row" x-data="{ expanded: false }">
                                <div @click="expanded = !expanded" class="text-primary" style="cursor: pointer;">
                                    <i :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'">Advanced</i>
                                </div>
                                <div x-show="expanded" x-transition class="card-text row mt-4">
                                    <div class="ms-3">
                                        <div class="px-2 py-3">
                                            <label for="payment_as" class="form-label">What should payments
                                                toward this hurdle count as?*</label>
                                            <input type="text" class="form-control" placeholder="Payment as"
                                                value="Other" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6" x-data="{ expanded: false }">
                <div class="card">
                    <div class="card-header">
                        <div class="col-md-12">
                            <div class="d-flex"
                                style="background-color: #e8f7ff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                                <div @click="expanded = !expanded" class="text-primary" style="cursor: pointer;">
                                    <i :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'"></i>
                                </div>
                                <span class="ms-3">
                                    Distribute
                                    <template x-for="(eqclass, index) in classes" :key="index">
                                        <span>
                                            <span x-text="eqclass.distribution_share "></span> to Path
                                            <span class="text-info" x-text="index + 1"></span>
                                            {{-- <span x-text="eqclass.equity_class_name"></span> --}}
                                            <template x-if="index < classes.length - 1">
                                                <span>, </span>
                                            </template>
                                        </span>
                                    </template>
                                    {{-- <span x-text="
                                            hurdle.preferred_return_type === 'irr' ? 'IRR' : 
                                            hurdle.preferred_return_type === 'roi' ? 'ROI' : 
                                            hurdle.preferred_return_type === 'cash_on_cash' ? 'CoC' : 
                                            hurdle.preferred_return_type === 'aar' ? 'AAR' : ''
                                        "></span> return of <span class="text-info" x-text="hurdle.upside_limit "></span> --}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div x-show="expanded" x-transition class="card-body" x-show="open" x-cloak>
                        <p>The split hurdle defines how remaining distributable funds should be split among the included
                            classes or paths. <a href="#">Learn more.</a></p>
                        <div>
                            <!-- Form fields (same as above but with different IDs) -->
                            <div class="mb-3">
                                <label for="hurdleType2" class="form-label">Hurdle type *</label>
                                <select id="hurdleType2" class="form-select">
                                    <option>Split</option>
                                    <!-- Other options -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="splitPaths2" class="form-label">Split into multiple paths? *</label>
                                <select id="splitPaths2" class="form-select">
                                    <option>No</option>
                                    <!-- Other options -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="includedClasses2" class="form-label">Included classes *</label>
                                <input type="text" id="includedClasses2" class="form-control"
                                    placeholder="Test Class" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="nextClassSplit2" class="form-label">Test Class's split *</label>
                                <input type="text" id="nextClassSplit2" class="form-control" placeholder="100%">
                            </div>
                            <div class="row" x-data="{ expanded: false }">
                                <div @click="expanded = !expanded" class="text-primary" style="cursor: pointer;">
                                    <i :class="expanded ? 'bi bi-caret-down-fill' : 'bi bi-caret-right-fill'">Advanced</i>
                                </div>

                                <div x-show="expanded" x-transition class="card-text row mt-4">
                                    <div class="ms-3">
                                        <div class="px-2 py-3">
                                            <label for="payment_as" class="form-label">What should payments
                                                toward this hurdle count as?*</label>
                                            <input type="text" class="form-control" placeholder="Payment as"
                                                value="Other" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@push('scripts')
    <script>
        function basicWaterfall() {
            return {
                waterfall: @json($waterfall),
                classes: @json($classes),
            }
        }
    </script>
@endpush
