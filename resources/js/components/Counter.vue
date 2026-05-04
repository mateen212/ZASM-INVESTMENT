<script setup>
    import { ref } from 'vue'

    const count = ref(0)

    const decrement = () => {
        count.value--
    }
</script>

<template>
    {{ count }}

    <button @click="count++">
        Add
    </button>
    <button @click="decrement">
        Subtract
    </button>
</template>