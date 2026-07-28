<template>
  <RouterLink :to="`/products/${product.slug}`" class="card">
    <div class="image-placeholder">
      <img v-if="product.images?.length" :src="product.images[0]" :alt="product.name" />
      <span v-else>無圖片</span>
    </div>
    <h3>{{ product.name }}</h3>
    <p class="price">NT$ {{ product.price }}</p>
    <p class="stock" :class="{ out: !product.in_stock }">
      {{ product.in_stock ? `庫存 ${product.stock}` : '缺貨中' }}
    </p>
  </RouterLink>
</template>

<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  product: { type: Object, required: true },
})
</script>

<style scoped>
.card {
  display: block;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 12px;
  text-decoration: none;
  color: inherit;
  transition: box-shadow 0.2s;
}
.card:hover {
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}
.image-placeholder {
  aspect-ratio: 1 / 1;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  overflow: hidden;
  color: #9ca3af;
  margin-bottom: 8px;
}
.image-placeholder img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.price {
  color: #dc2626;
  font-weight: bold;
}
.stock {
  font-size: 13px;
  color: #059669;
}
.stock.out {
  color: #dc2626;
}
</style>
