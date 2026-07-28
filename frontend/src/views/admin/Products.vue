<template>
  <div>
    <h1>商品管理</h1>

    <form class="new-product" @submit.prevent="handleCreate">
      <input v-model="form.name" placeholder="商品名稱" required />
      <input v-model.number="form.price" type="number" placeholder="價格" required />
      <input v-model="form.sku" placeholder="SKU" required />
      <input v-model.number="form.category_id" type="number" placeholder="分類ID" required />
      <input v-model.number="form.initial_stock" type="number" placeholder="初始庫存" />
      <button type="submit">新增商品</button>
    </form>

    <table class="table">
      <thead>
        <tr><th>ID</th><th>名稱</th><th>價格</th><th>分類</th><th>庫存</th><th>狀態</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="p in products" :key="p.id">
          <td>{{ p.id }}</td>
          <td>{{ p.name }}</td>
          <td>NT$ {{ p.price }}</td>
          <td>{{ p.category?.name }}</td>
          <td>{{ p.inventory?.quantity ?? '-' }}</td>
          <td>{{ p.is_active ? '上架' : '下架' }}</td>
          <td>
            <button @click="toggleActive(p)">{{ p.is_active ? '下架' : '上架' }}</button>
            <button @click="remove(p.id)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import api from '../../api/axios'

const products = ref([])
const form = reactive({
  name: '',
  price: null,
  sku: '',
  category_id: null,
  initial_stock: 0,
})

async function load() {
  const { data } = await api.get('/admin/products')
  products.value = data.data
}

async function handleCreate() {
  await api.post('/admin/products', form)
  Object.assign(form, { name: '', price: null, sku: '', category_id: null, initial_stock: 0 })
  await load()
}

async function toggleActive(product) {
  await api.patch(`/admin/products/${product.id}`, { is_active: !product.is_active })
  await load()
}

async function remove(id) {
  await api.delete(`/admin/products/${id}`)
  await load()
}

onMounted(load)
</script>

<style scoped>
.new-product {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.new-product input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
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
button {
  margin-right: 6px;
}
</style>
