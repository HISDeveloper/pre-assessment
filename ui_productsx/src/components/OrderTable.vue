<script setup>
import { ref } from 'vue';
import apiClient from '../api.js';
import PaginateButton from './PaginateButton.vue';

const orders = ref(null);

const currentPage = ref(1);
const totalRow = ref(0);
const totalPages = ref();
const tableRow = ref(1);

async function fetchOrder() {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.get('/get_orders');
        orders.value = response.data.orders;
        const ceilPages = Math.ceil(response.data.totalPages)
        totalPages.value = ceilPages;
        console.log('order data:', response.data);
        console.log('order page:', ceilPages);
    } catch (error) {
        console.error('Could not fetch order:', error);
    }
}

fetchOrder();

async function fetchOrderPaginate(skip) {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.post('/get_order_paginate', {
            "skip": skip
        });
        orders.value = response.data.orders;
        console.log('product data:', response.data);
    } catch (error) {
        console.error('Could not fetch product:', error);
    }
}

const handlePageChange = (newPage) => {
    currentPage.value = newPage;
    totalRow.value = (newPage * 10) - 10;
    tableRow.value = totalRow.value +1;
    // Fetch new data here
    console.log('Page changed to:', newPage);
    console.log('skip row:', totalRow.value);
    fetchOrderPaginate(totalRow.value);
};
</script>

<template>
    <h2>Order History</h2>
    <PaginateButton :current-page="currentPage" :total-pages="totalPages" @page-change="handlePageChange" />
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Checkout Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in orders" :key="item.id">
                    <td>{{ tableRow + index }}</td>
                    <td>{{ item.order_product.name }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.total }}</td>
                    <td>{{ item.checkout_date }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>