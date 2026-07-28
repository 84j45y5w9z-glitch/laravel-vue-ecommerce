<template>
  <div>
    <h1>我的訂單</h1>
    <div v-if="orders.length === 0">尚無訂單紀錄</div>
    <table v-else class="order-table">
      <thead>
        <tr>
          <th>訂單編號</th>
          <th>狀態</th>
          <th>金額</th>
          <th>建立時間</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>{{ order.order_no }}</td>
          <td><span class="badge">{{ statusLabel(order.status) }}</span></td>
          <td>NT$ {{ order.total_amount }}</td>
          <td>{{ new Date(order.created_at).toLocaleString() }}</td>
          <td><RouterLink :to="`/orders/${order.id}`">查看</RouterLink></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../api/axios'

const orders = ref([])

const statusMap = {
  pending: '待付款',
  paid: '已付款',
  processing: '出貨中',
  shipped: '已出貨',
  completed: '已完成',
  cancelled: '已取消',
}

function statusLabel(status) {
  return statusMap[status] || status
}

onMounted(async () => {
  const { data } = await api.get('/orders')
  orders.value = data.data
})
</script>

<style scoped>
.order-table {
  width: 100%;
  border-collapse: collapse;
}
.order-table th, .order-table td {
  border-bottom: 1px solid #eee;
  padding: 10px;
  text-align: left;
}
.badge {
  background: #eef2ff;
  color: #4338ca;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 12px;
}
</style>
