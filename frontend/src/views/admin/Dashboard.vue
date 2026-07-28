<template>
  <div>
    <h1>儀表板</h1>
    <div v-if="stats" class="stats-grid">
      <div class="stat-card">
        <p class="label">商品總數</p>
        <p class="value">{{ stats.total_products }}</p>
      </div>
      <div class="stat-card">
        <p class="label">訂單總數</p>
        <p class="value">{{ stats.total_orders }}</p>
      </div>
      <div class="stat-card">
        <p class="label">待付款訂單</p>
        <p class="value">{{ stats.pending_orders }}</p>
      </div>
      <div class="stat-card">
        <p class="label">已付款訂單</p>
        <p class="value">{{ stats.paid_orders }}</p>
      </div>
      <div class="stat-card highlight">
        <p class="label">總銷售額</p>
        <p class="value">NT$ {{ stats.total_sales }}</p>
      </div>
      <div class="stat-card warning">
        <p class="label">低庫存商品數</p>
        <p class="value">{{ stats.low_stock_count }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../api/axios'

const stats = ref(null)

onMounted(async () => {
  const { data } = await api.get('/admin/dashboard/stats')
  stats.value = data
})
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 16px;
  margin-top: 20px;
}
.stat-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}
.stat-card .label {
  font-size: 13px;
  color: #6b7280;
}
.stat-card .value {
  font-size: 24px;
  font-weight: bold;
  margin-top: 6px;
}
.stat-card.highlight .value {
  color: #059669;
}
.stat-card.warning .value {
  color: #dc2626;
}
</style>
