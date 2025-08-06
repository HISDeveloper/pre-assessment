<template>
  <div v-if="showPage" class="container mt-5">
    <h3 class="text-center">Cart</h3>

    <table class="table">
      <thead>
        <tr>
          <th>Product</th>
          <th style="width: 180px;">Qty</th>
          <th>Price</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        <tr v-if="!loading && cart.length === 0">
          <td colspan="4" class="text-center">Cart is empty</td>
        </tr>

        <tr v-else v-for="item in cart" :key="item.id">
          <td>{{ item.product.name }}</td>
          <td>
            <div class="input-group input-group-sm">
                <button class="btn btn-outline-secondary" @click="updateQuantity(item, item.quantity - 1)" :disabled="item.quantity <= 1">-</button>
                <span class="px-2">{{ item.quantity }}</span>
                <button class="btn btn-outline-secondary" @click="updateQuantity(item, item.quantity + 1)">+</button>
            </div>
          </td>
          <td>RM {{ (item.product?.price * item.quantity).toFixed(2) }}</td>
          <td>
            <button class="btn btn-sm btn-danger" @click="removeItem(item.id)">Remove</button>
          </td>
        </tr>

        <tr v-if="cart.length">
          <td colspan="2" class="text-end fw-bold">Total</td>
          <td colspan="2" class="fw-bold">RM {{ totalPrice.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>

    <button v-if="!loading && cart.length" class="btn btn-success" @click="placeOrder">Place Order</button>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import pop from '@/utils/pop'
import api from '@/utils/axios'

const showPage = ref(false)
const router = useRouter()
const cart = ref([])
const loading = ref(true)

const fetchCart = async () => {
  try {
    pop.loading()
    const token = localStorage.getItem('token')
    const res = await api.get('/api/cart/get-user-cart', {
      headers: { Authorization: `Bearer ${token}` }
    })

    if (res.data.status === true) {
      cart.value = res.data.data.items
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to load cart")
  } finally {
    loading.value = false
    pop.close()
  }
}

const updateQuantity = async (item, newQty) => {
  try {
    const token = localStorage.getItem('token');
    const res = await api.post(`/api/cart/update-quantity/${item.id}`, {
      quantity: newQty
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (res.data.status === true) {
      item.quantity = res.data.data.item.quantity;
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to update quantity")
  }
};

const removeItem = async (id) => {
  try {
    const token = localStorage.getItem('token')
    const res = await api.delete(`/api/cart/remove-item/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })

    if (res.data.status === true) {
      cart.value = cart.value.filter(i => i.id !== id)
      pop.success("Item removed")
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to remove item")
  }
}

const placeOrder = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await api.post('/api/order/place-order', {}, {
      headers: { Authorization: `Bearer ${token}` }
    })

    if (res.data.status === true) {
      cart.value = []
      pop.success("Order placed!")
      router.push('/orders')
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to place order")
  }
}

// Compute total cart price
const totalPrice = computed(() => {
  return cart.value.reduce((sum, item) => {
    const price = parseFloat(item.product?.price || 0)
    return sum + price * item.quantity
  }, 0)
})

onMounted(() => {
  const token = localStorage.getItem('token')
  if (!token) {
    // Redirect to login if token missing
    router.push('/login')
    return
  }

  showPage.value = true

  fetchCart()
})

</script>
