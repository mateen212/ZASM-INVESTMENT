<script setup>
import { ref, onMounted, computed } from 'vue'
import HurdleComponent from './HurdleComponent.vue'
import Multiselect from "vue-multiselect"; // Import vue-multiselect
import { defineProps, defineEmits } from 'vue'
import PercentInput from './Core/PercentInput.vue'

const components = {
    Multiselect
};
const props = defineProps({
    classes: {
        type: Array,
        default: () => []
    },
    bucketClasses: {
        type: Array,
        default: () => []
    },
    // gp_provision: {
    //     type: Object,
    //     default: () => ({}),
    // },

    gp_provision: {
        type: Object,
        default: () => ({
            deal_class_id: "",
            classes_catch_up: [],
            catch_up_splits: [],
            classify_payment: "",
            notes: "",

        })
    },

});
const gpClasses = computed(() =>
    props.classes.filter((cls) => cls.class_type === "GP")
);

const mergedClasses = computed(() =>
    [...props.classes, ...props.bucketClasses].filter(cls => cls.class_type !== 'GP')
);


// Methods
const updateClasses = (value) => {
    selected.value = value;
    props.gp_provision.classes_catch_up = value.map((item) => item.id);
    props.gp_provision.catch_up_splits = value.map((item) => ({
        id: item.id,
        value: "",
    }));
};
// const emit = defineEmits();
const emit = defineEmits(['deleteGpProvision', 'update']);
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
            return 'Average Annual Return';
        case 'return_of_capital':
            return 'Return of Capital';
        default:
            return str;
    }
};

const selected = ref(null);


// Ref to track the expanded state
const expanded = ref(false);
const showAdvance = ref(false);
const getClassName = (id) => {
    let name = '';
    props.classes.forEach((cls) => {
        if (cls.id == id) {
            name = cls.equity_class_name;
        }
    });
    if (name == '') {
        props.bucketClasses.forEach((bucket) => {
            bucket.classes.forEach((cls) => {
                if (cls.id == id) {
                    name = cls.equity_class_name;
                }
            });
        });
    }
    return name;
}
const hurdleTitle = (hurdle, index) => {
    if (!selected.value || selected.value.length === 0) {
        return 'then ';
    }

    const gpClass = selected.value.find(item => item.class_type === 'GP');
    const otherClasses = selected.value.filter(item => item.class_type !== 'GP');

    let title = 'then Catch Up ';

    if (props.gp_provision.deal_class_id) {
        title += `<span class="text-primary"> ${getClassName(props.gp_provision.deal_class_id)} </span>`;
    }

    title += ' on ';

    otherClasses.forEach((cls, idx) => {
        const splitValue = props.gp_provision.catch_up_splits.find(split => split.id === cls.id)?.value1 || '0%';
        const remainingSplit = (100 - parseFloat(splitValue)) + '%';
        title += `<span class="text-primary"> ${cls.equity_class_name} </span> assuming a ${splitValue} / ${remainingSplit} split`;
        if (idx < otherClasses.length - 1) {
            title += ', ';
        }
    });

    return title;
}

const leftTo100 = (value) => {
    value = value?.replace('%', '');
    return 100 - Number(value) + '%';
}

const deleteGpProvision = () => {
    emit('deleteGpProvision');
}


onMounted(() => {
    selected.value = mergedClasses.value.filter(cls => props.gp_provision.classes_catch_up.includes(cls.id));
});

</script>
<template>
    <div class="card class-form-card border  ms-5">
        <div class="card-body">
            <div class="card-title " @click="expanded = !expanded">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center" style="
                                background-color: #e8f7ff;
                                padding: 15px;
                                border-radius: 8px;
                                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                                cursor:pointer;

                            ">
                            <i class="me-2" :class="expanded
                                ? 'bi bi-caret-down-fill text-primary'
                                : 'bi bi-caret-right-fill text-primary'
                                "></i>
                            <span class="header-title" v-html="hurdleTitle(hurdle)">
                            </span>
                            <div class="ms-auto d-flex align-items-center class-info-block">
                                <span v-if="!readonly" class="btn remove-icon-btn" @click="deleteGpProvision()">
                                    <i class="las la-trash"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="expanded" class="row mt-3">

                <div>
                    <label for="gp-class" class="font-weight-bold">GP Class</label>
                    <select v-model="props.gp_provision.deal_class_id" class="form-control">
                        <option v-for="classItem in gpClasses" :key="classItem.id" :value="classItem.id">
                            {{ classItem.equity_class_name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label for="classes-to-catch-up" class="font-weight-bold">
                        Classes to Catch Up
                    </label>


                    <multiselect :modelValue="selected" @update:modelValue="updateClasses" hideSelected="true"
                        :options="mergedClasses" :multiple="true" @select="console.log($event)" track-by="id"
                        label="equity_class_name" placeholder="Select Classes"></multiselect>
                </div>


                <div v-if="gp_provision.catch_up_splits.length > 0" class="mt-3">
                    <div v-for="(classItem, c_index) in gp_provision.catch_up_splits" :key="c_index"
                        class="d-flex align-items-center mb-3">

                        <PercentInput input-class="sm-percent-inline" v-model="gp_provision.catch_up_splits[c_index].value1"></PercentInput>
                        <!-- Class name (e.g., LP Class2) -->
                        <span class="me-2 text-primary me-2">
                            {{ getClassName(classItem.id) }}
                        </span>

                        <PercentInput input-class="sm-percent-inline" :value="leftTo100(gp_provision.catch_up_splits[c_index].value1)" :disabled="true"></PercentInput>

                        <span class="mx-2">to</span>

                        <!-- GP class name (always visible) -->
                        <div v-for="classItem in gpClasses" :key="classItem.id" :value="classItem.id">
                            {{ classItem.equity_class_name }}
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex align-items-center cursor-pointer" @click="showAdvance = !showAdvance">
                        <i :class="showAdvance ? 'bi bi-caret-down-fill text-primary' : 'bi bi-caret-right-fill text-primary'" class="me-1"></i>
                        <span class="advance-btn fw-bold">Advanced</span>
                    </div>
                    <div v-show="showAdvance" v-collapse>
                        <div  class="row pt-2">
                            <div>
                                <label for="classify_payment" class="font-weight-bold text-dark bg-light p-2 rounded">
                                    Classify payments as<span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="preferred_return"
                                    v-model="gp_provision.classify_payment">
                                    <option value="preferred_return">
                                        Preferred return
                                    </option>
                                    <option value="interest">
                                        Interest
                                    </option>
                                    <option value="roi">
                                        Return on capital
                                    </option>
                                    <option value="other">other(return of capital,upside etc,)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex flex-column mb-3">
                            <div>
                                <label for="notes" class="font-weight-bold text-dark bg-light p-2 rounded">Notes</label>
                                <textarea id="notes" class="form-control" placeholder="Enter any notes"
                                    v-model="gp_provision.notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</template>
<style scoped>
.header-title{
    color: #5b6e88;
    margin-bottom: 0;
    white-space: normal;
    width: auto;
    font-weight: bold;
    font-size: 14px;
}
</style>