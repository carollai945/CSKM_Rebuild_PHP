import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'

export const usePermissionStore = defineStore('permission', () => {
  const allowedModules = ref<string[]>([])

  const canAccess = computed(() => (module: string) => allowedModules.value.includes(module))

  async function fetchPermissions() {
    const auth = useAuthStore()
    if (!auth.user?.id) return
    const { data } = await api.get(`/staff/${auth.user.id}/permissions`)
    allowedModules.value = data.allowed_modules ?? data.allowedModules ?? []
  }

  function reset() {
    allowedModules.value = []
  }

  return { allowedModules, canAccess, fetchPermissions, reset }
})
