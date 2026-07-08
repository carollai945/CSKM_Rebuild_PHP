import { defineStore } from 'pinia'
import { ref } from 'vue'
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

export const useUserStore = defineStore('user', () => {
  const profile = ref<UserProfile | null>(null)

  async function fetchMe() {
    const { data } = await api.get('/auth/me')
    profile.value = data.data ?? data
  }

  function reset() {
    profile.value = null
  }

  return { profile, fetchMe, reset }
})
