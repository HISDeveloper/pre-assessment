<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { ref } from 'vue'
import apiClient from '../api.js';

const router = useRouter();
const token = localStorage.getItem('bearerToken');
if (!token) {
    router.push('/');
}

const storage_username = localStorage.getItem('username');
const username = ref(storage_username);
const navItems = [
    { name: 'Dashboard', path: '/home/dashboard', icon: 'fas fa-tachometer-alt' },
    { name: 'Products', path: '/home/products', icon: 'fas fa-box-open' },
    { name: 'Cart', path: '/home/carts', icon: 'fas fa-shopping-cart' },
    { name: 'Orders History', path: '/home/orders', icon: 'fas fa-shopping-cart' },
    // { name: 'Customers', path: '/customers', icon: 'fas fa-users' },
    // { name: 'Settings', path: '/settings', icon: 'fas fa-cog' }
]

async function logout() {
    console.log('Logging out...');
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.post('/logout');
        console.log('msg:', response.data);
        //destroy token
        localStorage.removeItem('bearerToken');
        localStorage.removeItem('username');
        localStorage.removeItem('email');
        localStorage.removeItem('id');
        router.push('/');

    } catch (error) {
        console.error('Could not logout:', error);
    }

}

</script>

<template>
    <div class="layout-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <h1 class="app-title">ShoppingX</h1>
                <div class="user-info">
                    <span class="username">Welcome, {{ username }}</span>
                    <i class="fas fa-user-circle user-icon"></i>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Side Navigation -->
            <nav class="side-nav">
                <ul class="nav-menu">
                    <li v-for="item in navItems" :key="item.name" class="nav-item">
                        <RouterLink :to="item.path" class="nav-link">
                            <i :class="item.icon"></i>
                            <span class="link-text">{{ item.name }}</span>
                        </RouterLink>
                    </li>
                </ul>

                <!-- Logout Button at Bottom -->
                <div class="logout-container">
                    <button @click="logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </nav>

            <!-- Content Container -->
            <div class="content-container">
                <RouterView />
                <!-- <router-view></router-view> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Base Styles */
.layout-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-width: 100vw;
}

/* Header Styles */
.app-header {
    background-color: #1976D2;
    /* Primary blue */
    color: white;
    padding: 0 20px;
    height: 60px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 10;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.app-title {
    font-size: 1.5rem;
    margin: 0;
    font-weight: 500;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.username {
    font-size: 0.9rem;
}

.user-icon {
    font-size: 1.5rem;
}

/* Main Content Area */
.main-content {
    width: 100vh;
    display: flex;
    flex: 1;
}

/* Side Navigation */
.side-nav {
    width: 250px;
    background-color: #1565C0;
    /* Darker blue */
    color: white;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    position: sticky;
    top: 60px;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    flex: 1;
    min-width: 15vw;
}

.nav-item {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.3s;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.router-link-exact-active {
    background-color: rgba(255, 255, 255, 0.2);
    border-left: 4px solid white;
}

.link-text {
    font-size: 0.9rem;
}

/* Logout Button */
.logout-container {
    padding: 15px 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    background-color: transparent;
    color: white;
    border: none;
    width: 100%;
    text-align: left;
    padding: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.logout-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Content Container */
.content-container {
    flex: 1;
    padding: 20px;
    background-color: #E3F2FD;
    /* Light bluish background */
    min-height: calc(100vh - 60px);
    width: 100vw;
    min-width: calc(100vw - 18vw);
}
</style>