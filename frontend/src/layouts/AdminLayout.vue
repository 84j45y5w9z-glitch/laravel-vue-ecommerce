<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>管理後台</h2>
      <nav>
        <RouterLink to="/admin">儀表板</RouterLink>
        <RouterLink to="/admin/products">商品管理</RouterLink>
        <RouterLink to="/admin/orders">訂單管理</RouterLink>
        <RouterLink to="/admin/inventory">庫存管理</RouterLink>
        <RouterLink to="/">回到前台</RouterLink>
      </nav>
    </aside>
    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { RouterView, RouterLink } from 'vue-router'
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 220px;
  background: #1f2937;
  color: white;
  padding: 20px;
}
.sidebar nav {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 20px;
}
.sidebar a {
  color: #d1d5db;
  text-decoration: none;
}
.sidebar a.router-link-active {
  color: white;
  font-weight: bold;
}
.content {
  flex: 1;
  padding: 24px;
  background: #f9fafb;
}
</style>
