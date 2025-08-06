<template>
  <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center">Register</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="register">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input v-model="name" type="text" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input v-model="email" type="email" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input v-model="password" type="password" class="form-control" required />
            </div>

            <ul class="list-unstyled small mb-3 ms-1">
              <li :class="{'text-success': hasUppercase, 'text-muted': !hasUppercase}">
                <span v-if="hasUppercase">✔</span>
                <span v-else>✖</span>
                At least 1 uppercase letter
              </li>
              <li :class="{'text-success': hasLowercase, 'text-muted': !hasLowercase}">
                <span v-if="hasLowercase">✔</span>
                <span v-else>✖</span>
                At least 1 lowercase letter
              </li>
              <li :class="{'text-success': hasNumber, 'text-muted': !hasNumber}">
                <span v-if="hasNumber">✔</span>
                <span v-else>✖</span>
                At least 1 number
              </li>
              <li :class="{'text-success': hasSpecialChar, 'text-muted': !hasSpecialChar}">
                <span v-if="hasSpecialChar">✔</span>
                <span v-else>✖</span>
                At least 1 special character
              </li>
              <li :class="{'text-success': hasMinLength, 'text-muted': !hasMinLength}">
                <span v-if="hasMinLength">✔</span>
                <span v-else>✖</span>
                Minimum 6 characters
              </li>
            </ul>

            <!-- <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input v-model="confirmPassword" type="password" class="form-control" required />
            </div> -->

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input v-model="confirmPassword" type="password" class="form-control" required />
              <div v-if="confirmPassword && passwordMismatch" class="text-danger small mt-1">
                Passwords do not match
              </div>
            </div>           

            <button type="submit" class="btn btn-success w-100">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import pop from '@/utils/pop'
import api from '@/utils/axios'
import { Modal } from 'bootstrap'

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')

const hasUppercase = computed(() => /[A-Z]/.test(password.value))
const hasLowercase = computed(() => /[a-z]/.test(password.value))
const hasNumber = computed(() => /\d/.test(password.value))
const hasSpecialChar = computed(() => /[@$!%*#?&^()\-_=+{};:,<.>]/.test(password.value))
const hasMinLength = computed(() => password.value.length >= 6)

const passwordMismatch = computed(() => confirmPassword.value && password.value !== confirmPassword.value)

const register = async () => {
  if (password.value !== confirmPassword.value) {
    return pop.error('Passwords do not match')
  }

  const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-_=+{};:,<.>]).+$/;

  if (!strongPassword.test(password.value)) {
    return pop.error('Password must include uppercase, lowercase, number, and special character.')
  }

  try {
    pop.loading()
    const res = await api.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    })

    if (res.data.status === true) {
      pop.success(res.data.message || 'Registration successful')
      registerModalInstance?.hide()

      const backdrop = document.querySelector('.modal-backdrop')
      if (backdrop) {
        backdrop.remove()
      }

      document.body.classList.remove('modal-open')
      document.body.style = ''
    } else {
      console.log(res.data)
      pop.error(res.data.message || 'Registration failed')
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Registration failed")
  } finally {
    pop.close()
  }
}

const resetForm = () => {
    name.value = ''
    email.value = ''
    password.value = ''
    confirmPassword.value = ''
}

let registerModalInstance = null

onMounted(() => {
  const modalEl = document.getElementById('registerModal')
  if (modalEl) {
    registerModalInstance = new Modal(modalEl)
    modalEl.addEventListener('hidden.bs.modal', resetForm)
  }
})

onBeforeUnmount(() => {
  const modalEl = document.getElementById('registerModal')
  if (modalEl) {
    modalEl.removeEventListener('hidden.bs.modal', resetForm)
  }
})

</script>
