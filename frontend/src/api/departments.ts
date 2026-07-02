import api from '@/api/axios'
export const departmentsApi = {
  list: () => api.get('/master/departments'),
  create: (d: Record<string,unknown>) => api.post('/master/departments', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/master/departments/${id}`, d),
  delete: (id: number) => api.delete(`/master/departments/${id}`),
}
export const titlesApi = {
  list: () => api.get('/master/titles'),
  create: (d: Record<string,unknown>) => api.post('/master/titles', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/master/titles/${id}`, d),
  delete: (id: number) => api.delete(`/master/titles/${id}`),
}
