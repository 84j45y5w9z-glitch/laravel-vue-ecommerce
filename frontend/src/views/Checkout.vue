<template>
  <div class="checkout">
    <h1>結帳</h1>
    <form @submit.prevent="handleSubmit">
      <label>
        收件人姓名
        <input v-model="form.receiver_name" required />
      </label>
      <label>
        聯絡電話
        <input v-model="form.receiver_phone" required />
      </label>
      <label>
        收件地址
        <input v-model="form.shipping_address" required />
      </label>

      <label>
        付款方式
        <select v-model="gateway">
          <option value="ecpay">綠界 ECPay</option>
          <option value="newebpay">藍新 NewebPay</option>
        </select>
      </label>

      <p class="total">應付金額：NT$ {{ cart.totalAmount }}</p>
      <p v-if="error" class="error">{{ error }}</p>
      <button type="submit" :disabled="submitting">{{ submitting ? '處理中...' : '送出訂單並前往付款' }}</button>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useCartStore } from '../stores/cart'
import api from '../api/axios'

const cart = useCartStore()
const submitting = ref(false)
const error = ref('')
const gateway = ref('ecpay')

const form = reactive({
  receiver_name: '',
  receiver_phone: '',
  shipping_address: '',
})

onMounted(() => cart.fetchCart())

async function handleSubmit() {
  submitting.value = true
  error.value = ''
  try {
    const { data: order } = await api.post('/orders', form)
    const { data: paymentForm } = await api.post(`/orders/${order.data.id}/pay`, { gateway: gateway.value })

    // 動態組表單並自動送出，導向金流付款頁
    const formEl = document.createElement('form')
    formEl.method = paymentForm.method
    formEl.action = paymentForm.action
    for (const [key, value] of Object.entries(paymentForm.fields)) {
      const input = document.createElement('input')
      input.type = 'hidden'
      input.name = key
      input.value = value
      formEl.appendChild(input)
    }
    document.body.appendChild(formEl)
    formEl.submit()
  } catch (e) {
    error.value = e.response?.data?.message || '建立訂單失敗'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.checkout {
  max-width: 480px;
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
  color: #444;
}
input, select {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}
.total {
  font-size: 18px;
  font-weight: bold;
}
button {
  padding: 12px;
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
