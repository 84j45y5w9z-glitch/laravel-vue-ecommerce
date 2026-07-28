<template>
  <div>
    <div class="toolbar">
      <input v-model="keyword" placeholder="搜尋商品..." @keyup.enter="search" />
      <select v-model="sort" @change="search">
        <option value="">預設排序</option>
        <option value="price_asc">價格由低到高</option>
        <option value="price_desc">價格由高到低</option>
      </select>
    </div>

    <div v-if="productStore.loading">載入中...</div>
    <div v-else class="grid">
      <ProductCard v-for="p in productStore.products" :key="p.id" :product="p" />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'

const productStore = useProductStore()
const route = useRoute()
const keyword = ref('')
const sort = ref('')

function search() {
  productStore.fetchProducts({
    keyword: keyword.value || undefined,
    sort: sort.value || undefined,
    category_id: route.query.category_id || undefined,
  })
}

onMounted(search)
</script>

<style scoped>
.toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}
.toolbar input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}
</style>
