import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'
import { useAuthStore } from '@/stores/auth'

export const usePermissionStore = defineStore('permission', () => {
  const allowedModules = ref<string[]>([])
  const initialized = ref(false)

  function canAccess(module: string) {
    return allowedModules.value.includes(module)
  }

  async function fetchPermissions() {
    const auth = useAuthStore()
    if (!auth.user?.id) {
      allowedModules.value = []
      initialized.value = true
      return
    }
    const { data } = await api.get(`/staff/${auth.user.id}/permissions`)
    const payload = data?.data ?? {}
    const modules = payload.modules ?? payload.allowed_modules ?? payload.allowedModules ?? []
    allowedModules.value = Array.isArray(modules) ? modules : []
    initialized.value = true
  }

  async function ensureLoaded() {
    if (initialized.value) return
    await fetchPermissions()
  }

  function reset() {
    allowedModules.value = []
    initialized.value = false
  }

  return { allowedModules, initialized, canAccess, fetchPermissions, ensureLoaded, reset }
})
