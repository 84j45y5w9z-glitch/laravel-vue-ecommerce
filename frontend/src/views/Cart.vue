<template>
  <div>
    <h1>購物車</h1>
    <div v-if="cart.items.length === 0">購物車是空的</div>
    <table v-else class="cart-table">
      <thead>
        <tr>
          <th>商品</th>
          <th>單價</th>
          <th>數量</th>
          <th>小計</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in cart.items" :key="item.id">
          <td>{{ item.product.name }}</td>
          <td>NT$ {{ item.product.price }}</td>
          <td>
            <input
              type="number"
              min="1"
              v-model.number="item.quantity"
              @change="cart.updateItem(item.id, item.quantity)"
            />
          </td>
          <td>NT$ {{ item.product.price * item.quantity }}</td>
          <td><button @click="cart.removeItem(item.id)">移除</button></td>
        </tr>
      </tbody>
    </table>

    <div v-if="cart.items.length" class="summary">
      <p>總金額：<strong>NT$ {{ cart.totalAmount }}</strong></p>
      <RouterLink to="/checkout" class="btn">前往結帳</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cart'

const cart = useCartStore()
onMounted(() => cart.fetchCart())
</script>

<style scoped>
.cart-table {
  width: 100%;
  border-collapse: collapse;
}
.cart-table th, .cart-table td {
  border-bottom: 1px solid #eee;
  padding: 10px;
  text-align: left;
}
.cart-table input {
  width: 60px;
  padding: 4px;
}
.summary {
  margin-top: 20px;
  text-align: right;
}
.btn {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 24px;
  background: #111827;
  color: white;
  border-radius: 6px;
  text-decoration: none;
}
</style>
