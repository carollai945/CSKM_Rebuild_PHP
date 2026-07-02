import api from '@/api/axios'
export const reportsApi = {
  list: (p?: Record<string,unknown>) => api.get('/reports', { params: p }),
  get: (id: number) => api.get(`/reports/${id}`),
  create: (d: Record<string,unknown>) => api.post('/reports', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/reports/${id}`, d),
  submit: (id: number) => api.post(`/reports/${id}/submit`),
  income: (p?: Record<string,unknown>) => api.get('/reports/income', { params: p }),
}
