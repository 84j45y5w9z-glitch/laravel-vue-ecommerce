<template>
  <header class="navbar">
    <RouterLink to="/" class="brand">🛍️ Shop</RouterLink>
    <nav>
      <RouterLink to="/products">商品</RouterLink>
      <RouterLink v-if="auth.isLoggedIn" to="/orders">我的訂單</RouterLink>
      <RouterLink v-if="auth.isLoggedIn" to="/cart">購物車 ({{ cart.itemCount }})</RouterLink>
      <RouterLink v-if="auth.isAdmin" to="/admin">後台</RouterLink>
      <button v-if="auth.isLoggedIn" @click="handleLogout">登出</button>
      <RouterLink v-else to="/login">登入</RouterLink>
    </nav>
  </header>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()

onMounted(async () => {
  if (auth.isLoggedIn) {
    await auth.fetchMe().catch(() => {})
    await cart.fetchCart().catch(() => {})
  }
})

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 24px;
  border-bottom: 1px solid #eee;
}
.brand {
  font-weight: bold;
  font-size: 20px;
  text-decoration: none;
  color: #111;
}
nav {
  display: flex;
  gap: 16px;
  align-items: center;
}
nav a, nav button {
  text-decoration: none;
  color: #333;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
}
nav a.router-link-active {
  font-weight: bold;
  color: #2563eb;
}
</style>
