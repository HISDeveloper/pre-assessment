<template>
  <button 
    @click="handleClick"
    :disabled="isDisabled"
    :class="{ 'active': isActive }"
    class="paginate-button"
  >
    {{ label }}
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: {
    type: [String, Number],
    required: true
  },
  position: {
    type: Number,
    required: true
  },
  currentPage: {
    type: Number,
    default: 1
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['page-change']);

const isActive = computed(() => props.position === props.currentPage);
const isDisabled = computed(() => props.disabled || isActive.value);

const handleClick = () => {
  if (!isDisabled.value) {
    emit('page-change', props.position);
  }
};
</script>

<style scoped>
.paginate-button {
  padding: 0.5rem 1rem;
  margin: 0 0.25rem;
  border: 1px solid #ddd;
  background-color: #fff;
  color: #333;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.paginate-button:hover:not(:disabled) {
  background-color: #f0f0f0;
}

.paginate-button.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.paginate-button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}
</style>