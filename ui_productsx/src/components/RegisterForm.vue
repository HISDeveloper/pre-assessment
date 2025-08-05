<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { ref } from 'vue'
import axios from 'axios'


const router = useRouter();

// retain to dashboard if token not destroy
const token = localStorage.getItem('bearerToken');
if (token) {
  router.push('/home/dashboard');
}

const username = ref("");
const email = ref("");
const password = ref("");
const confirm_password = ref("");
// const message = "";

const statusMessage1 = ref("");
const statusMessage2 = ref("");
const statusMessage3 = ref("");
const statusMessage4 = ref("");

async function submitData() {
  if (!username.value) {
    statusMessage1.value = 'Please enter a username.';
    return;
  } else {
    statusMessage1.value = '';
  }
  if (!email.value) {
    statusMessage2.value = 'Please enter an email.';
    return;
  } else {
    statusMessage2.value = '';
  }
  if (!password.value) {
    statusMessage3.value = 'Please enter password.';
    return;
  } else {
    statusMessage3.value = '';
  }
  if (!confirm_password.value) {
    statusMessage4.value = 'Please enter confirm-password.';
    return;
  } else {
    statusMessage4.value = '';
  }
  if (confirm_password.value != password.value) {
    statusMessage4.value = 'confirm password not match!';
    return;
  } else {
    statusMessage4.value = '';
  }

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/register', {
      name: username.value,
      email: email.value,
      password: password.value,
    });
    console.log('Success:', response.data);
    statusMessage4.value = 'Account created. Please login';

    setTimeout(() => {
      router.push('/');
    }, 4000)


  } catch (error) {
    // Handle error
    console.error('Error submitting data:', error);
    statusMessage4.value = 'Failed to submit data. Please try again.';
  }
}


</script>

<template>

  <body>
    <div class="form-container">
      <h2>Create an Account</h2>

      <div class="form-group">
        <label for="username" class="form-label">Username</label>
        <input type="text" v-model="username" class="form-input" placeholder="Username" />
        <span v-if="statusMessage1">{{ statusMessage1 }}</span>
      </div>
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" v-model="email" class="form-input" placeholder="email" />
        <span v-if="statusMessage2">{{ statusMessage2 }}</span>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" v-model="password" class="form-input" placeholder="password" />
        <span v-if="statusMessage3">{{ statusMessage3 }}</span>
      </div>

      <div class="form-group">
        <label for="confirm-password" class="form-label">Confirm Password</label>
        <input type="password" v-model="confirm_password" class="form-input" placeholder="confirm-password" />
        <span v-if="statusMessage4">{{ statusMessage4 }}</span>
      </div>

      <button @click="submitData" class="form-button">
        Register
      </button>
      <hr>
      Already register?
      <RouterLink to="/" class="">
        Login
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
</style>
