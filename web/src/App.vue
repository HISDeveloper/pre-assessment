<template>
  <div>
    <Navbar v-if="showNavbar" />

    <router-view />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'

const route = useRoute()

const showNavbar = computed(() => {
  return !['/login', '/register'].includes(route.path)
})
</script>
