import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    component: () => import('../layouts/DefaultLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('../views/Home.vue') },
      { path: 'products', name: 'products', component: () => import('../views/ProductList.vue') },
      { path: 'products/:slug', name: 'product-detail', component: () => import('../views/ProductDetail.vue') },
      { path: 'cart', name: 'cart', component: () => import('../views/Cart.vue'), meta: { requiresAuth: true } },
      { path: 'checkout', name: 'checkout', component: () => import('../views/Checkout.vue'), meta: { requiresAuth: true } },
      { path: 'orders', name: 'orders', component: () => import('../views/OrderHistory.vue'), meta: { requiresAuth: true } },
      { path: 'orders/:id', name: 'order-detail', component: () => import('../views/OrderDetail.vue'), meta: { requiresAuth: true } },
      { path: 'login', name: 'login', component: () => import('../views/Login.vue') },
      { path: 'register', name: 'register', component: () => import('../views/Register.vue') },
    ],
  },
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', name: 'admin-dashboard', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'products', name: 'admin-products', component: () => import('../views/admin/Products.vue') },
      { path: 'orders', name: 'admin-orders', component: () => import('../views/admin/Orders.vue') },
      { path: 'inventory', name: 'admin-inventory', component: () => import('../views/admin/Inventory.vue') },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return { name: 'login' }
  }

  if (to.meta.requiresAdmin) {
    if (!auth.user) {
      await auth.fetchMe().catch(() => {})
    }
    if (!auth.isAdmin) {
      return { name: 'home' }
    }
  }

  return true
})

export default router
