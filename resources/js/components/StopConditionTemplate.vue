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
    stop_hurdle: {
        type: Object,
        default: () => ({
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
        })
    },
    preferred_return_type: {
        type: String,
        default: "main"
        // split stop_hurdle or main stop_hurdle
    },
    readonly: {
        type: Boolean,
        default: false
    }
});


// const emit = defineEmits();
const emit = defineEmits(['deleteStopCondition', 'update']);

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
const expanded = ref(true);
const showAdvance = ref(true);
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
    showGpProvision.value = true;
};
const addStopCondition = () => {
    showStopCondition.value = true;
};
const stop_hurdleTitle = (stop_hurdle, index) => {
    let str = 'until';
    // if (index === 0) {
    //     str += ' Distribute to ';
    // } else {
    //     str += ' then ';
    // }

    // if (stop_hurdle.preferred_return_type == 'management_fee') {
    //     str = ` Pay `;
    // }

    if ( stop_hurdle.preferred_return_type == 'cash_on_cash' ||
        stop_hurdle.preferred_return_type == 'irr' || stop_hurdle.preferred_return_type == 'roi' || stop_hurdle.preferred_return_type ==
        'return_of_capital' || stop_hurdle.preferred_return_type == 'cumulative_return' || stop_hurdle.preferred_return_type == 'interest'
    ) {
        stop_hurdle.classes_values?.forEach((cls, index) => {
            if (index > 0) {
                str += ', ';
            }
            str += `<span class="text-primary mx-2">${getClassName(cls.id)}</span>`;
            if (stop_hurdle.preferred_return_type == 'cash_on_cash') {
                str += `until it achieves ${getNameTitle(stop_hurdle.preferred_return_type)} of ${cls.value}`;
                if (stop_hurdle.upside_limit) {
                    str += ` with an upside limit of ${stop_hurdle.upside_limit}`;
                }
            } else if (stop_hurdle.preferred_return_type == 'irr' || stop_hurdle.preferred_return_type == 'roi') {
                str += `until it achieves ${getNameTitle(stop_hurdle.preferred_return_type)} of ${cls.value}`;
            } else if (stop_hurdle.preferred_return_type == 'return_of_capital' || stop_hurdle.preferred_return_type ==
                'interest') {
                str +=
                    `until it receives ${getNameTitle(stop_hurdle.preferred_return_type)} of initial capital`;
            } else if (stop_hurdle.preferred_return_type == 'split') {
                // TODO - Add split logic with another loop
                str += `until it achieves ${cls.value}`;
            }
        });
    }

    if (stop_hurdle.preferred_return_type == 'split' && stop_hurdle.split == 'Yes') {
        str = ' Distribute ';
        stop_hurdle.splits.forEach((split, index) => {
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
    props.stop_hurdle.included_class = value.map((item) => item.id);
    props.stop_hurdle.classes_values = value.map((item) => ({
        id: item.id,
        value: "",
    }));
};


const deletestop_hurdle = (index) => {
    // props.stop_hurdle.paths.splice(index, 1);
    emit('deletestop_hurdle');
};

// Method to prepare nested data for submission
function prepareNestedData(stop_hurdle) {
    const data = { ...stop_hurdle };
    if (data.paths) {
        data.paths = data.paths.map(path => ({
            ...path,
            stop_hurdles: path.stop_hurdles.map(stop_hurdle => prepareNestedData(stop_hurdle))
        }));
    }
    return data;
}

watch(() => props.stop_hurdle, (newstop_hurdle) => {
    selected.value = mergedClasses.value.filter(cls => newstop_hurdle.included_class?.includes(cls.id));
}, { deep: true });

onMounted(() => {
    selected.value = mergedClasses.value.filter(cls => props.stop_hurdle.included_class?.includes(cls.id));
});
const paymentOptions = [
    { label: 'Preferred return', value: 'preffered_return' },
    { label: 'Interest', value: 'interest' },
    { label: 'Principal', value: 'principal' },
    { label: 'Other', value: 'other' }
];
</script>

<template>
    <div class="card class-form-card border" style="width: 90%; margin-left:8%;">
        <div class="card-body " >
            <div class="card-title class-header" @click="expanded = !expanded">
                <div class="row align-items-center">
                    <div class=""  >
                        <div class="d-flex align-items-center expandable-header">
                            <i class="me-2" :class="expanded
                                ? 'bi bi-caret-down-fill text-primary'
                                : 'bi bi-caret-right-fill text-primary'
                                "></i>
                            <span class="title-span" v-html="stop_hurdleTitle(stop_hurdle, wfh_index)">
                            </span>
                            <div class="ms-auto d-flex align-items-center class-info-block">
                                <span v-if="!readonly" class="btn remove-icon-btn" @click="deletestop_hurdle(wfh_index)">
                                    <i class="las la-trash danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="expanded" class="d-block">
                <div class="col-md-12 d-flex flex-column">
                    <label for="preferred_return_type" class="font-weight-bold text-dark bg-light p-2 rounded">Preferred return type
                        <span
                            class="text-danger">*</span></label>
                    <select class="form-control" id="preferred_return_type" v-model="stop_hurdle.preferred_return_type" :disabled="readonly">
                        <option value="" disabled selected>Select preferred return type Type</option>
                        <option value="cash_on_cash">Cash on cash</option>
                        <option value="irr">Internal Rate of Return</option>
                        <option value="roi">Return On Investment</option>
                        <!-- <option value="split">Split</option> -->
                        <option value="return_of_capital">
                            Return Of Capital
                        </option>
                        <option value="cumulative_return">
                            Cumulative Return
                        </option>
                    </select>
                </div>

                <div class="col-md-12 d-flex flex-column" v-if="
                    stop_hurdle.preferred_return_type == 'cash_on_cash' ||
                    stop_hurdle.preferred_return_type == 'irr' ||
                    stop_hurdle.preferred_return_type == 'roi' ||
                    stop_hurdle.preferred_return_type == 'return_of_capital' ||
                    stop_hurdle.preferred_return_type == 'cumulative_return' ||
                    stop_hurdle.preferred_return_type == 'interest'
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
                    stop_hurdle.preferred_return_type == 'cash_on_cash' ||
                    stop_hurdle.preferred_return_type == 'irr' ||
                    stop_hurdle.preferred_return_type == 'roi' ||
                    stop_hurdle.preferred_return_type == 'return_of_capital' ||
                    stop_hurdle.preferred_return_type == 'cumulative_return' ||
                    stop_hurdle.preferred_return_type == 'interest'
                ">
                    <div v-for="(classItem, c_index) in stop_hurdle.classes_values" :key="c_index">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-column">
                                <label for="class_value" class="font-weight-bold text-dark bg-light p-2 rounded">
                                    <span>{{
                                        getClassName(
                                            stop_hurdle.classes_values[c_index].id
                                        )
                                    }}</span>
                                    <span class="tag-text">{{
                                        stop_hurdle.preferred_return_type + " (%)"
                                        }}</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <PercentInput v-model="stop_hurdle.classes_values[c_index].value" :disabled="readonly" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-flex flex-column" v-if="stop_hurdle.preferred_return_type == 'cumulative_return'">
                    <div>
                        <label for="cumulated_return_reach"
                            class="font-weight-bold text-dark bg-light p-2 rounded">Until the following cumulative
                            return is
                            reached<span class="text-danger">*</span></label>
                        <PercentInput v-model="stop_hurdle.cumulated_return_reach" :disabled="readonly" />
                    </div>
                </div>

                <div v-if="
                    !(
                        stop_hurdle.preferred_return_type == 'split' &&
                        stop_hurdle.split == 'Yes'
                    )
                ">
                    <div>
                        <div class="d-flex align-items-center cursor-pointer" @click="showAdvance = !showAdvance">
                            <i :class="showAdvance ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'" class="me-1"></i>
                            <span class="advance-btn fw-bold">Advanced</span>
                        </div>
                        <div class="row pt-2" v-if="showAdvance">
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="
                                stop_hurdle.preferred_return_type == 'cash_on_cash' ||
                                stop_hurdle.preferred_return_type == 'irr' ||
                                stop_hurdle.preferred_return_type == 'interset'
                            ">
                                <div>
                                    <label for="day_count" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Day count convention<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="day_count" v-model="stop_hurdle.day_count"
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
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="stop_hurdle.preferred_return_type == 'interset'">
                                <div>
                                    <label for="compounding_frequency"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Compounding frequency<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="compounding_frequency"
                                        v-model="stop_hurdle.compounding_frequency" :disabled="readonly">
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
                            <div class="col-md-6 mb-3" v-if="
                                stop_hurdle.preferred_return_type == 'interset' ||
                                stop_hurdle.preferred_return_type == 'cash_on_cash'
                            ">
                                <div>
                                    <label for="start_date" class="form-label">Start date override</label>
                                    <input type="date" onclick="this.showPicker()" id="start_date" class="form-control"
                                        placeholder="Enter override start date" v-model="stop_hurdle.start_date"
                                        :disabled="readonly" />
                                </div>
                            </div>

                            <div class="col-md-6 mb-3" v-if="
                                stop_hurdle.preferred_return_type == 'interset' ||
                                stop_hurdle.preferred_return_type == 'cash_on_cash'
                            ">
                                <label for="end_date" class="form-label">End date</label>
                                <input type="date" onclick="this.showPicker()" id="end_date" class="form-control" placeholder="Enter an end date"
                                    v-model="stop_hurdle.end_date" :disabled="readonly" />
                            </div>
                            <div class="col-md-6 mb-3" v-if="stop_hurdle.preferred_return_type == 'interset'">
                                <div>
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="number" id="duration" class="form-control"
                                        placeholder="Enter duration in months" v-model="stop_hurdle.duration"
                                        :disabled="readonly" />
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="
                                stop_hurdle.preferred_return_type == 'interset' ||
                                stop_hurdle.preferred_return_type == 'irr' ||
                                stop_hurdle.preferred_return_type == 'cash_on_cash'
                            ">
                                <div>
                                    <label for="accrues_on" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Accrues on<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="accrues_on" v-model="stop_hurdle.accrues_on"
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
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="
                                stop_hurdle.preferred_return_type !== null &&
                                stop_hurdle.preferred_return_type !== ''
                            ">
                                <div>
                                    <label for="payment_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Classify payments as<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="payments_towards" v-model="stop_hurdle.payments_towards"
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
                              v-if="stop_hurdle.preferred_return_type !== ''"  >
                                <div>
                                    <label for="payment_type_towards"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Payments counted towards hurdle<span class="text-danger">*</span>
                                    </label>
                                    <multiselect v-model="stop_hurdle.payment_type_towards" :options="paymentOptions"
                                        :multiple="true" :disabled="readonly" placeholder="Select payment types"
                                        track-by="value" label="label" id="payment_type_towards" />
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="stop_hurdle.preferred_return_type == 'cash_on_cash'">
                                <div>
                                    <label for="split_unpayed" class="font-weight-bold text-dark bg-light p-2 rounded">
                                        Split unpaid accrual to investments
                                        by<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="split_unpayed" v-model="stop_hurdle.split_unpayed"
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
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="
                                stop_hurdle.preferred_return_type == 'cash_on_cash' ||
                                stop_hurdle.preferred_return_type == 'interset'
                            ">
                                <div>
                                    <label for="accrual_cadence"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Accrual cadence</label>
                                    <select class="form-control" id="accrual_cadence" v-model="stop_hurdle.accrual_cadence"
                                        :disabled="readonly">
                                        <option value="daily">
                                            Daily (most common)
                                        </option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-3" v-if="stop_hurdle.preferred_return_type !== 'split' && stop_hurdle.preferred_return_type !== ''">
                                <div>
                                    <label for="notes"
                                        class="font-weight-bold text-dark bg-light p-2 rounded">Notes</label>
                                    <textarea id="notes" class="form-control" placeholder="Enter any notes"
                                        v-model="stop_hurdle.notes" :disabled="readonly"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'stop_hurdleComponent',
}


</script>

<style scoped>

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
