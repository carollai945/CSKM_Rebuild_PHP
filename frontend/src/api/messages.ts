import api from '@/api/axios'

export function getMessages() {
  return api.get('/messages')
}

export function markMessageRead(id: number | string) {
  return api.patch(`/messages/${id}/read`)
}
