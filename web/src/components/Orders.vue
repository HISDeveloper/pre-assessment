<template>
  <div v-if="showPage" class="container mt-5">
    <h3 class="text-center">Order History</h3>

    <div v-if="loading" class="text-center"></div>

    <div v-else-if="orders.length === 0" class="text-center my-5 text-muted">
      No orders found.
    </div>

    <div v-else v-for="order in orders" :key="order.id" class="card my-3">
      <div class="card-body">
        <h5>Order #{{ order.id }} - RM {{ order.total_price ? Number(order.total_price).toFixed(2) : '0.00' }}</h5>
        <ul>
          <li v-for="item in order.order_items" :key="item.id">
            {{ item.product.name }} x {{ item.quantity }} (RM {{ item.price ? Number(item.price).toFixed(2) : '0.00' }})
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import pop from '@/utils/pop'
import api from '@/utils/axios'

const showPage = ref(false)
const router = useRouter()
const orders = ref([])
const loading = ref(true)

const loadOrders = async () => {
  try {
    pop.loading()
    const token = localStorage.getItem('token')
    const res = await api.get('/api/order/get-order-history', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (res.data.status === true) {
      orders.value = res.data.data.orders
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Could not load orders")
  } finally {
    loading.value = false
    pop.close()
  }
}

onMounted(() => {
  const token = localStorage.getItem('token')
  if (!token) {
    // Redirect to login if token missing
    router.push('/login')
    return
  }

  showPage.value = true

  loadOrders()
})
</script>
