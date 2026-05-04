<!-- components/Toast.vue -->
<template>
    <div v-if="visible" class="toast-wrapper" :style="{ backgroundColor: bgColor }">
        <span>{{ message }}</span>
        <span class="close-btn" @click="hide">&times;</span>
        <div class="progress-bar" ref="progressBar"></div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: String,
    duration: {
        type: Number,
        default: 3000,
    },
    bgColor: {
        type: String,
        default: '#28a745',
    },
});

const emit = defineEmits(['close']);
const visible = ref(true);
const progressBar = ref(null);

const hide = () => {
    visible.value = false;
    emit('close');
};

watch(visible, (val) => {
    if (val && progressBar.value) {
        progressBar.value.style.transition = `width ${props.duration}ms linear`;
        progressBar.value.style.width = '0%';
    }
});

setTimeout(hide, props.duration);
</script>

<style scoped>
.toast-wrapper {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    gap: 10px;
    align-items: center;
    min-width: 300px;
    max-width: 90%;
    z-index: 10000;
    pointer-events: auto;
    overflow: hidden;
}

.close-btn {
    cursor: pointer;
    margin-left: auto;
    font-weight: bold;
    font-size: 18px;
}

.progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: rgba(255, 255, 255, 0.7);
    width: 100%;
}
</style>
