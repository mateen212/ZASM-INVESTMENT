<template>
  <div v-if="loading" class="custom-loader-overlay">
    <div class="custom-loader"></div>
  </div>
  <div class="card shadow-sm border-0 p-4 text-center mt-5 mx-auto" style="max-width: 100%;">
    <h4 class="mb-4">Link Bank Account</h4>

    <div class="mb-3 text-start">
      <label class="form-label text-center">Account Holder Name</label>
      <input type="text" v-model="achForm.name" class="form-control" />
    </div>

    <div class="mb-3 text-start">
      <label class="form-label text-center">Routing Number</label>
      <input type="text" v-model="achForm.routing_number" class="form-control" />
    </div>

    <div class="mb-3 text-start">
      <label class="form-label text-center">Account Number</label>
      <input type="text" v-model="achForm.account_number" class="form-control" />
    </div>

    <div class="mb-4 text-start">
      <label class="form-label text-center">Account Type</label>
      <select v-model="achForm.account_type" class="form-select">
        <option value="checking">Checking</option>
        <option value="savings">Savings</option>
      </select>
    </div>

    <button class="btn btn-primary w-100" @click="submitACH">Link Bank Account</button>
  </div>

</template>
<script setup>
import {
  ref,
  computed,
  onMounted,
  watch,h,
  render
} from 'vue';
import Toast from '../Toast.vue';
const loading = ref(false);

const achForm = ref({

  name: '',
  routing_number: '',
  account_number: '',
  account_type: 'checking', // Default to 'checking' or leave empty
});
const submitACH = async () => {
  debugger;
  loading.value = true;
  const url = window.urls.ach;
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        achForm: achForm.value
      }),
    });

    loading.value = false;
    const responseData = await response.json(); // 👈 parse response body
    if (responseData.status === 'pending') {
      showSuccessToast("Micro-deposits sent. Please check your bank account in 1-2 days.");
      window.location.href = responseData.data.onboarding_url;
    } else {
      console.log("ACH response:", responseData);
    }
  } catch (error) {
    showErrorToast("Error:", error);
  }
};
const showToast = (message, color = '#28a745') => {
  const container = document.createElement('div');
  document.body.appendChild(container);

  const vnode = h(Toast, {
    message,
    bgColor: color,
    onClose: () => {
      render(null, container);
      document.body.removeChild(container);
    },
  });

  render(vnode, container);
};

const showSuccessToast = (msg) => showToast(msg, '#28a745');
const showErrorToast = (msg) => showToast(msg, '#dc3545');

</script>