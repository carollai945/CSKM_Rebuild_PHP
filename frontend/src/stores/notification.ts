import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export interface Message {
  id: number
  title: string
  body: string
  read: boolean
  createdAt: string
}

export const useNotificationStore = defineStore('notification', () => {
  const messages = ref<Message[]>([])
  const unreadCount = computed(() => messages.value.filter((m) => !m.read).length)
  const unreadMessages = computed(() => messages.value.filter((m) => !m.read))

  async function fetchMessages() {
    const { data } = await api.get('/messages')
    messages.value = data.messages ?? data ?? []
  }

  async function markRead(id: number) {
    await api.patch(`/messages/${id}/read`)
    const msg = messages.value.find((m) => m.id === id)
    if (msg) msg.read = true
  }

  function reset() {
    messages.value = []
  }

  return { messages, unreadCount, unreadMessages, fetchMessages, markRead, reset }
})
