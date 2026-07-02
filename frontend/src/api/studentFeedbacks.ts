import api from '@/api/axios'
export const studentFeedbacksApi = {
  list: (p?: Record<string,unknown>) => api.get('/student-feedbacks', { params: p }),
  get: (id: number) => api.get(`/student-feedbacks/${id}`),
  create: (d: Record<string,unknown>) => api.post('/student-feedbacks', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/student-feedbacks/${id}`, d),
}
