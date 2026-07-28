<template>
  <div class="auth-form">
    <h1>註冊</h1>
    <form @submit.prevent="handleSubmit">
      <label>姓名 <input v-model="name" required /></label>
      <label>Email <input v-model="email" type="email" required /></label>
      <label>密碼 <input v-model="password" type="password" required /></label>
      <label>確認密碼 <input v-model="passwordConfirmation" type="password" required /></label>
      <p v-if="error" class="error">{{ error }}</p>
      <button type="submit">註冊</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')

async function handleSubmit() {
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || '註冊失敗'
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
</style>
