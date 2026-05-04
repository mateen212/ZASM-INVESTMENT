<script setup>
import { ref, onMounted, computed } from 'vue'
import HurdleComponent from './HurdleComponent.vue'
import Gp_Provision from './Gp_Provision.vue'
import Loading from './Loading.vue'
import StopConditionTemplate from './StopConditionTemplate.vue'
const props = defineProps({
    waterfalls: {
        type: Array,
        default: () => []
    },
    waterfall: {
        type: Object,
        default: () => ({
            waterfall_name: '',
            hurdles: []
        })
    },
    classes: {
        type: Array,
        default: () => []
    },
    buckets: {
        type: Array,
        default: () => []
    }
});

const selectedWaterfall = ref(props.waterfall);
const loading = ref(false);
const errors = ref(null);
const successMessage = ref('');
const changeWaterfall = (id) => {
    if (id === 'add_waterfall') {
        $('#addWaterfallModal').modal('show');
        waterfall.id = ''; 
        return;
    }
    const selected = props.waterfalls.find(waterfall => waterfall.id === id);
    selectedWaterfall.value = selected;
};



const newWaterfallHurdle = () => {
    selectedWaterfall.value.hurdles.push({
        hurdle_type: "",
        split: "No",
        splits: [],
        included_class: [],
        classes_values: [],
        preferred_return_type: "",
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

    });
}

const deleteHurdle = (index) => {
    selectedWaterfall.value.hurdles.splice(index, 1);
};

const canAddHurdle = computed(() => {
    return selectedWaterfall.value.hurdles.length === 0 || !(selectedWaterfall.value.hurdles[selectedWaterfall.value.hurdles.length - 1].hurdle_type === 'split' && selectedWaterfall.value.hurdles[selectedWaterfall.value.hurdles.length - 1].split === 'Yes') && !selectedWaterfall.value.is_basic;
});

const submitWaterfallForm = async () => {
    loading.value = true;
    let url = window.urls.waterfallSave;
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                waterfall: selectedWaterfall.value,
            })
        });

        loading.value = true;
        if (response.status === 422) {
            const responseData = await response.json();
            // update errors in alpine data
            errors.value = responseData.errors;
            return;
        }

        const responseData = await response.json();
        if (response.status === 200) {
            loading.value = false;
            // alert(responseData.message);
            console.log(responseData);
            successMessage.value = 'Updated Successfully';
            // $('.alert-success').alert();
            cosyAlert('<strong>Success</strong><br />Updated data succefully!', 'success');
            // window.location.reload();
        } else {
            loading.value = false;
            // alert(responseData.message);
            console.log(responseData);
        }

    } catch (error) {
        console.error('Error:', error);
        loading.value = false;
    }
}
</script>

<template>
    <div class="card p-3 shadow-sm " style="background-color: #F9F9F9;">
        <loading v-if="loading" />
        <div class="col-md-6 d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 me-3" style="font-size:15px;">Distribution waterfall to edit</h5>
            <select class="form-select custom-select" id="distribution-select" style="width: auto;"
                v-model="waterfall.id" @change="changeWaterfall(waterfall.id)">
                <option v-for="(waterfall, w_index) in waterfalls" :key="w_index" :value="waterfall.id"
                    v-text="waterfall.waterfall_name"></option>
                <option value="add_waterfall">Add distribution waterfall</option>
            </select>
        </div>
        <div class="col-md-12">
            <h3 id="waterfall_name" class="heading-class" v-text="selectedWaterfall.waterfall_name"></h3>
        </div>

        <div>
            <p>Add hurdles to the waterfall.<a href="w">Learn more.</a></p>
        </div>

        <template v-for="(hurdle, index) in selectedWaterfall.hurdles" :key="index">
            <HurdleComponent :wfh_index="index" :hurdle="hurdle" :classes="classes" :buckets="buckets"
                :readonly="(selectedWaterfall.is_basic == 1)" @deleteHurdle="deleteHurdle" />
        </template>
        <!-- <Gp_Provision  /> -->
        <!-- Dropzone Section -->
        <div v-if="canAddHurdle" class="dropzone mt-4 p-4 bg-light text-center" role="button"
            @click="newWaterfallHurdle()">
            <p class="mb-0">Add hurdle</p>
        </div>

        <button v-if="!selectedWaterfall.is_basic" type="submit" class="btn btn-primary mt-3"
            @click="submitWaterfallForm">Save</button>
    </div>
</template>