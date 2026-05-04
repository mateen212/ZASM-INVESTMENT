<template>
  <input type="text" :class="['form-control', inputClass]" :value="displayValue" @input="handleInput" @blur="formatValue" ref="input"
    :disabled="disabled" />
</template>

<script>
export default {
  name: "PercentageInput",
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    inputClass: {
      type: String,
      default: "",
    },
  },
  emits: ["update:modelValue"],
  computed: {
    displayValue() {
      return this.modelValue?.replace("%", "") + "%"; // Ensure % is always there
    },
  },
  methods: {
    handleInput(event) {
      let value = event.target.value.replace("%", ""); // Remove % temporarily
      if (!isNaN(value) && value !== "") {
        value = `${value}%`;
        this.$emit("update:modelValue", value);
      }
      this.moveCursorBeforePercent();
    },
    formatValue() {
      let value = this.modelValue?.replace("%", "") || "0"; // Prevent empty values
      this.$emit("update:modelValue", `${value}%`); // ✅ Only emit, don't modify prop
      this.moveCursorBeforePercent();
    },
    moveCursorBeforePercent() {
      this.$nextTick(() => {
        const input = this.$refs.input;
        if (input) {
          const cursorPosition = input.value.length - 1;
          input.setSelectionRange(cursorPosition, cursorPosition);
        }
      });
    },
  },
  mounted() {
    this.moveCursorBeforePercent();
  },
};
</script>

<style scoped>
input {
  padding: 5px;
  font-size: 16px;
}

input:disabled {
  background-color: #f5f3f3;
}
</style>
