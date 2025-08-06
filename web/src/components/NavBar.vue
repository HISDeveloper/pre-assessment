<template>
  <div>
    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <router-link to="/dashboard" class="navbar-brand">HARGREAVES</router-link>

        <button class="btn btn-outline-light" @click="toggleNav">
          <i :class="navOpen ? 'bi bi-x' : 'bi bi-list'"></i>
        </button>
      </div>
    </nav>

    <div style="padding-top: 56px;"></div>

    <!-- Sidebar -->
    <div :class="['sidebar', navOpen ? 'sidebar-open' : '']">
      <ul class="nav flex-column p-3">
        <li class="nav-item mb-3">
          <router-link to="/dashboard" class="nav-link text-white">
            <i class="bi bi-speedometer2"></i> Dashboard
          </router-link>
        </li>
        <li class="nav-item mb-3">
          <router-link to="/cart" class="nav-link text-white">
            <i class="bi bi-cart"></i> Cart
          </router-link>
        </li>
        <li class="nav-item mb-3">
          <router-link to="/orders" class="nav-link text-white">
            <i class="bi bi-clock-history"></i> Order History
          </router-link>
        </li>
        <li class="nav-item">
          <button @click="logout" class="btn btn-danger w-100">Logout</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import pop from '@/utils/pop'
import api from '@/utils/axios'

const navOpen = ref(false)
const router = useRouter()

const toggleNav = () => {
  navOpen.value = !navOpen.value
}

const logout = async () => {
  pop.loading()
  try {
    const token = localStorage.getItem('token')
    const res = await api.post('/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    
    if (res.data.status === true) {
      localStorage.removeItem('token')
      pop.success('Logged out')
      router.push('/login')
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Logout failed")
  } finally {
    pop.close()
  }
}

onMounted(() => {
  router.afterEach(closeSidebar)
})

onBeforeUnmount(() => {
  router.afterEach(() => {})
})

const closeSidebar = () => {
  navOpen.value = false
}

</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 56px;
  right: -220px;
  width: 220px;
  height: calc(100vh - 56px);
  background-color: #212529;
  color: white;
  transition: right 0.3s ease-in-out;
  z-index: 1040;
  overflow-y: auto;
}

.sidebar-open {
  right: 0;
}

.nav-link {
  color: white;
}
.nav-link:hover {
  color: #ccc;
}
</style>
