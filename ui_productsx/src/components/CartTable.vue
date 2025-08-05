<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'
import apiClient from '../api.js';
import ConfirmationModal from './ConfirmationModal.vue';
import NotificationModal from './NotificationModal.vue';
import PaginateButton from './PaginateButton.vue';

const cart = ref([]);
const grandTotal = ref(0);
const showConfirmation = ref(false);
const showNotification = ref(false);
const successMsg = ref("Items checked out");
const router = useRouter();

const currentPage = ref(1);
const totalRow = ref(0);
const totalPages = ref();

async function fetchUserCart() {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.get('/get_user_carts');
        cart.value = response.data.carts;
        const ceilPages = Math.ceil(response.data.totalPages)
        totalPages.value = ceilPages;
        console.log('cart data:', response.data);
        console.log('totalPages:', ceilPages);
    } catch (error) {
        console.error('Could not fetch cart:', error);
    }
}
fetchUserCart();

async function fetchCartPaginate(skip) {
    try {
        // No need to add headers anymore. its kept in api.js \o/
        const response = await apiClient.post('/get_user_cart_paginate', {
            "skip": skip
        });
        cart.value = response.data.carts;
        console.log('cart data:', response.data);
    } catch (error) {
        console.error('Could not fetch cart:', error);
    }
}

const handlePageChange = (newPage) => {
    currentPage.value = newPage;
    totalRow.value = (newPage * 5) - 5;
    // Fetch new data here
    console.log('Page changed to:', newPage);
    console.log('skip row:', totalRow.value);
    fetchCartPaginate(totalRow.value);
};

async function removeFromCart(id) {
    try {
        const response = await apiClient.delete('/remove_from_cart',
            {
                "data": {
                    "cart_id": id,
                }
            });
        console.log(response.data);
        const statusCode = response.status;

        if (statusCode === 200) {
            removeRow(id);
        }
    } catch (error) {
        console.error('Could not delete cart:', error);
    }
}

async function removeRow(id) {
    cart.value = cart.value.filter(item => item.id !== id);
};

const handleCheckout = async () => {
    console.log('checkout...')
    // showConfirmation.value = false;
    try {
        // const response = await apiClient.post('/add_order',
        //     {
        //         "product_list": JSON.parse(JSON.stringify(cart.value)),
        //     });

        const response = await apiClient.post('/add_order',
            {
                "product_list": cart.value.map(item => ({
                    product_id: item.cart_product.id,
                    quantity: item.quantity,
                    price: item.cart_product.price
                })),
            });

        console.log(response.data);
        const statusCodeOrder = response.status;
        if (statusCodeOrder === 200) {
            //remove all cart
            const response = await apiClient.delete('/remove_user_cart',
                {
                    "data": {
                        "user": 'just delete',
                    }
                }
            );
            console.log(response.data);
            const statusCodeCart = response.status;
            if (statusCodeCart === 200) {
                cart.value = [];
                showNotification.value = true
                setTimeout(() => {
                    showNotification.value = false
                }, 3000)
            }
        }
    } catch (error) {
        // Handle error
        console.error('Checkout failed', error);
    } finally {
        showConfirmation.value = false;
    }
};
</script>

<template>
    <h2>Your Cart</h2>
    <ConfirmationModal :show="showConfirmation" @confirm="handleCheckout" @cancel="showConfirmation = false" />
    <NotificationModal :show="showNotification" :message="successMsg" />
    <!-- <PaginateButton :current-page="currentPage" :total-pages="totalPages" @page-change="handlePageChange" /> -->
    <div class="table-wrapper">
        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in cart" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.cart_product.name }}</td>
                        <td>{{ item.quantity }}</td>
                        <td>{{ item.cart_product.price }}</td>
                        <td>{{ item.cart_product.price * item.quantity }}
                        </td>
                        <td>
                            <button class="remove-button" @click="removeFromCart(item.id)">
                                <span class="add-icon">-</span>
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <hr>
        <br>
        <div class="right-summary">
            <h3>Order Summary</h3>
            <p>Total Product: {{ cart.length }}</p>
            <p>Total Items: {{cart.reduce((total, item) => total + item.quantity,
                0)}}</p>
            <p>Subtotal: {{cart.reduce((total, item) => total + (item.quantity * item.cart_product.price),
                0).toFixed(2)}}</p>
            <!-- Add more summary info as needed -->
            <button class="checkout-button" @click="showConfirmation = true">Proceed to Checkout</button>
        </div>
    </div>
</template>

<style>
.table-wrapper {
    display: flex;
    flex-direction: column;
    width: 100%;
    position: relative;
    /* Needed for absolute positioning alternative */
}

.card-right-fixed {
    position: fixed;
    right: 20px;
    top: 20px;
    width: 300px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.remove-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    background-color: #b92e2e;
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

.remove-button:hover {
    background-color: #d94242;
    /* Darker green on hover */
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.remove-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
}

.right-summary {
    align-self: flex-end;
    /* This forces right alignment */
    width: 300px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    margin-left: auto;
    /* Key property that pushes it right */
}

/* Checkout button styling */
.checkout-button {
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #009879;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
}

.checkout-button:hover {
    background-color: #007d63;
}
</style>

<script>

</script>