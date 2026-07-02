import api from '@/api/axios'
export const reimbursementsApi = {
  list: (p?: Record<string,unknown>) => api.get('/reimbursements', { params: p }),
  get: (id: number) => api.get(`/reimbursements/${id}`),
  create: (d: Record<string,unknown>) => api.post('/reimbursements', d),
  financeConfirm: (id: number) => api.post(`/reimbursements/${id}/finance-confirm`),
  reject: (id: number, reason?: string) => api.post(`/reimbursements/${id}/reject`, { reject_reason: reason }),
}
