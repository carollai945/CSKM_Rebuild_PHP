import api from '@/api/axios'
export const staffApi = {
  list: (p?: Record<string,unknown>) => api.get('/staff', { params: p }),
  get: (id: number) => api.get(`/staff/${id}`),
  create: (d: Record<string,unknown>) => api.post('/staff', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/staff/${id}`, d),
  updateStatus: (id: number, status: string) => api.patch(`/staff/${id}/status`, { status }),
  overview: (p?: Record<string,unknown>) => api.get('/staff/overview', { params: p }),
  autocomplete: (p?: Record<string,unknown>) => api.get('/staff/autocomplete', { params: p }),
}
