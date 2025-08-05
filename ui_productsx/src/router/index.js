import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AuthRegister from '../views/AuthRegister.vue'
import AuthLogin from '@/views/AuthLogin.vue'
import HomeProduct from '@/views/HomeProduct.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import DefaultLayout2 from '@/layouts/DefaultLayout2.vue'
import Dashboard from '@/views/Dashboard.vue'
import HomeCart from '@/views/HomeCart.vue'
import HomeOrder from '@/views/HomeOrder.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/',
      name: 'login',
      component: AuthLogin,
    },
    {
      path: '/register',
      name: 'register',
      component: AuthRegister,
    },
    // {
    //   path: '/home',
    //   name: 'home',
    //   component: HomeProduct,
    // },
    {
      path: '/home',
      component: DefaultLayout2,
      children: [
        { path: 'products', component: HomeProduct },
        { path: 'dashboard', component: Dashboard },
        { path: 'carts', component: HomeCart },
        { path: 'orders', component: HomeOrder },
      ]
    },
  ],
})

export default router
