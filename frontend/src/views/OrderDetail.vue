<template>
  <div v-if="order">
    <h1>訂單詳情</h1>
    <p>訂單編號：{{ order.order_no }}</p>
    <p>狀態：{{ statusLabel(order.status) }}</p>
    <p>收件人：{{ order.receiver_name }}（{{ order.receiver_phone }}）</p>
    <p>收件地址：{{ order.shipping_address }}</p>

    <table class="order-table">
      <thead>
        <tr><th>商品</th><th>單價</th><th>數量</th><th>小計</th></tr>
      </thead>
      <tbody>
        <tr v-for="item in order.items" :key="item.product_id">
          <td>{{ item.product_name }}</td>
          <td>NT$ {{ item.price }}</td>
          <td>{{ item.quantity }}</td>
          <td>NT$ {{ item.price * item.quantity }}</td>
        </tr>
      </tbody>
    </table>
    <p class="total">總金額：NT$ {{ order.total_amount }}</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api/axios'

const route = useRoute()
const order = ref(null)

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
  const { data } = await api.get(`/orders/${route.params.id}`)
  order.value = data.data
})
</script>

<style scoped>
.order-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 16px;
}
.order-table th, .order-table td {
  border-bottom: 1px solid #eee;
  padding: 8px;
  text-align: left;
}
.total {
  margin-top: 16px;
  font-size: 18px;
  font-weight: bold;
  text-align: right;
}
</style>
