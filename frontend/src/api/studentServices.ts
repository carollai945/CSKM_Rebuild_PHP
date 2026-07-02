import api from '@/api/axios'
export const studentServicesApi = {
  list: (p?: Record<string,unknown>) => api.get('/student-services', { params: p }),
  get: (id: number) => api.get(`/student-services/${id}`),
  create: (d: Record<string,unknown>) => api.post('/student-services', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/student-services/${id}`, d),
  delete: (id: number) => api.delete(`/student-services/${id}`),
}
