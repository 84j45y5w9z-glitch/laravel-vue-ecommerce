import { defineStore } from 'pinia'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    async login(email, password) {
      const { data } = await api.post('/auth/login', { email, password })
      this.setSession(data.user, data.token)
    },
    async register(name, email, password, passwordConfirmation) {
      const { data } = await api.post('/auth/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      })
      this.setSession(data.user, data.token)
    },
    async fetchMe() {
      if (!this.token) return
      const { data } = await api.get('/auth/me')
      this.user = data
    },
    async logout() {
      try {
        await api.post('/auth/logout')
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
      }
    },
    setSession(user, token) {
      this.user = user
      this.token = token
      localStorage.setItem('token', token)
    },
  },
})
