import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<Record<string, unknown> | null>(
    JSON.parse(localStorage.getItem('auth_user') ?? 'null')
  )

  const isLoggedIn = computed(() => !!token.value)

  async function login(email: string, password: string) {
    const { data } = await api.post('/auth/login', { email, password })
    token.value = data.token
    user.value = data.user ?? null
    localStorage.setItem('token', data.token)
    localStorage.setItem('auth_user', JSON.stringify(user.value))
  }

  async function logout() {
    await api.post('/auth/logout').catch(() => {})
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('auth_user')
  }

  return { token, user, isLoggedIn, login, logout }
})
