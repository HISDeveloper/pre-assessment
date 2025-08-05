<template>
  <div class="pagination-container">
    <button
      class="pagination-button"
      :disabled="currentPage === 1"
      @click="changePage(currentPage - 1)"
    >
      Previous
    </button>

    <template v-for="page in pages" :key="page">
      <button
        v-if="page === '...'"
        class="pagination-ellipsis"
        disabled
      >
        {{ page }}
      </button>
      <button
        v-else
        class="pagination-button"
        :class="{ active: page === currentPage }"
        @click="changePage(page)"
      >
        {{ page }}
      </button>
    </template>

    <button
      class="pagination-button"
      :disabled="currentPage === totalPages"
      @click="changePage(currentPage + 1)"
    >
      Next
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  maxVisibleButtons: {
    type: Number,
    default: 5
  }
});

const emit = defineEmits(['page-change']);

const changePage = (page) => {
  if (page >= 1 && page <= props.totalPages) {
    emit('page-change', page);
  }
};

const pages = computed(() => {
  const range = [];
  const half = Math.floor(props.maxVisibleButtons / 2);
  let start = Math.max(props.currentPage - half, 1);
  let end = Math.min(start + props.maxVisibleButtons - 1, props.totalPages);

  // Adjust if we're at the end
  if (end === props.totalPages) {
    start = Math.max(props.totalPages - props.maxVisibleButtons + 1, 1);
  }

  // Always show first page
  if (start > 1) {
    range.push(1);
    if (start > 2) {
      range.push('...');
    }
  }

  // Add middle range
  for (let i = start; i <= end; i++) {
    range.push(i);
  }

  // Always show last page
  if (end < props.totalPages) {
    if (end < props.totalPages - 1) {
      range.push('...');
    }
    range.push(props.totalPages);
  }

  return range;
});
</script>

<style scoped>
.pagination-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin: 1rem 0;
}

.pagination-button {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  background-color: white;
  color: #4a5568;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
  min-width: 2.5rem;
}

.pagination-button:hover:not(:disabled) {
  background-color: #f7fafc;
  border-color: #cbd5e0;
}

.pagination-button.active {
  background-color: #4299e1;
  color: white;
  border-color: #4299e1;
}

.pagination-button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.pagination-ellipsis {
  padding: 0.5rem 1rem;
  border: none;
  background-color: transparent;
  cursor: default;
}
</style>