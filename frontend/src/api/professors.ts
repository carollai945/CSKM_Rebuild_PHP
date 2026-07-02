import api from '@/api/axios'
export const professorsApi = {
  list: (p?: Record<string,unknown>) => api.get('/professors', { params: p }),
  get: (id: number) => api.get(`/professors/${id}`),
  create: (d: Record<string,unknown>) => api.post('/professors', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/professors/${id}`, d),
  delete: (id: number) => api.delete(`/professors/${id}`),
}
