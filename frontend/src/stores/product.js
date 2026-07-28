import { defineStore } from 'pinia'
import api from '../api/axios'

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    categories: [],
    currentProduct: null,
    meta: null,
    loading: false,
  }),
  actions: {
    async fetchCategories() {
      const { data } = await api.get('/categories')
      this.categories = data
    },
    async fetchProducts(params = {}) {
      this.loading = true
      try {
        const { data } = await api.get('/products', { params })
        this.products = data.data
        this.meta = data.meta
      } finally {
        this.loading = false
      }
    },
    async fetchProduct(slug) {
      const { data } = await api.get(`/products/${slug}`)
      this.currentProduct = data.data
    },
  },
})
