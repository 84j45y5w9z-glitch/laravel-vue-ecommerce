<template>
  <div class="layout">
    <Navbar />
    <main class="container">
      <RouterView />
    </main>
    <footer class="footer">© 2026 Shop Demo — 作品集展示用電商網站</footer>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import Navbar from '../components/Navbar.vue'
</script>

<style scoped>
.layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
.container {
  flex: 1;
  max-width: 1100px;
  margin: 0 auto;
  padding: 24px 16px;
  width: 100%;
}
.footer {
  text-align: center;
  padding: 16px;
  color: #888;
  font-size: 13px;
}
</style>
