import { defineStore } from 'pinia'
import api from '../api/axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    totalAmount: 0,
  }),
  getters: {
    itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
  },
  actions: {
    async fetchCart() {
      const { data } = await api.get('/cart')
      this.items = data.items
      this.totalAmount = data.total_amount
    },
    async addItem(productId, quantity = 1) {
      await api.post('/cart/items', { product_id: productId, quantity })
      await this.fetchCart()
    },
    async updateItem(itemId, quantity) {
      await api.patch(`/cart/items/${itemId}`, { quantity })
      await this.fetchCart()
    },
    async removeItem(itemId) {
      await api.delete(`/cart/items/${itemId}`)
      await this.fetchCart()
    },
  },
})
