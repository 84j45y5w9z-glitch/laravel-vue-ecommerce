<template>
  <div>
    <h1>庫存管理</h1>
    <label class="filter">
      <input type="checkbox" v-model="lowStockOnly" @change="load" />
      只顯示低庫存商品
    </label>

    <table class="table">
      <thead>
        <tr><th>商品</th><th>庫存數量</th><th>已預留</th><th>安全庫存</th><th>調整</th></tr>
      </thead>
      <tbody>
        <tr v-for="inv in inventories" :key="inv.id" :class="{ low: isLow(inv) }">
          <td>{{ inv.product?.name }}</td>
          <td>{{ inv.quantity }}</td>
          <td>{{ inv.reserved }}</td>
          <td>{{ inv.safety_stock }}</td>
          <td>
            <input type="number" v-model.number="inv.quantity" style="width: 70px" />
            <button @click="save(inv)">儲存</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../api/axios'

const inventories = ref([])
const lowStockOnly = ref(false)

async function load() {
  const { data } = await api.get('/admin/inventories', {
    params: { low_stock_only: lowStockOnly.value ? 1 : undefined },
  })
  inventories.value = data.data
}

function isLow(inv) {
  return inv.quantity - inv.reserved <= inv.safety_stock
}

async function save(inv) {
  await api.patch(`/admin/inventories/${inv.id}`, { quantity: inv.quantity })
  await load()
}

onMounted(load)
</script>

<style scoped>
.filter {
  display: block;
  margin-bottom: 12px;
}
.table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}
.table th, .table td {
  border-bottom: 1px solid #eee;
  padding: 8px 10px;
  text-align: left;
}
.table tr.low {
  background: #fef2f2;
}
</style>
