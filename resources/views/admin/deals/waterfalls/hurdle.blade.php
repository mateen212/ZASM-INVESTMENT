<template x-component="hurdle">
    <div class="card class-form-card border border-primary mb-2" x-data="{ ...hurdle() , ...$el.parentElement.data()}"
        @notify="setClasses($event.detail,'wfhurdle', wfh_index)">
        <div class="card-body" x-data="{ expanded: false }">
            <div class="card-title class-header">
                <div class="row">
                    <div class="col-md-12" @click="expanded = !expanded">
                        <div class="d-flex align-items-center"
                            style="background-color: #e8f7ff; padding: 15px; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">
                            <i class="me-2" :class="expanded ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'"></i>
                                <span class=""
                                    x-html="hurdleTitle(dhurdle, wfh_index)">
                                </span>
                            <div class="ms-auto d-flex align-items-center class-info-block">

                                {{-- Delete Button --}}
                                {{--  <div class="ms-3">
                                    <button class="btn btn-danger" title="Delete hurdle"
                                        @click="deleteConfirmation('waterfallhurdle', null, null, null, wfh_index)">
                                        <i class="las la-trash"></i>
                                    </button>
                                </div>  --}}
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
                    <select class="form-control" id="hurdle_type" x-model="dhurdle.hurdle_type" :disabled="readonly"
                        x-init="$watch('dhurdle.hurdle_type', value => {
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
                <template x-if="dhurdle.hurdle_type == 'split'">
                    <div class="col-md-4 d-flex flex-column">
                        <label for="split" class="font-weight-bold text-dark bg-light p-2 rounded">Split
                            <span class="text-danger">*</span></label>
                        <select class="form-control" id="split" x-model="dhurdle.split" @change="changeSplitType(wfh_index)" :disabled="readonly">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                </template>

                <template x-if="dhurdle.hurdle_type == 'split'">
                    <div col-md-4 d-flex flex-column>
                        <span >&nbsp;</span>
                    </div>
                </template>

                {{--  included class  --}}
                <template x-if="(dhurdle.hurdle_type == 'split' && dhurdle.split == 'No') || dhurdle.hurdle_type == 'cash_on_cash' || dhurdle.hurdle_type == 'irr' || dhurdle.hurdle_type == 'roi' || dhurdle.hurdle_type == 'return_of_capital' || dhurdle.hurdle_type == 'cumulative_return' || dhurdle.hurdle_type == 'interest'">
                    <div class="col-md-4 d-flex flex-column">
                        <label for="included_class"
                            class="font-weight-bold text-dark bg-light p-2 rounded">Included Classes <span
                                class="text-danger">*</span></label>
                        <select :id="'included_class_' + wfh_index"
                            class="included_class form-select js-example-basic-multiple"
                            multiple="multiple"
                            :disabled="readonly">
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
                <template x-if="(dhurdle.hurdle_type == 'split' && dhurdle.split == 'No') || dhurdle.hurdle_type == 'cash_on_cash' || dhurdle.hurdle_type == 'irr' || dhurdle.hurdle_type == 'roi' || dhurdle.hurdle_type == 'return_of_capital' || dhurdle.hurdle_type == 'cumulative_return' || dhurdle.hurdle_type == 'interest'">
                    <template x-for="(class, c_index) in dhurdle.classes_values" :key="c_index">
                        <div class="row">
                            <div class="col-md-4 d-flex flex-column">
                                <label for="class_value"
                                    class="font-weight-bold text-dark bg-light p-2 rounded"><span
                                        x-text="getClassName(dhurdle.classes_values[c_index].id)"></span>
                                        <span class="tag-text" x-text="dhurdle.hurdle_type + ' (%)'"></span>
                                        <span class="text-danger">*</span></label>
                                <input type="text" x-on:input="percentFormat($el)" min="1" max="100" class="form-control" id="class_value"
                                    x-model="dhurdle.classes_values[c_index].value" :disabled="readonly">
                            </div>
                        </div>
                    </template>
                </template>

                <template x-if="dhurdle.hurdle_type =='cumulative_return'">
                    <div class="col-md-4 d-flex flex-column">
                        <label for="cumulated_return_reach"
                            class="font-weight-bold text-dark bg-light p-2 rounded">Until the following
                            cumulative return is reached<span class="text-danger">*</span></label>
                        <input type="text" x-on:input="percentFormat($el)" class="form-control" id="cumulated_return_reach"
                            x-model="dhurdle.cumulated_return_reach" :disabled="readonly">
                    </div>
                </template>

                {{-- Paths if hurdle type split and split == yes --}}
                <template x-if="dhurdle.hurdle_type == 'split' && dhurdle.split == 'Yes'">
                    <div class="col-md-4 d-flex flex-column">
                        <template x-for="(split, s_index) in dhurdle.splits" :key="s_index">
                            <div class="row">
                                <div class="col-md-12 d-flex flex-column">
                                    <div class="d-flex justify-content-between">    
                                        <label for="split_value"
                                        class="font-weight-bold text-dark p-2 rounded">Split Path <span x-text="s_index + 1"></span> <span class="text-danger">*</span></label>
                                        <span class="btn text-danger" @click="removeSplitPath(wfh_index, s_index)"><i class="las la-trash danger"></i></span>
                                    </div>
                                        <input type="text" x-on:input="percentFormat($el)" min="1" max="100" class="form-control"
                                        x-model="dhurdle.splits[s_index].value" :disabled="readonly">
                                </div>
                            </div>
                        </template>
                        <span class="btn btn-primary mt-3" @click="addSplitPath(wfh_index)">Add Split Path</span>
                    </div>
                </template>

                <template x-if="!(dhurdle.hurdle_type == 'split' && dhurdle.split == 'Yes')">
                    <div x-data="{ expanded: false }">
                        <button class="btn btn-primary mt-3" @click="expanded = !expanded">Advanced</button>
                        <div class="row pt-2" x-show="expanded" x-collapse>
                            <!-- Day count convention -->
                            <template
                                x-if="dhurdle.hurdle_type =='cash_on_cash' || dhurdle.hurdle_type =='irr' || dhurdle.hurdle_type =='interset'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="day_count"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Day count convention<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="day_count" x-model="dhurdle.day_count" :disabled="readonly">   
                                        <option value="actual_365">Actual/365 (most common)</option>
                                        <option value="actual_actual">Actual/Actual</option>
                                        <option value="actual_360">Actual/360</option>
                                        <option value="30_365">30/365</option>
                                        <option value="30_360">30/360</option>
                                    </select>
                                </div>
                            </template>
                            <!-- Compounding frequency -->
                            <template x-if="dhurdle.hurdle_type =='interset'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="compounding_frequency"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Compounding frequency<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="compounding_frequency"
                                        x-model="dhurdle.compounding_frequency" :disabled="readonly">
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
                                x-if="dhurdle.hurdle_type =='interset' || dhurdle.hurdle_type =='cash_on_cash' ">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Start date override</label>
                                    <input type="date" onclick="this.showPicker()" id="start_date" class="form-control"
                                        placeholder="Enter override start date" x-model="dhurdle.start_date" :disabled="readonly">
                                </div>
                            </template>
                            {{--  end date override  --}}
                            <template
                                x-if="dhurdle.hurdle_type =='interset' || dhurdle.hurdle_type =='cash_on_cash' ">
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">End date</label>
                                    <input type="date" onclick="this.showPicker()" id="end_date" class="form-control"
                                        placeholder="Enter an end date" x-model="dhurdle.end_date" :disabled="readonly">
                                </div>
                            </template>
                            {{--  duration  --}}
                            <template x-if="dhurdle.hurdle_type =='interset' ">
                                <div class="col-md-6 mb-3">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="number" id="duration" class="form-control"
                                        placeholder="Enter duration in months" x-model="dhurdle.duration" :disabled="readonly">
                                </div>
                            </template>
                            {{--  accrues on  --}}
                            <template
                                x-if="dhurdle.hurdle_type =='interset' || dhurdle.hurdle_type =='irr' || dhurdle.hurdle_type == 'cash_on_cash'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="accrues_on"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Accrues on<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="accrues_on"
                                        x-model="dhurdle.accrues_on" :disabled="readonly">
                                        <option value="capital_balance">Capital balance (most common)</option>
                                        <option value="invested_amount">Invested amount</option>
                                    </select>
                                </div>
                            </template>
                            {{--  What should payments toward this hurdle count as?  --}}
                            <template x-if="dhurdle.hurdle_type !== null && dhurdle.hurdle_type !== ''">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="payment_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        What should payments toward this hurdle count as?<span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="payments_towards"
                                        x-model="dhurdle.payments_towards" :disabled="readonly">
                                        <option value="preffered_return">Preferred return</option>
                                        <option value="interest">Interest</option>
                                        <option value="principal">Principal</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </template>
                            {{--  What payment types count toward this hurdle?  --}}
                            <template
                                x-if="dhurdle.hurdle_type !== 'split' && dhurdle.hurdle_type !== 'management_fee'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="payment_type_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        What payment types count toward this hurdle?<span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="payment_type_towards"
                                        x-model="dhurdle.payment_type_towards" :disabled="readonly">
                                        <option value="preffered_return">Preferred return</option>
                                        <option value="interest">Interest</option>
                                        <option value="principal">Principal</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </template>
                            {{--  Split unpaid accrual to investments by  --}}
                            <template x-if="dhurdle.hurdle_type == 'cash_on_cash' ">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="split_unpayed"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Split unpaid accrual to investments by<span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="split_unpayed"
                                        x-model="dhurdle.split_unpayed" :disabled="readonly">
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
                                x-if="dhurdle.hurdle_type == 'cash_on_cash' || dhurdle.hurdle_type == 'interset'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="accrual_cadence"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Accrual
                                        cadence</label>
                                    <select class="form-control" id="accrual_cadence"
                                        x-model="dhurdle.accrual_cadence" :disabled="readonly">
                                        <option value="daily">Daily (most common)</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </template>
                            {{--  notes  --}}
                            <template x-if="dhurdle.hurdle_type !== 'split'">
                                <div class="col-md-6 d-flex flex-column mb-3">
                                    <label for="notes"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Notes</label>
                                    <textarea id="notes" class="form-control" placeholder="Enter any notes" x-model="dhurdle.notes" :disabled="readonly"></textarea>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- If Hurdle is split in paths --}}
                <template x-if="(dhurdle.hurdle_type == 'split' && dhurdle.split == 'Yes')">
                    <div class="d-flex " style="overflow-x: auto;">
                        <template x-for="(split, s_index) in dhurdle.splits" :key="s_index">
                            <div class="d-flex flex-column" style="min-width: 30vw; border-right: 1px solid #541567;">
                                <label for="split_value" class="font-weight-bold text-dark p-2 rounded">Path <span x-text="s_index + 1"></span> <span class="text-danger" x-text="`${split.value}%`"></span> of remaining funds</label>
                                
                                <template x-if="typeof dhurdle.paths !== 'undefined' && typeof dhurdle.paths[s_index] !== 'undefined'">
                                    <template x-for="(nestedHurdle, nh_index) in dhurdle.paths[s_index].hurdles" :key="`nh${nh_index}`">
                                        <xa-hurdle :dhurdle="nestedHurdle" :wfh_index="wfh_index" :s_index="s_index" :nh_index="nh_index" />
                                    </template>
                                </template>

                                <div class="dropzone mt-4 p-4 bg-light text-center" role="button" @click="addNestedHurdle(dhurdle, s_index)">
                                    <p class="mb-0">Add hurdle to Split Path <span x-text="s_index + 1"></span></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

@push('script')

<script>
    function hurdle() {
        return {
            dhurdle: {
                hurdle_type: '',
                split: 'No',
                splits: [],
                classes_values: [],
                paths: [],
                notes: '',
                accrues_on: '',
                payment_type_towards: '',
                payments_towards: '',
                split_unpayed: '',
                accrual_cadence: '',
                start_date: '',
                end_date: '',
                duration: '',
                day_count: '',
                compounding_frequency: '',
                cumulated_return_reach: '',
            },
            wfh_index: 0,
            classes: [],
            eqclass: [],
            eqbclass: [],
            cb_hurdle: false,
            initSelect2() {
                $('.js-example-basic-multiple').select2();
            },
            getClassName(id) {
                return this.classes.find(c => c.id == id).equity_class_name;
            },
            hurdleTitle(hurdle, index) {
                debugger
                let str = '';
                if (index === 0) {
                    str += ' Distribute to '; 
                } else {
                    str += ' then '
                }

                if(hurdle.hurdle_type == 'management_fee'){
                    str = ` Pay `;
                }

                if((hurdle.hurdle_type == 'split' && hurdle.split == 'No') || hurdle.hurdle_type == 'cash_on_cash' || hurdle.hurdle_type == 'irr' || hurdle.hurdle_type == 'roi' || hurdle.hurdle_type == 'return_of_capital' || hurdle.hurdle_type == 'cumulative_return' || hurdle.hurdle_type == 'interest')
                {
                    debugger
                    hurdle.classes_values?.forEach((cls, index) => {
                        if (index > 0) {
                            str += ', ';
                        }
                        str += `<span class="text-primary">${this.getClassName(cls.id)} </span>`;
                        if(hurdle.hurdle_type == 'cash_on_cash' || hurdle.hurdle_type == 'irr' || hurdle.hurdle_type == 'roi'){
                            str += `until it achieves ${hurdle.hurdle_type} of ${cls.value}`;
                        }else if(hurdle.hurdle_type == 'return_of_capital' || hurdle.hurdle_type == 'interest'){
                            str += `until it receives ${hurdle.hurdle_type} of initial capital`;
                        }else if(hurdle.hurdle_type == 'split') {
                            // TODO - Add split logic with another loop
                            str += `until it achieves ${cls.value}`;
                        }
                    });
                }

                if(hurdle.hurdle_type == 'split' && hurdle.split == 'Yes') {
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
            percentFormat(el) {
                el.value = el.value.replace(/[^0-9]/g, '');
            },
            changeSplitType(index) {
                if (this.dhurdle.split == 'Yes') {
                    this.dhurdle.splits = [];
                }
            },
            addSplitPath(index) {
                this.dhurdle.splits.push({
                    value: ''
                });
            },
            removeSplitPath(wfh_index, s_index) {
                this.dhurdle.splits.splice(s_index, 1);
            },
            addNestedHurdle(dhurdle, s_index) {
                debugger;
                // if (typeof dhurdle.paths[s_index] === 'undefined') {
                //     dhurdle.paths[s_index] = {
                //         hurdles: []
                //     };
                // }
                // dhurdle.paths[s_index].hurdles.push({
                //     hurdle_type: '',
                //     split: 'No',
                //     splits: [],
                //     classes_values: [],
                //     notes: '',
                //     accrues_on: '',
                //     payment_type_towards: '',
                //     payments_towards: '',
                //     split_unpayed: '',
                //     accrual_cadence: '',
                //     start_date: '',
                //     end_date: '',
                //     duration: '',
                //     day_count: '',
                //     compounding_frequency: '',
                //     cumulated_return_reach: '',
                // });
            },
            deleteConfirmation(type, index, s_index, nh_index, wfh_index) {
                if (confirm('Are you sure you want to delete this hurdle?')) {
                    if (type == 'waterfallhurdle') {
                        this.$el.parentElement.remove();
                    } else if (type == 'splitpath') {
                        this.dhurdle.splits.splice(s_index, 1);
                    } else if (type == 'nestedhurdle') {
                        this.dhurdle.paths[s_index].hurdles.splice(nh_index, 1);
                    }
                }
            },
            setClasses(classes, type, index) {
                if (type == 'wfhurdle') {
                    this.dhurdle.classes_values = classes;
                }
            },
            init() {
                debugger;
                this.initSelect2();
            }
        }
    }

    document.querySelectorAll('[x-component]').forEach(el => {
        const componentName = 'xa-' + el.getAttribute('x-component');
        debugger;
        class Component extends HTMLElement {
            connectedCallback() {
                this.append(el.content.cloneNode(true));
            }

            data() {
                const attributes = this.getAttributeNames();
                const data = {};
                attributes.forEach(attr => {
                    if(attr == ':dhurdle'){
                        data[attr] = JSON.parse(this.getAttribute(attr));
                    }else{
                        data[attr] = this.getAttribute(attr);
                    }
                });
                return data;
            }
        }
        customElements.define(componentName, Component);
    });
</script>

@endpush