import api from '@/api/axios'

interface ApiResponse<T> {
  data: T
}

export interface MessageItem {
  id: number
  title: string
  content: string
  created_at?: string
  read_at?: string | null
}

export interface PendingCounts {
  leave_requests: number
  petitions: number
  reports: number
  invoice_requests: number
}

export interface MessagesResponse {
  announcements: MessageItem[]
  pending_counts: PendingCounts
}

export interface MessageStatusResponse {
  message: string
}

export const messagesApi = {
  getMessages: () => api.get<ApiResponse<MessagesResponse>>('/messages'),
  markRead: (id: number) => api.patch<ApiResponse<MessageStatusResponse>>(`/messages/${id}/read`),
}
