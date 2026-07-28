<template>
  <div class="auth-form">
    <h1>登入</h1>
    <form @submit.prevent="handleSubmit">
      <label>Email <input v-model="email" type="email" required /></label>
      <label>密碼 <input v-model="password" type="password" required /></label>
      <p v-if="error" class="error">{{ error }}</p>
      <button type="submit">登入</button>
    </form>
    <p>還沒有帳號？<RouterLink to="/register">立即註冊</RouterLink></p>
    <p class="hint">測試帳號：admin@example.com / customer@example.com，密碼皆為 password</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')

async function handleSubmit() {
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || '登入失敗'
  }
}
</script>

<style scoped>
.auth-form {
  max-width: 360px;
  margin: 0 auto;
}
form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
label {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 14px;
}
input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}
button {
  padding: 10px;
  background: #111827;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.error {
  color: #dc2626;
}
.hint {
  font-size: 12px;
  color: #888;
  margin-top: 12px;
}
</style>
