<script setup>
import { ref } from 'vue';
import apiClient from '../api.js';
import PaginateButton from './PaginateButton.vue';

const products = ref(null);
const showPopup = ref(false);

const currentPage = ref(1);
const totalRow = ref(0);
const totalPages = ref();
const tableRow = ref(1);

async function fetchProducts() {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.get('/get_products');
        products.value = response.data.products;
        const ceilPages = Math.ceil(response.data.totalPages)
        totalPages.value = ceilPages;
        console.log('product data:', response.data);
    } catch (error) {
        console.error('Could not fetch product:', error);
    }
}
fetchProducts();

async function fetchProductsPaginate(skip) {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.post('/get_product_paginate', {
            "skip": skip
        });
        products.value = response.data.products;
        console.log('product data:', response.data);
    } catch (error) {
        console.error('Could not fetch product:', error);
    }
}

async function addTocart(item) {
    console.log('clicked')
    // if (item.orderQty > 0 && item.orderQty <= item.quantity) {
    const qtyInput = document.getElementById('qty_' + item.id);
    const qty = parseInt(qtyInput.value);
    if (qty > 0) {
        try {
            const response = await apiClient.post('/add_to_cart', {
                "user_id": localStorage.getItem('id'),
                "product_id": item.id,
                "quantity": qty
            });
            console.log(response.data);
            console.log('Added:', item);
            qtyInput.value = 0;
            showPopup.value = true

            // Hide after 2 seconds
            setTimeout(() => {
                showPopup.value = false
            }, 2000)
        } catch (error) {
            console.error('Could not fetch product:', error);
        }
    }
}

const handlePageChange = (newPage) => {
    currentPage.value = newPage;
    totalRow.value = (newPage * 10) - 10;
    tableRow.value = totalRow.value +1;
    // Fetch new data here
    console.log('Page changed to:', newPage);
    console.log('skip row:', totalRow.value);
    fetchProductsPaginate(totalRow.value);
};
</script>

<template>
    <h2>Product List</h2>
    <hr>
    <!-- Search:
    <input type="text" id="search">
    <br>
    <br> -->
    <div v-if="showPopup" class="cart-popup">
        Added to Cart!
    </div>
    <PaginateButton :current-page="currentPage" :total-pages="totalPages" @page-change="handlePageChange" />
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price(ea)</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in products" :key="item.id">
                    <td>{{ tableRow + index }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.category }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.price }}</td>
                    <td><input class="five-digit-input" type="number" :id="'qty_' + item.id" :min="1"
                            :max="item.quantity" value="0">
                    </td>
                    <!-- <td><input type="number" v-model.number="item.orderQty" :max="item.quantity" min="0"
                            class="qty-input" :id="'qty_' + item.id" value="0">
                    </td> -->
                    <td>
                        <button class="add-button" @click="addTocart(item)">
                            <span class="add-icon">+</span>
                            Add Item
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- <div>
        <table class="my-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in products" :key="item.id">
                    <td class="center">{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.quantity }}</td>
                    <td class="center"><input type="number" :id="'qty_' + item.id" min="1" value="0"></td>
                    <td class="center">
                        <button class="add-button" @click="addTocart(item.id)">
                            <span class="add-icon">+</span>
                            Add Item
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> -->
</template>

<style>
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
    gap: 0.5rem;
}

.cart-popup {
    position: fixed;
    top: 20px;
    /* Changed from bottom to top */
    right: 20px;
    /* Keeps it on the right side */
    padding: 12px 24px;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s;
    z-index: 1000;
    /* Added to ensure it stays above other elements */
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
        /* Changed to negative to animate from top */
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.five-digit-input {
    width: 5rem;
    /* 'ch' unit = width of the '0' character */
    padding: 0.5rem;
    font-family: monospace;
    /* Fixed-width numbers */
    font-size: 1rem;
    text-align: right;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input {
    font-family: inherit;
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    transition: all 0.3s ease;
    width: 100%;
    max-width: 400px;
    margin: 0.5rem 0;
    box-sizing: border-box;
}

/* Focus state */
input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

/* Disabled state */
input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

/* Error state */
input.error {
    border-color: #e74c3c;
}

input.error:focus {
    box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2);
}

.center {
    text-align: center;
}

.add-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    background-color: #4CAF50;
    /* Green color */
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.add-button:hover {
    background-color: #45a049;
    /* Darker green on hover */
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.add-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
}

.add-icon {
    margin-right: 8px;
    font-size: 20px;
    font-weight: bold;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    margin-bottom: 20px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9em;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-width: 400px;
}

.styled-table thead tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
}

.styled-table th,
.styled-table td {
    padding: 12px 15px;
}

.styled-table tbody tr {
    border-bottom: 1px solid #dddddd;
    transition: all 0.2s ease;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #009879;
}

.styled-table tbody tr:hover {
    background-color: #f1f1f1;
    transform: scale(1.01);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .styled-table {
        font-size: 0.8em;
    }

    .styled-table th,
    .styled-table td {
        padding: 8px 10px;
    }
}
</style>

<script>



</script>