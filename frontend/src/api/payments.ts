import api from '@/api/axios'
export const paymentsApi = {
  list: (p?: Record<string,unknown>) => api.get('/payments', { params: p }),
  get: (id: number) => api.get(`/payments/${id}`),
  create: (d: Record<string,unknown>) => api.post('/payments', d),
  financeConfirm: (id: number) => api.post(`/payments/${id}/finance-confirm`),
  academicConfirm: (id: number) => api.post(`/payments/${id}/academic-confirm`),
  reject: (id: number) => api.post(`/payments/${id}/reject`),
}
