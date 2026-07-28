<template>
  <div>
    <section class="hero">
      <h1>歡迎光臨 Shop</h1>
      <p>PHP 8.4 + Laravel API + Vue3 打造的電商展示網站</p>
      <RouterLink to="/products" class="btn">開始逛逛</RouterLink>
    </section>

    <section>
      <h2>分類</h2>
      <div class="categories">
        <RouterLink
          v-for="cat in productStore.categories"
          :key="cat.id"
          :to="{ path: '/products', query: { category_id: cat.id } }"
          class="category-chip"
        >
          {{ cat.name }}
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useProductStore } from '../stores/product'

const productStore = useProductStore()

onMounted(() => {
  productStore.fetchCategories()
})
</script>

<style scoped>
.hero {
  text-align: center;
  padding: 48px 0;
}
.btn {
  display: inline-block;
  margin-top: 16px;
  padding: 10px 24px;
  background: #111827;
  color: white;
  border-radius: 6px;
  text-decoration: none;
}
.categories {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 12px;
}
.category-chip {
  padding: 6px 14px;
  border: 1px solid #ddd;
  border-radius: 999px;
  text-decoration: none;
  color: #333;
}
</style>
