<script setup>
import { ref, reactive, computed, onMounted, watch, defineEmits } from "vue";
import Multiselect from "vue-multiselect"; // Import vue-multiselect
import Gp_Provision from "./Gp_Provision.vue";
import PercentInput from "./Core/PercentInput.vue";
const components = {
    Multiselect,
    PercentInput
};
const props = defineProps({
    classes: {
        type: Array,
        default: () => []
    },
    buckets: {
        type: Array,
        default: () => []
    },
    wfh_index: {
        type: Number,
        default: 0
    },
    hurdle: {
        type: Object,
        default: () => ({
            hurdle_type: "",
            split: "No",
            splits: [],
            included_class: [],
            classes_values: [],
            paths: [],
            notes: "",
            accrues_on: "capital_balance",
            payment_type_towards: [],
            payments_towards: "preferred_return",
            split_unpayed: "unpaid_accrual",
            accrual_cadence: "daily",
            start_date: "",
            end_date: "",
            duration: "",
            day_count: "actual_365",
            compounding_frequency: "none",
            cumulated_return_reach: "",
            stop_hurdle: null,
            gp_provision: null
        })
    },

    hurdle_type: {
        type: String,
        default: "main"
        // split hurdle or main hurdle
    },
    readonly: {
        type: Boolean,
        default: false
    }
});


// const emit = defineEmits();
const emit = defineEmits(['deleteHurdle', 'addNestedHurdle', 'removeSplitPath']);

const showGpProvision = ref(false);
const showStopCondition = ref(false);
const getNameTitle = (str) => {
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
}

const selected = ref(null);

// Ref to track the expanded state
const expanded = ref(false);
const showAdvance = ref(false);
const mergedClasses = computed(() => {
    let bucketClasses = [];
    props.buckets.forEach((bucket) => {
        bucketClasses = [...bucketClasses, ...bucket.classes];
    });
    return [...props.classes, ...bucketClasses];
});
const getClassName = (id) => {
    let name = '';
    props.classes.forEach((cls) => {
        if (cls.id == id) {
            name = cls.equity_class_name;
        }
    });
    if (name == '') {
        props.buckets.forEach((bucket) => {
            bucket.classes.forEach((cls) => {
                if (cls.id == id) {
                    name = cls.equity_class_name;
                }
            });
        });
    }
    return name;
};
const addGpProvision = () => {
    if (!props.hurdle.gp_provision) {
        // Initialize gp_provision if it doesn't exist
        props.hurdle.gp_provision = {
            deal_class_id: "",
            classes_catch_up: [],
            catch_up_splits: [],
            classify_payment: "",
            notes: "",

        };
    }
    showGpProvision.value = true;
};
const addStopCondition = () => {
    if (!props.hurdle.stop_hurdle) {
        // Initialize stop_hurdle if it doesn't exist
        props.hurdle.stop_hurdle = {
            preferred_return_type: "",
            included_class: [],
            classes_values: [],
            notes: "",
            accrues_on: "capital_balance",
            payment_type_towards: [],
            payments_towards: "preferred_return",
            split_unpayed: "unpaid_accrual",
            accrual_cadence: "daily",
            start_date: "",
            end_date: "",
            duration: "",
            day_count: "actual_365",
            compounding_frequency: "none",
            cumulated_return_reach: ""
        };
    }
    showStopCondition.value = true;
};

const hurdleTitle = (hurdle, index) => {
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
            if (hurdle.hurdle_type !== 'split') {
                str += `<span class="text-primary">${getClassName(cls.id)} </span>`;
            }
            if (hurdle.hurdle_type == 'cash_on_cash') {
                str += `until it achieves ${getNameTitle(hurdle.hurdle_type)} of ${cls.value}`;
                if (hurdle.upside_limit) {
                    str += ` with an upside limit of ${hurdle.upside_limit}`;
                }
            } else if (hurdle.hurdle_type == 'irr' || hurdle.hurdle_type == 'roi') {
                str += `until it achieves ${getNameTitle(hurdle.hurdle_type)} of ${cls.value}`;
            } else if (hurdle.hurdle_type == 'return_of_capital' || hurdle.hurdle_type ==
                'interest') {
                str +=
                    `until it receives ${getNameTitle(hurdle.hurdle_type)} of initial capital`;
            } else if (hurdle.hurdle_type == 'split') {
                if (hurdle.split == 'No') {
                    str += `${cls.value} to <span class="text-primary"> ${getClassName(cls.id)} </span> `;
                } else {
                    // str += `until it achieves ${cls.value}`;
                }
                // TODO - Add split logic with another loop
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
}
const updateClasses = (value) => {
    selected.value = value;
    props.hurdle.included_class = value.map((item) => item.id);
    props.hurdle.classes_values = value.map((item) => ({
        id: item.id,
        value: "",
    }));
};
const addSplitPath = (wfh_index) => {
    props.hurdle.splits.push({ value: "" });
};
const removeSplitPath = (wfh_index, s_index) => {
    props.hurdle.splits.splice(s_index, 1);
};
const changeSplitType = (wfh_index) => {
    if (props.hurdle.split == "No") {
        props.hurdle.splits = [];
    } else if (props.hurdle.split === 'Yes') {
        if (props.hurdle.splits.length == 0) {
            props.hurdle.splits = [{ "value": "50%" }, { "value": "50%" }];
        }
    }
};

function addNestedhurdle(hurdle, s_index) {
    if (typeof hurdle.paths === 'undefined') {
        hurdle.paths = [];
    }
    if (!hurdle.paths[s_index]) {
        hurdle.paths[s_index] = { hurdles: [] };
    }
    hurdle.paths[s_index].hurdles.push({
        hurdle_type: '',
        split: 'No',
        splits: [],
        classes_values: [],
        paths: [],
        notes: '',
        accrues_on: 'capital_balance',
        payment_type_towards: [],
        payments_towards: 'preferred_return',
        split_unpayed: 'unpaid_accrual',
        accrual_cadence: 'daily',
        start_date: '',
        end_date: '',
        duration: '',
        day_count: 'actual_365',
        compounding_frequency: 'none',
        cumulated_return_reach: ''
    });
}

const deleteHurdle = (index) => {
    // props.hurdle.paths.splice(index, 1);
    emit('deleteHurdle', index);
};

const deleteNestedHurdle = (parentHurdle, s_index, index) => {
    console.log(parentHurdle, index);
    parentHurdle.paths[s_index].hurdles.splice(index, 1);
};


// Method to prepare nested data for submission
function prepareNestedData(hurdle) {
    const data = { ...hurdle };
    if (data.paths) {
        data.paths = data.paths.map(path => ({
            ...path,
            hurdles: path.hurdles.map(hurdle => prepareNestedData(hurdle))
        }));
    }
    return data;
}

watch(() => props.hurdle, (newHurdle) => {
    selected.value = mergedClasses.value.filter(cls => newHurdle.included_class?.includes(cls.id));
}, { deep: true });

onMounted(() => {
    selected.value = mergedClasses.value.filter(cls => props.hurdle.included_class?.includes(cls.id));
    if (props.hurdle.stop_hurdle) {
        showStopCondition.value = true;
    }
});

const canAddhurdle = (index) => {
    const path = props.hurdle.paths?.[index];
    const lastHurdle = path?.hurdles?.[path.hurdles.length - 1];

    if (lastHurdle) {
        return !(lastHurdle.hurdle_type === 'split' && lastHurdle.split === 'Yes');
    }

    return true;
};

const deleteStopCondition = () => {
    props.hurdle.stop_hurdle = null;
    showStopCondition.value = false;
};

const deleteGpProvision = () => {
    props.hurdle.gp_provision = null;
    showGpProvision.value = false;
};

const paymentOptions = [
    { label: 'Preferred return', value: 'preffered_return' },
    { label: 'Interest', value: 'interest' },
    { label: 'Principal', value: 'principal' },
    { label: 'Other', value: 'other' }
];

</script>

<template>
    <div class="card class-form-card border mb-2 ">
        <div class="card-body">
            <div class="card-title class-header" @click="expanded = !expanded">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center expandable-header">
                            <i class="me-2" :class="expanded
                                ? 'bi bi-caret-down-fill text-primary'
                                : 'bi bi-caret-right-fill text-primary'
                                "></i>
                            <span class="title-span " v-html="hurdleTitle(hurdle, wfh_index)">
                            </span>
                            <div class="ms-auto d-flex align-items-center class-info-block">
                                <span v-if="!readonly" class="btn remove-icon-btn" @click="deleteHurdle(wfh_index)">
                                    <i class="las la-trash danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="expanded" class="">
                <div class="col-md-12 d-flex flex-column">
                    <label for="hurdle_type" class="font-weight-bold text-dark bg-light p-2 rounded">Hurdle type <span
                            class="text-danger">*</span></label>
                    <select class="form-control" id="hurdle_type" v-model="hurdle.hurdle_type" :disabled="readonly">
                        <option value="">Select Hurdle Type</option>
                        <option value="cash_on_cash">Cash on cash</option>
                        <option value="irr">Internal Rate of Return</option>
                        <option value="roi">Return On Investment</option>
                        <option value="split">Split</option>
                        <option value="management_fee">Management Fee</option>
                        <option value="return_of_capital">
                            Return Of Capital
                        </option>
                        <option value="cumulative_return">
                            Cumulative Return
                        </option>
                        <option value="interest">Interest</option>
                    </select>
                </div>

                <div class="col-md-12 d-flex flex-column" v-if="hurdle.hurdle_type == 'split'">
                    <div>
                        <label for="split" class="font-weight-bold text-dark bg-light p-2 rounded">Split <span
                                class="text-danger">*</span></label>
                        <select class="form-control" id="split" v-model="hurdle.split"
                            @change="changeSplitType(wfh_index)" :disabled="readonly">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>
                </div>
                <div v-if="hurdle.hurdle_type == 'split'">
                    <div col-md-12 d-flex flex-column>
                        <span>&nbsp;</span>
                    </div>
                </div>

                <div class="col-md-12 d-flex flex-column" v-if="
                    (hurdle.hurdle_type == 'split' &&
                        hurdle.split == 'No') ||
                    hurdle.hurdle_type == 'cash_on_cash' ||
                    hurdle.hurdle_type == 'irr' ||
                    hurdle.hurdle_type == 'roi' ||
                    hurdle.hurdle_type == 'return_of_capital' ||
                    hurdle.hurdle_type == 'cumulative_return' ||
                    hurdle.hurdle_type == 'interest'
                ">
                    <div>
                        <label for="included_class" class="font-weight-bold text-dark bg-light p-2 rounded">
                            Included Classes <span class="text-danger">*</span>
                        </label>
                        <!-- vue-multiselect component -->
                        <multiselect :modelValue="selected" :searchable="false" @update:modelValue="updateClasses"
                            :hideSelected="true" :options="mergedClasses" :multiple="true" @select="console.log($event)"
                            track-by="id" label="equity_class_name" placeholder="Select Classes" :disabled="readonly">
                        </multiselect>
                    </div>
                </div>

                <div v-if="
                    (hurdle.hurdle_type == 'split' &&
                        hurdle.split == 'No') ||
                    hurdle.hurdle_type == 'cash_on_cash' ||
                    hurdle.hurdle_type == 'irr' ||
                    hurdle.hurdle_type == 'roi' ||
                    hurdle.hurdle_type == 'return_of_capital' ||
                    hurdle.hurdle_type == 'cumulative_return' ||
                    hurdle.hurdle_type == 'interest'
                ">
                    <div v-for="(classItem, c_index) in hurdle.classes_values" :key="c_index">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-column">
                                <label for="class_value" class="font-weight-bold text-dark bg-light p-2 rounded">
                                    <span>{{
                                        getClassName(
                                            hurdle.classes_values[c_index].id
                                        )
                                    }}</span>
                                    <span class="tag-text">{{
                                        hurdle.hurdle_type + " (%)"
                                        }}</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <PercentInput v-model="hurdle.classes_values[c_index].value" :disabled="readonly" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-flex flex-column" v-if="hurdle.hurdle_type == 'cumulative_return'">
                    <div>
                        <label for="cumulated_return_reach"
                            class="font-weight-bold text-dark bg-light p-2 rounded">Until the following cumulative
                            return is
                            reached<span class="text-danger">*</span></label>
                        <PercentInput v-model="hurdle.cumulated_return_reach" :disabled="readonly" />
                    </div>
                </div>

                <div class="row" v-if="
                    hurdle.hurdle_type == 'split' && hurdle.split == 'Yes'
                ">
                    <div class="col-md-6 d-flex flex-column split-paths-box">
                        <div v-for="(split, s_index) in hurdle.splits" :key="s_index">
                            <div class="row">
                                <div class="col-md-12 d-flex flex-column">
                                    <div class="d-flex justify-content-between">
                                        <label for="split_value" class="font-weight-bold text-dark p-2 rounded">Split
                                            Path
                                            <span v-text="s_index + 1"></span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <span v-if="props.hurdle.splits.length === 3" class="btn remove-icon-btn"
                                            @click="
                                                removeSplitPath(
                                                    wfh_index,
                                                    s_index
                                                )
                                                "><i class="las la-trash danger"></i>
                                        </span>
                                    </div>
                                    <PercentInput v-model="hurdle.splits[s_index].value" :disabled="readonly" />
                                </div>
                            </div>
                        </div>
                        <span class=" mt-3" style="cursor: pointer; color:blue;" @click="addSplitPath(wfh_index)">+Add
                            Path</span>
                    </div>
                </div>
                <div v-if="
                    !(
                        hurdle.hurdle_type == 'split' &&
                        hurdle.split == 'Yes'
                    )
                ">
                    <div>
                        <div class="d-flex align-items-center cursor-pointer" @click="showAdvance = !showAdvance">
                            <i :class="showAdvance ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'" class="me-1"></i>
                            <span class="advance-btn fw-bold">Advanced</span>
                        </div>


                        <div class=" pt-2" v-if="showAdvance">
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="
                                hurdle.hurdle_type == 'cash_on_cash' ||
                                hurdle.hurdle_type == 'irr' ||
                                hurdle.hurdle_type == 'interset'
                            ">
                                <div>
                                    <label for="day_count" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Day count convention<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="day_count" v-model="hurdle.day_count"
                                        :disabled="readonly">
                                        <option value="actual_365">
                                            Actual/365 (most common)
                                        </option>
                                        <option value="actual_actual">
                                            Actual/Actual
                                        </option>
                                        <option value="actual_360">
                                            Actual/360
                                        </option>
                                        <option value="30_365">30/365</option>
                                        <option value="30_360">30/360</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="hurdle.hurdle_type == 'interset'">
                                <div>
                                    <label for="compounding_frequency"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Compounding frequency<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="compounding_frequency"
                                        v-model="hurdle.compounding_frequency" :disabled="readonly">
                                        <option value="none">
                                            None (most common)
                                        </option>
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quarterly">
                                            Quarterly
                                        </option>
                                        <option value="annually">
                                            Annually
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3" v-if="
                                hurdle.hurdle_type == 'interset' ||
                                hurdle.hurdle_type == 'cash_on_cash'
                            ">
                                <div>
                                    <label for="start_date" class="form-label">Start date override</label>
                                    <input type="date" id="start_date" class="form-control"
                                        placeholder="Enter override start date" v-model="hurdle.start_date"
                                        :disabled="readonly" />
                                </div>
                            </div>

                            <div class="col-md-12 mb-3" v-if="
                                hurdle.hurdle_type == 'interset' ||
                                hurdle.hurdle_type == 'cash_on_cash'
                            ">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" id="end_date" class="form-control" placeholder="Enter an end date"
                                    v-model="hurdle.end_date" :disabled="readonly" />
                            </div>
                            <div class="col-md-12 mb-3" v-if="hurdle.hurdle_type == 'interset'">
                                <div>
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="number" id="duration" class="form-control"
                                        placeholder="Enter duration in months" v-model="hurdle.duration"
                                        :disabled="readonly" />
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="
                                hurdle.hurdle_type == 'interset' ||
                                hurdle.hurdle_type == 'irr' ||
                                hurdle.hurdle_type == 'cash_on_cash'
                            ">
                                <div>
                                    <label for="accrues_on" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Accrues on<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="accrues_on" v-model="hurdle.accrues_on"
                                        :disabled="readonly">
                                        <option value="capital_balance">
                                            Capital balance (most common)
                                        </option>
                                        <option value="invested_amount">
                                            Invested amount
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="
                                hurdle.hurdle_type !== null &&
                                hurdle.hurdle_type !== ''
                            ">
                                <div>
                                    <label for="payment_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Classify payments as<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="payments_towards" v-model="hurdle.payments_towards"
                                        :disabled="readonly">
                                        <option value="preferred_return">
                                            Preferred return
                                        </option>
                                        <option value="interest">
                                            Interest
                                        </option>
                                        <option value="principal">
                                            Principal
                                        </option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3"
                                v-if="hurdle.hurdle_type !== 'split' && hurdle.hurdle_type !== 'management_fee'">
                                <div>
                                    <label for="payment_type_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Payments counted towards hurdle<span class="text-danger">*</span>
                                    </label>
                                    <multiselect v-model="hurdle.payment_type_towards" :options="paymentOptions"
                                        :multiple="true" :disabled="readonly" placeholder="Select payment types"
                                        track-by="value" label="label" id="payment_type_towards" />
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="hurdle.hurdle_type == 'cash_on_cash'">
                                <div>
                                    <label for="split_unpayed" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Split unpaid accrual to investments
                                        by<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="split_unpayed" v-model="hurdle.split_unpayed"
                                        :disabled="readonly">
                                        <option value="unpaid_accrual">
                                            Share of unpaid accrual (most
                                            common)
                                        </option>
                                        <option value="ownership_of_class">
                                            Ownership of class
                                        </option>
                                        <option value="principal">
                                            Principal
                                        </option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="
                                hurdle.hurdle_type == 'cash_on_cash' ||
                                hurdle.hurdle_type == 'interset'
                            ">
                                <div>
                                    <label for="accrual_cadence"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Accrual cadence</label>
                                    <select class="form-control" id="accrual_cadence" v-model="hurdle.accrual_cadence"
                                        :disabled="readonly">
                                        <option value="daily">
                                            Daily (most common)
                                        </option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-3" v-if="hurdle.hurdle_type !== 'split'">
                                <div>
                                    <label for="notes"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Notes</label>
                                    <textarea id="notes" class="form-control" placeholder="Enter any notes"
                                        v-model="hurdle.notes" :disabled="readonly"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="paths-wrapper" style="overflow-x: auto; white-space: nowrap;"
                    v-if="hurdle.hurdle_type == 'split' && hurdle.split == 'Yes'">
                    <div class="paths-container d-flex flex-nowrap">
                        <div class="me-3 path-box" v-for="(split, s_index) in hurdle.splits" :key="s_index">

                            <!-- Path Label -->
                            <label for="split_value" class="font-weight-bold text-dark p-2 rounded">
                                Path <span v-text="s_index + 1"></span>
                                <span class="text-danger" v-text="` ${split.value}`"></span> of remaining funds
                            </label>

                            <!-- Nested Hurdles -->
                            <div v-if="hurdle.paths && hurdle.paths[s_index]">
                                <div v-for="(nestehurdle, nh_index) in hurdle.paths[s_index].hurdles"
                                    :key="`nh${nh_index}`">
                                    <HurdleComponent :hurdle="nestehurdle" :wfh_index="nh_index" :classes="classes"
                                        :buckets="buckets" hurdle_type="split" :readonly="readonly"
                                        @deleteHurdle="deleteNestedHurdle(hurdle, s_index, nh_index)" />
                                </div>
                            </div>

                            <!-- Dropzone Button (Centered) -->
                            <div class="dropzone mt-4 p-3 bg-light text-center mx-auto" role="button"
                                v-if="canAddhurdle(s_index) && !readonly" @click="addNestedhurdle(hurdle, s_index)">
                                <p class="mb-0">
                                    Add hurdle to Split Path <span v-text="s_index + 1"></span>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-2 d-flex flex-column">
                    <a v-if="!showStopCondition && (hurdle.hurdle_type == 'split' &&
                        hurdle.split == 'No') && !readonly"  class="cursor-pointer fw-bold mt-3" @click="addStopCondition">
                        Stop condition
                    </a>

                    <a 
                        v-if="hurdle.gp_provision == null && (hurdle.hurdle_type == 'cash_on_cash' || hurdle.hurdle_type == 'irr') && !readonly"
                        class=" cursor-pointer fw-bold mt-3" @click="addGpProvision">
                        + add Gp Provision
                    </a>
                </div>

            </div>
            <gp-provision
                v-if="hurdle.gp_provision && (hurdle.hurdle_type == 'cash_on_cash' || hurdle.hurdle_type == 'irr') && !readonly"
                :classes="classes" :buckets="buckets" :gp_provision="hurdle.gp_provision" :readonly="readonly"
                @deleteGpProvision="deleteGpProvision" />
        </div>
    </div>
    <div>
        
    </div>
    <stop-condition-template v-if="showStopCondition && (hurdle.hurdle_type == 'split' && hurdle.split == 'No') && !readonly"
        :wfh_index="index" :hurdle="hurdle" :classes="classes" :buckets="buckets" :stop_hurdle="hurdle.stop_hurdle"
        :readonly="readonly" @deletestop_hurdle="deleteStopCondition">
    </stop-condition-template>
</template>

<script>
export default {
    name: 'HurdleComponent',
}


</script>

<style scoped>
.paths-container .path-box {
    min-width: 30vw;
    max-width: 50vw;
    border-right: 1px solid #541567;
    padding: 10px;
    flex-shrink: 0;
}

.dropzone {
    border: 2px dashed #541567;
    border-radius: 5px;
    color: #541567;
    cursor: pointer;
    width: 90%;
    height: 60px;
}

.remove-icon-btn {
    cursor: pointer;
    color: red;
}

.remove-icon-btn i {
    color: red;
}

.split-paths-box {
    background-color: #F6F9FE;
    margin-top: 20px;
    border-left: 5px solid #0D6EFD;
    padding: 10px;
}

.title-span {
    color: #5b6e88;
    margin-bottom: 0;
    white-space: normal;
    width: auto;
    font-weight: bold;
    font-size: 14px;

}


.expandable-header {
    cursor: pointer;
    background-color: #f3f6f7;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}
</style>
