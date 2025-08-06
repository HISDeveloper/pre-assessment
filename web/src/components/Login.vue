<template>
  <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
      <h3 class="mb-4 text-center">Sign In</h3>

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input v-model="email" type="email" id="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input v-model="password" type="password" id="password" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign In</button>
      </form>

      <div class="mt-3 text-center">
        <small>
          Don't have an account? 
          <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
        </small>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <RegisterModal />
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import pop from '@/utils/pop'
import RegisterModal from '@/components/RegisterModal.vue'
import api from '@/utils/axios'

const email = ref('')
const password = ref('')
const router = useRouter()

const handleLogin = async () => {
  pop.loading()
  try {
    const res = await api.post('/api/login', {
      email: email.value,
      password: password.value
    })

    if (res.data.status === true) {
      localStorage.setItem('token', res.data.data.token)
      pop.success('Login successful')
      router.push('/dashboard')
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Login failed")
  } finally {
    pop.close()
  }
}
</script>
