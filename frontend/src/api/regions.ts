import api from '@/api/axios'
export const regionsApi = {
  list: () => api.get('/master/regions'),
  create: (d: Record<string,unknown>) => api.post('/master/regions', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/master/regions/${id}`, d),
  delete: (id: number) => api.delete(`/master/regions/${id}`),
}
