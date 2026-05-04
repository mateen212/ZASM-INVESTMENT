<div class="card class-form-card border border-primary mb-2"
    @notify="setClasses($event.detail,'wfhurdle', wfh_index)">
    <div class="card-body" x-data="{ expanded: false }">
        <div class="card-title class-header">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-center"
                        style="background-color: #e8f7ff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                        <i class="me-2" @click="expanded = !expanded" :class="expanded ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'"></i>
                            <span class=""
                                x-html="hurdleTitle(nestedHurdle, wfh_index)">
                            </span>
                        <div class="ms-auto d-flex align-items-center class-info-block">

                            {{-- Delete Button --}}
                            <div class="ms-3">
                                <button class="btn btn-danger" title="Delete hurdle" @click="removeNestentedHurdle(dhurdle, s_index, nh_index)">
                                    <i class="las la-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @csrf
        <div class="row" x-show="expanded">

            <div class="col-md-4 d-flex flex-column">
                <label for="hurdle_type" class="font-weight-bold text-dark bg-light p-2 rounded">Hurdle
                    type <span class="text-danger">*</span></label>
                <select class="form-control" id="hurdle_type" x-model="nestedHurdle.hurdle_type"
                    x-init="$watch('nestedHurdle.hurdle_type', value => {
                        initSelect2();
                    })">
                    <option value="">Select Hurdle Type</option>
                    <option value="cash_on_cash">Cash on cash</option>
                    <option value="irr">Internal Rate of Return</option>
                    <option value="roi">Return On Investment</option>
                    <option value="split">Split</option>
                    <option value="management_fee">Management Fee</option>
                    <option value="return_of_capital">Return Of Capital</option>
                    <option value="cumulative_return">Cumulative Return</option>
                    <option value="interest">Interest</option>
                </select>
            </div>
            {{--  Split  --}}
            <template x-if="nestedHurdle.hurdle_type == 'split'">
                <div class="col-md-4 d-flex flex-column">
                    <label for="split" class="font-weight-bold text-dark bg-light p-2 rounded">Split
                        <span class="text-danger">*</span></label>
                    <select class="form-control" id="split" x-model="nestedHurdle.split" @change="changeSplitType(wfh_index)">
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            </template>

            <template x-if="nestedHurdle.hurdle_type == 'split'">
                <div col-md-4 d-flex flex-column>
                    <span >&nbsp;</span>
                </div>
            </template>

            {{--  included class  --}}
            <template x-if="(nestedHurdle.hurdle_type == 'split' && nestedHurdle.split == 'No') || nestedHurdle.hurdle_type == 'cash_on_cash' || nestedHurdle.hurdle_type == 'irr' || nestedHurdle.hurdle_type == 'roi' || nestedHurdle.hurdle_type == 'return_of_capital' || nestedHurdle.hurdle_type == 'cumulative_return' || nestedHurdle.hurdle_type == 'interest'">
                <div class="col-md-4 d-flex flex-column">
                    <label for="included_class"
                        class="font-weight-bold text-dark bg-light p-2 rounded">Included Classes <span
                            class="text-danger">*</span></label>
                    <select :id="'included_class_' + wfh_index"
                        class="included_class form-select js-example-basic-multiple"
                        multiple="multiple"
                        >
                        {{-- <option value="">Select Classes</option> --}}
                        <template x-for="(classoption, cl_index) in mergedClasses" :key="cl_index">
                            <option x-text="classoption.equity_class_name" :value="classoption.id">
                            </option>
                        </template>
                    </select>

                </div>
            </template>
            {{-- End Include classes  --}}
            
            {{-- included classes values in percentage values  --}}
            <template x-if="(nestedHurdle.hurdle_type == 'split' && nestedHurdle.split == 'No') || nestedHurdle.hurdle_type == 'cash_on_cash' || nestedHurdle.hurdle_type == 'irr' || nestedHurdle.hurdle_type == 'roi' || nestedHurdle.hurdle_type == 'return_of_capital' || nestedHurdle.hurdle_type == 'cumulative_return' || nestedHurdle.hurdle_type == 'interest'">
                <template x-for="(class, c_index) in nestedHurdle.classes_values" :key="c_index">
                    <div class="row">
                        <div class="col-md-4 d-flex flex-column">
                            <label for="class_value"
                                class="font-weight-bold text-dark bg-light p-2 rounded"><span
                                    x-text="getClassName(nestedHurdle.classes_values[c_index].id)"></span>
                                    <span class="tag-text" x-text="nestedHurdle.hurdle_type + ' (%)'"></span>
                                    <span class="text-danger">*</span></label>
                            <input type="text" x-on:input="percentFormat($el)" min="1" max="100" class="form-control" id="class_value"
                                x-model="nestedHurdle.classes_values[c_index].value">
                        </div>
                    </div>
                </template>
            </template>

            <template x-if="nestedHurdle.hurdle_type =='cumulative_return'">
                <div class="col-md-4 d-flex flex-column">
                    <label for="cumulated_return_reach"
                        class="font-weight-bold text-dark bg-light p-2 rounded">Until the following
                        cumulative return is reached<span class="text-danger">*</span></label>
                    <input type="text" x-on:input="percentFormat($el)" class="form-control" id="cumulated_return_reach"
                        x-model="nestedHurdle.cumulated_return_reach">
                </div>
            </template>

            {{-- Paths if hurdle type split and split == yes --}}
            <template x-if="nestedHurdle.hurdle_type == 'split' && nestedHurdle.split == 'Yes'">
                <div class="col-md-4 d-flex flex-column">
                    <template x-for="(split, s_index) in nestedHurdle.splits" :key="s_index">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-column">
                                <div class="d-flex justify-content-between">    
                                    <label for="split_value"
                                    class="font-weight-bold text-dark p-2 rounded">Split Path <span x-text="s_index + 1"></span> <span class="text-danger">*</span></label>
                                    <span class="btn text-danger" @click="removeSplitPath(wfh_index, s_index)"><i class="las la-trash danger"></i></span>
                                </div>
                                    <input type="text" x-on:input="percentFormat($el)" min="1" max="100" class="form-control"
                                    x-model="nestedHurdle.splits[s_index].value">
                            </div>
                        </div>
                    </template>
                    <span class="btn btn-primary mt-3" @click="addSplitPath(wfh_index)">Add Split Path</span>
                </div>
            </template>

            <template x-if="!(nestedHurdle.hurdle_type == 'split' && nestedHurdle.split == 'Yes')">
                <div x-data="{ expanded: false }">
                    <button class="btn btn-primary mt-3" @click="expanded = !expanded">Advanced</button>
                    <div class="row pt-2" x-show="expanded" x-collapse>
                        <!-- Day count convention -->
                        <template
                            x-if="nestedHurdle.hurdle_type =='cash_on_cash' || nestedHurdle.hurdle_type =='irr' || nestedHurdle.hurdle_type =='interset'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="day_count"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    Day count convention<span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="day_count" x-model="nestedHurdle.day_count">
                                    <option value="actual_365">Actual/365 (most common)</option>
                                    <option value="actual_actual">Actual/Actual</option>
                                    <option value="actual_360">Actual/360</option>
                                    <option value="30_365">30/365</option>
                                    <option value="30_360">30/360</option>
                                </select>
                            </div>
                        </template>
                        <!-- Compounding frequency -->
                        <template x-if="nestedHurdle.hurdle_type =='interset'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="compounding_frequency"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    Compounding frequency<span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="compounding_frequency"
                                    x-model="nestedHurdle.compounding_frequency">
                                    <option value="none">None (most common)</option>
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                </select>
                            </div>
                        </template>
                        {{--  start date override  --}}
                        <template
                            x-if="nestedHurdle.hurdle_type =='interset' || nestedHurdle.hurdle_type =='cash_on_cash' ">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start date override</label>
                                <input type="date" onclick="this.showPicker()" id="start_date" class="form-control"
                                    placeholder="Enter override start date" x-model="nestedHurdle.start_date">
                            </div>
                        </template>
                        {{--  end date override  --}}
                        <template
                            x-if="nestedHurdle.hurdle_type =='interset' || nestedHurdle.hurdle_type =='cash_on_cash' ">
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" onclick="this.showPicker()" id="end_date" class="form-control"
                                    placeholder="Enter an end date" x-model="nestedHurdle.end_date">
                            </div>
                        </template>
                        {{--  duration  --}}
                        <template x-if="nestedHurdle.hurdle_type =='interset' ">
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="number" id="duration" class="form-control"
                                    placeholder="Enter duration in months" x-model="nestedHurdle.duration">
                            </div>
                        </template>
                        {{--  accrues on  --}}
                        <template
                            x-if="nestedHurdle.hurdle_type =='interset' || nestedHurdle.hurdle_type =='irr' || nestedHurdle.hurdle_type == 'cash_on_cash'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="accrues_on"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    Accrues on<span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="accrues_on"
                                    x-model="nestedHurdle.accrues_on">
                                    <option value="capital_balance">Capital balance (most common)</option>
                                    <option value="invested_amount">Invested amount</option>
                                </select>
                            </div>
                        </template>
                        {{--  What should payments toward this hurdle count as?  --}}
                        <template x-if="nestedHurdle.hurdle_type !== null && nestedHurdle.hurdle_type !== ''">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="payment_towards"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    What should payments toward this hurdle count as?<span
                                        class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="payments_towards"
                                    x-model="nestedHurdle.payments_towards">
                                    <option value="preffered_return">Preferred return</option>
                                    <option value="interest">Interest</option>
                                    <option value="principal">Principal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </template>
                        {{--  What payment types count toward this hurdle?  --}}
                        <template
                            x-if="nestedHurdle.hurdle_type !== 'split' && nestedHurdle.hurdle_type !== 'management_fee'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="payment_type_towards"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    What payment types count toward this hurdle?<span
                                        class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="payment_type_towards"
                                    x-model="nestedHurdle.payment_type_towards">
                                    <option value="preffered_return">Preferred return</option>
                                    <option value="interest">Interest</option>
                                    <option value="principal">Principal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </template>
                        {{--  Split unpaid accrual to investments by  --}}
                        <template x-if="nestedHurdle.hurdle_type == 'cash_on_cash' ">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="split_unpayed"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">
                                    Split unpaid accrual to investments by<span
                                        class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="split_unpayed"
                                    x-model="nestedHurdle.split_unpayed">
                                    <option value="unpaid_accrual">Share of unpaid accrual (most common)
                                    </option>
                                    <option value="ownership_of_class">Ownership of class</option>
                                    <option value="principal">Principal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </template>
                        {{--  Accrual cadence  --}}
                        <template
                            x-if="nestedHurdle.hurdle_type == 'cash_on_cash' || nestedHurdle.hurdle_type == 'interset'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="accrual_cadence"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">Accrual
                                    cadence</label>
                                <select class="form-control" id="accrual_cadence"
                                    x-model="nestedHurdle.accrual_cadence">
                                    <option value="daily">Daily (most common)</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </template>
                        {{--  notes  --}}
                        <template x-if="nestedHurdle.hurdle_type !== 'split'">
                            <div class="col-md-6 d-flex flex-column mb-3">
                                <label for="notes"
                                    class="font-weight-bold text-dark bg-light p-2 rounded">Notes</label>
                                <textarea id="notes" class="form-control" placeholder="Enter any notes" x-model="nestedHurdle.notes"></textarea>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- If Hurdle is split in paths --}}
            <template x-if="(nestedHurdle.hurdle_type == 'split' && nestedHurdle.split == 'Yes')">
                <div class="d-flex " style="overflow-x: auto;">
                    <template x-for="(split, s_index) in nestedHurdle.splits" :key="s_index">
                        <div class="d-flex flex-column" style="min-width: 30vw; border-right: 1px solid #541567;">
                            <label for="split_value" class="font-weight-bold text-dark p-2 rounded">Path <span x-text="s_index + 1"></span> <span class="text-danger" x-text="`${split.value}%`"></span> of remaining funds</label>
                            <div class="dropzone mt-4 p-4 bg-light text-center" role="button" @click="addNestenestedHurdle(nestedHurdle, s_index)">
                                <p class="mb-0">Add hurdle to Split Path <span x-text="s_index + 1"></span></p>
                            </div>
                            <template x-for="(nestenestedHurdle, nh_index) in split.hurdles" :key="nh_index">
                                <div class="card class-form-card border border-primary mb-2">
                                    <div class="card-body" x-data="{ expanded: false }">
                                        <div class="card-title class-header">
                                            <div class="row">
                                                <div class="col-md-12" @click="expanded = !expanded">
                                                    <div class="d-flex align-items-center" style="background-color: #e8f7ff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                                                        <i class="me-2" :class="expanded ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'"></i>
                                                        <span class="" x-html="hurdleTitle(nestenestedHurdle, nh_index)"></span>
                                                        <div class="ms-auto d-flex align-items-center class-info-block">
                                                            <div class="ms-3">
                                                                <button class="btn btn-danger" title="Delete hurdle" @click="deleteConfirmation('nestenestedHurdle', nestedHurdle)">
                                                                    <i class="las la-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @csrf
                                        <div class="row" x-show="expanded">
                                            <!-- Nested Hurdle Form Fields -->
                                            <!-- Similar to the main hurdle form fields -->
                                            <!-- ...existing code for form fields... -->
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>