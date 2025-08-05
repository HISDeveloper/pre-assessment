<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { ref } from 'vue'
import axios from 'axios'
import NotificationModal from './NotificationModal.vue';

const router = useRouter();

const email = ref("");
const password = ref("");

const statusMessage1 = ref("");
const statusMessage2 = ref("");
const isLoading = ref(false);
const successMsg = ref("login successful");
const showNotification = ref(false);

// retain to dashboard if token not destroy
const token = localStorage.getItem('bearerToken');
if (token) {
  router.push('/home/dashboard');
}

async function submitData() {
  if (!email.value) {
    statusMessage1.value = 'Please enter an email.';
    return;
  } else {
    statusMessage1.value = '';
  }
  if (!password.value) {
    statusMessage2.value = 'Please enter password.';
    return;
  } else {
    statusMessage2.value = '';
  }

  try {
    isLoading.value = true;

    // await axios.get('http://127.0.0.1:8000/sanctum/csrf-cookie', { withCredentials: true });


    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value,
    });
    console.log('Success:', response.data);
    const token = response.data.token;
    const user = response.data.user;
    if (token) {
      localStorage.setItem('bearerToken', token);
      localStorage.setItem('username', user.name);
      localStorage.setItem('email', user.email);
      localStorage.setItem('id', user.id);
      console.log('data kept');

      showNotification.value = true
      // redirect
      setTimeout(() => {
        router.push('/home/dashboard');
      }, 1500)

    }

  } catch (error) {
    // Handle error
    console.error('Error submitting data:', error);
    // statusMessage.value = 'Failed to submit data. Please try again.';
  } finally {
    isLoading.value = false;
  }
}


</script>

<template>

  <body>
    <div class="form-container">
      <NotificationModal :show="showNotification" :message="successMsg" />
      <h2>Login</h2>

      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" v-model="email" class="form-input" placeholder="email" />
        <span v-if="statusMessage1">{{ statusMessage1 }}</span>
      </div>
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" v-model="password" class="form-input" placeholder="password" />
        <span v-if="statusMessage2">{{ statusMessage2 }}</span>
      </div>

      <button @click="submitData" class="form-button">
        Login
      </button>
      <span v-if="isLoading">
        <span class="spinner"></span> Processing...
      </span>
      <!-- <span v-else></span> -->
      <hr>
      Not register?
      <RouterLink to="/register" class="">
        Register
      </RouterLink>
    </div>
  </body>
</template>

<style>
/* Global Styles */
:root {
  --bg-color: #f3f4f6;
  --surface-color: #ffffff;
  --border-color: #d1d5db;
  --primary-color: #4f46e5;
  --primary-hover-color: #4338ca;
  --primary-focus-ring: #6366f1;
  --text-color-primary: #1f2937;
  --text-color-secondary: #6b7280;
  --text-color-label: #374151;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

body {
  font-family: 'Inter', sans-serif;
  background-color: #a6c4ff;
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 0rem;
  margin: 0;
}

/* Form Container */
.form-container {
  width: 20rem;
  max-width: 28rem;
  /* Equivalent to max-w-md */
  background-color: var(--surface-color);
  border-radius: 0.75rem;
  /* Equivalent to rounded-xl */
  box-shadow: var(--shadow-2xl);
  padding: 2rem;
  /* Equivalent to p-8 */
}

.form-container h2 {
  font-size: 1.875rem;
  /* Equivalent to text-3xl */
  font-weight: 700;
  /* Equivalent to font-bold */
  color: var(--text-color-primary);
  text-align: center;
  margin-bottom: 0.5rem;
}

.form-container p {
  color: var(--text-color-secondary);
  text-align: center;
  margin-bottom: 1.5rem;
}

/* Form Groups and Inputs */
.form-group {
  margin-bottom: 1rem;
  /* Equivalent to space-y-4 */
}

.form-label {
  display: block;
  font-size: 0.875rem;
  /* Equivalent to text-sm */
  font-weight: 500;
  /* Equivalent to font-medium */
  color: var(--text-color-label);
  margin-bottom: 0.25rem;
}

.form-input {
  display: block;
  width: 100%;
  padding: 0.75rem;
  /* Equivalent to p-3 */
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  /* Equivalent to rounded-lg */
  box-shadow: var(--shadow-sm);
  transition: all 0.15s ease-in-out;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-focus-ring);
  box-shadow: 0 0 0 2px var(--surface-color), 0 0 0 4px var(--primary-focus-ring);
}

.checkbox-group {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-input {
  height: 1rem;
  width: 1rem;
  color: var(--primary-color);
  border-color: var(--border-color);
  border-radius: 0.25rem;
  box-shadow: var(--shadow-sm);
}

.checkbox-label {
  margin-left: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-color-primary);
}

.checkbox-label a {
  color: var(--primary-color);
  font-weight: 500;
  text-decoration: none;
}

.checkbox-label a:hover {
  color: var(--primary-hover-color);
}

/* Button Styles */
.form-button {
  width: 100%;
  padding: 0.75rem 1rem;
  /* Equivalent to py-3 px-4 */
  border-radius: 0.5rem;
  /* Equivalent to rounded-lg */
  background-color: var(--primary-color);
  color: white;
  font-weight: 700;
  font-size: 1.125rem;
  /* Equivalent to text-lg */
  box-shadow: var(--shadow-md);
  transition: all 0.15s ease-in-out;
  cursor: pointer;
  border: none;
}

.form-button:hover {
  background-color: var(--primary-hover-color);
}

.form-button:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--surface-color), 0 0 0 4px var(--primary-focus-ring);
}

.form-button:hover:not(:disabled) {
  background-color: #45a049;
}

.form-button:disabled {
  background-color: #81c784;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
  margin-right: 8px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
