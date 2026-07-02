<template>
  <div class="login-page">
    <form class="login-form" @submit.prevent="handleLogin">
      <h1>CSKM 系統登入</h1>
      <div v-if="error" class="error">{{ error }}</div>
      <label>
        電子郵件
        <input v-model="email" type="email" required autocomplete="email" />
      </label>
      <label>
        密碼
        <input v-model="password" type="password" required autocomplete="current-password" />
      </label>
      <button type="submit" :disabled="loading">{{ loading ? '登入中...' : '登入' }}</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    router.push('/')
  } catch {
    error.value = '帳號或密碼錯誤，請重試。'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5; }
.login-form { background: #fff; padding: 2rem; border-radius: 8px; width: 360px; display: flex; flex-direction: column; gap: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
.login-form h1 { margin: 0; font-size: 1.5rem; text-align: center; }
label { display: flex; flex-direction: column; gap: .25rem; font-size: .875rem; }
input { padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 1rem; }
button { padding: .625rem; background: #1677ff; color: #fff; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; }
button:disabled { opacity: .6; cursor: not-allowed; }
.error { color: #ff4d4f; font-size: .875rem; }
</style>
