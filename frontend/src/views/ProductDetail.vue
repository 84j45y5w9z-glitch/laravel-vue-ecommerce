<template>
  <div v-if="productStore.currentProduct" class="detail">
    <div class="image-placeholder">
      <img
        v-if="productStore.currentProduct.images?.length"
        :src="productStore.currentProduct.images[0]"
        :alt="productStore.currentProduct.name"
      />
      <span v-else>無圖片</span>
    </div>
    <div class="info">
      <h1>{{ productStore.currentProduct.name }}</h1>
      <p class="price">NT$ {{ productStore.currentProduct.price }}</p>
      <p>{{ productStore.currentProduct.description }}</p>
      <p class="stock" :class="{ out: !productStore.currentProduct.in_stock }">
        {{ productStore.currentProduct.in_stock ? `庫存 ${productStore.currentProduct.stock}` : '缺貨中' }}
      </p>

      <div class="actions">
        <input type="number" v-model.number="quantity" min="1" :max="productStore.currentProduct.stock" />
        <button :disabled="!productStore.currentProduct.in_stock" @click="handleAddToCart">
          加入購物車
        </button>
      </div>
      <p v-if="message" class="message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '../stores/product'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const cartStore = useCartStore()
const authStore = useAuthStore()

const quantity = ref(1)
const message = ref('')

function load() {
  productStore.fetchProduct(route.params.slug)
}

onMounted(load)
watch(() => route.params.slug, load)

async function handleAddToCart() {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }
  try {
    await cartStore.addItem(productStore.currentProduct.id, quantity.value)
    message.value = '已加入購物車！'
  } catch (e) {
    message.value = e.response?.data?.message || '加入失敗'
  }
}
</script>

<style scoped>
.detail {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}
.image-placeholder {
  aspect-ratio: 1 / 1;
  background: #f3f4f6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.image-placeholder img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.price {
  font-size: 24px;
  color: #dc2626;
  font-weight: bold;
}
.stock.out {
  color: #dc2626;
}
.actions {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}
.actions input {
  width: 70px;
  padding: 8px;
}
.actions button {
  padding: 8px 20px;
  background: #111827;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.actions button:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}
.message {
  margin-top: 10px;
  color: #059669;
}
</style>
