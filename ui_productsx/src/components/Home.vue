<template>
  <div class="container">
    <!-- Left Column (2/3 width) -->
    <div class="left-column">
      <slot name="left"></slot>
    </div>
    
    <!-- Right Column (1/3 width) -->
    <div class="right-column">
      <slot name="right"></slot>
    </div>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  width: 100%;
  gap: 16px; /* Adjust gap between columns as needed */
}

.left-column {
  flex: 2; /* Takes 2 parts of the available space */
  min-width: 0; /* Prevents overflow issues */
}

.right-column {
  flex: 1; /* Takes 1 part of the available space */
  min-width: 0; /* Prevents overflow issues */
}

/* Responsive behavior - stack columns on small screens */
@media (max-width: 768px) {
  .container {
    flex-direction: column;
  }
  
  .left-column,
  .right-column {
    width: 100%;
  }
}
</style>