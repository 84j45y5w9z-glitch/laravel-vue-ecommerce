<template>
  <div>
    <h1>訂單管理</h1>
    <select v-model="statusFilter" @change="load">
      <option value="">全部狀態</option>
      <option value="pending">待付款</option>
      <option value="paid">已付款</option>
      <option value="processing">出貨中</option>
      <option value="shipped">已出貨</option>
      <option value="completed">已完成</option>
      <option value="cancelled">已取消</option>
    </select>

    <table class="table">
      <thead>
        <tr><th>訂單編號</th><th>金額</th><th>狀態</th><th>更新狀態</th></tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>{{ order.order_no }}</td>
          <td>NT$ {{ order.total_amount }}</td>
          <td>{{ order.status }}</td>
          <td>
            <select :value="order.status" @change="updateStatus(order, $event.target.value)">
              <option value="pending">待付款</option>
              <option value="paid">已付款</option>
              <option value="processing">出貨中</option>
              <option value="shipped">已出貨</option>
              <option value="completed">已完成</option>
              <option value="cancelled">已取消</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../api/axios'

const orders = ref([])
const statusFilter = ref('')

async function load() {
  const { data } = await api.get('/admin/orders', { params: { status: statusFilter.value || undefined } })
  orders.value = data.data
}

async function updateStatus(order, status) {
  await api.patch(`/admin/orders/${order.id}/status`, { status })
  await load()
}

onMounted(load)
</script>

<style scoped>
.table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  margin-top: 16px;
}
.table th, .table td {
  border-bottom: 1px solid #eee;
  padding: 8px 10px;
  text-align: left;
}
</style>
