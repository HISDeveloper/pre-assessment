<script setup>
import { RouterLink, RouterView } from 'vue-router'
import {ref} from 'vue'

const storage_username = localStorage.getItem('username');
const username = ref(storage_username);

</script>

<template>
    <div class="dashboard-layout">
        <!-- Red Header -->
        <header class="dashboard-header">
            <!-- Title on top left -->
            <div class="header-title">
                <h1>Dashboard</h1>
            </div>

            <!-- Nav bar in middle -->
            <nav class="header-nav">
                <RouterLink to="/home/dashboard">Home</RouterLink>
            </nav>

            <!-- Optional user section on right -->
            <div class="header-user">
                <span>Welcome, {{ username }}</span>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="dashboard-content">
            <RouterView />
        </main>
    </div>
</template>

<style scoped>
.dashboard-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.dashboard-header {
  background-color: #273fb4; /* Red color */
  color: white;
  padding: 0 20px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  width: 100vh;
}

.header-title h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.header-nav {
  display: flex;
  gap: 20px;
}

.header-nav a {
  color: white;
  text-decoration: none;
  padding: 5px 10px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.header-nav a:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.header-nav a.router-link-exact-active {
  background-color: rgba(255, 255, 255, 0.3);
  font-weight: bold;
}

.header-user {
  display: flex;
  align-items: center;
  gap: 10px;
}

.dashboard-content {
  flex: 1;
  max-width: 100vh;
  padding: 20px;
  background-color: #f5f5f5;
}
</style>
