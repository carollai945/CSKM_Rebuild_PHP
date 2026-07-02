import api from '@/api/axios'
export const interviewsApi = {
  list: (p?: Record<string,unknown>) => api.get('/interviews', { params: p }),
  get: (id: number) => api.get(`/interviews/${id}`),
  create: (d: Record<string,unknown>) => api.post('/interviews', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/interviews/${id}`, d),
  delete: (id: number) => api.delete(`/interviews/${id}`),
}
