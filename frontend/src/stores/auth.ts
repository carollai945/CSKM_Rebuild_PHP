import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export interface UserProfile {
  id: number
  name: string
  email: string
  role: string
  department?: string
  region?: string
  photo?: string
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<UserProfile | null>(null)

  const isLoggedIn = computed(() => !!token.value)

  async function login(email: string, password: string) {
    const { data } = await api.post('/auth/login', { email, password })
    token.value = data.token
    user.value = data.user ?? null
    localStorage.setItem('token', data.token)
  }

  async function fetchMe() {
    const { data } = await api.get('/auth/me')
    user.value = data.user ?? data
  }

  async function logout() {
    await api.post('/auth/logout').catch(() => {})
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { token, user, isLoggedIn, login, fetchMe, logout }
})
