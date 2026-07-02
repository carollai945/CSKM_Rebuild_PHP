import api from '@/api/axios'
export const feeItemsApi = {
  list: () => api.get('/fee-items'),
  create: (d: Record<string,unknown>) => api.post('/fee-items', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/fee-items/${id}`, d),
  delete: (id: number) => api.delete(`/fee-items/${id}`),
}
