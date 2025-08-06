<template>
  <div v-if="showPage" class="container mt-5 dashboard-container">
    <h3 class="text-center mb-4">Dashboard</h3>

    <!-- Search and Filter -->
    <div class="row mb-3">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text">
        <i class="bi bi-search"></i>
          </span>
          <input
        type="text"
        v-model="searchQuery"
        @input="fetchProducts(1)"
        placeholder="Search"
        class="form-control"
          />
        </div>
      </div>
      <div class="col-md-6">
        <select v-model="selectedCategory" @change="fetchProducts(1)" class="form-control">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered product-table">
        <thead>
          <tr>
            <th style="width: 50px">#</th>
            <th style="width: 180px" @click="sort('name')" class="sortable">
              Name
              <i v-if="sortBy === 'name'" :class="sortDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
            </th>
            <th style="width: 150px" @click="sort('sku')" class="sortable">
              SKU
              <i v-if="sortBy === 'sku'" :class="sortDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
            </th>
            <th style="width: 150px" @click="sort('category')" class="sortable">
              Category
              <i v-if="sortBy === 'category'" :class="sortDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
            </th>
            <th style="width: 120px" @click="sort('quantity')" class="sortable">
              Quantity
              <i v-if="sortBy === 'quantity'" :class="sortDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
            </th>
            <th style="width: 120px" @click="sort('price')" class="sortable">
              Price
              <i v-if="sortBy === 'price'" :class="sortDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
            </th>
            <th class="text-center" style="width: 130px">Action</th>
          </tr>
        </thead>
        <tbody>
            <tr v-if="!loading && products.length === 0">
            <td colspan="6" class="text-center">No Available Data</td>
          </tr>
          <tr v-for="(product, index) in products" :key="product.id">
            <td>{{ index + 1 + (pagination.current_page - 1) * pagination.per_page }}</td>
            <td>{{ product.name }}</td>
            <td>{{ product.sku }}</td>
            <td>{{ product.category }}</td>
            <td>{{ product.quantity }}</td>
            <td>RM {{ product.price }}</td>
            <td class="text-center">
              <button
                :disabled="product.quantity <= 0"
                @click="addToCart(product.id)"
                class="btn btn-sm btn-primary"
              >
                Add to Cart
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <nav v-if="pagination.last_page > 1" class="pagination-wrapper">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
          <button class="page-link" @click="fetchProducts(pagination.current_page - 1)">Previous</button>
        </li>
        <li class="page-item" :class="{ active: pagination.current_page === 1 }">
          <button class="page-link" @click="fetchProducts(1)">1</button>
        </li>
        <li v-if="pagination.current_page > 4" class="page-item disabled">
          <span class="page-link">...</span>
        </li>
        <li
          v-for="page in pagesAroundCurrent"
          :key="page"
          class="page-item"
          :class="{ active: pagination.current_page === page }"
        >
          <button class="page-link" @click="fetchProducts(page)">{{ page }}</button>
        </li>
        <li v-if="pagination.current_page < pagination.last_page - 3" class="page-item disabled">
          <span class="page-link">...</span>
        </li>
        <li class="page-item" :class="{ active: pagination.current_page === pagination.last_page }">
          <button class="page-link" @click="fetchProducts(pagination.last_page)">{{ pagination.last_page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
          <button class="page-link" @click="fetchProducts(pagination.current_page + 1)">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import pop from '@/utils/pop'
import api from '@/utils/axios'

const showPage = ref(false)
const router = useRouter()
const products = ref([])
const pagination = ref({ current_page: 1, last_page: 1, per_page: 10 })
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ref([])
const sortBy = ref('name')
const sortDir = ref('asc')

const fetchProducts = async (page = 1) => {
  loading.value = true
  try {
    pop.loading()
    const token = localStorage.getItem('token')
    const res = await api.get(`/api/product/get-all-products`, {
      headers: { Authorization: `Bearer ${token}` },
      params: {
        page,
        per_page: 10,
        search: searchQuery.value,
        category: selectedCategory.value,
        sort_by: sortBy.value,
        sort_dir: sortDir.value,
      }
    })

    if (Array.isArray(res.data.data)) {
      products.value = res.data.data
      pagination.value = {
        current_page: res.data.current_page,
        last_page: res.data.last_page,
        per_page: res.data.per_page,
      }
    } else {
      pop.error('Unexpected response format')
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to load products")
  } finally {
    loading.value = false
    pop.close()
  }
}

const fetchCategories = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await api.get('/api/product/get-categories', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (res.data.status === true) {
      categories.value = res.data.data.categories
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to load categories")
  }
}

const pagesAroundCurrent = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  let pages = []
  for (let i = current - 1; i <= current + 1; i++) {
    if (i > 1 && i < last) pages.push(i)
  }
  return pages
})

const sort = (field) => {
  if (sortBy.value === field) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortDir.value = 'asc'
  }
  fetchProducts(1)
}

const addToCart = async (productId) => {
  pop.loading()
  try {
    const res = await api.post('/api/cart/add-to-cart', {
      product_id: productId,
      quantity: 1
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })

    if (res.data.status === true) {
      pop.success('Added to cart')
      // Update quantity
      const product = products.value.find(p => p.id === productId)
      if (product && product.quantity > 0) {
        product.quantity -= 1
      }
    } else {
      pop.error(res.data.message)
    }
  } catch (e) {
    console.log(e)
    pop.error(e.response?.data?.message || "Failed to add to cart")
  } finally {
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

  fetchProducts()
  fetchCategories()
})
</script>

<style scoped>
/* Minimal container for full height page */
.dashboard-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding-bottom: 2rem;
}

/* Fix column size */
.product-table {
  table-layout: fixed;
  width: 100%;
  min-width: 900px;
}

/* Prevent layout shift during loading/search */
.product-table th,
.product-table td {
  white-space: normal;
  overflow: visible;
  text-overflow: unset;
}


/* Make sortable columns clickable */
.sortable {
  cursor: pointer;
}

/* Pagination spacing */
.pagination-wrapper {
  margin-top: 30px;
}
</style>
