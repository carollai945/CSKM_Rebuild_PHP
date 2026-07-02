import api from '@/api/axios'
export const leaveRequestsApi = {
  list: (p?: Record<string,unknown>) => api.get('/applications/leave-requests', { params: p }),
  get: (id: number) => api.get(`/applications/leave-requests/${id}`),
  create: (d: Record<string,unknown>) => api.post('/applications/leave-requests', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/applications/leave-requests/${id}`, d),
  delete: (id: number) => api.delete(`/applications/leave-requests/${id}`),
}
export const petitionsApi = {
  list: (p?: Record<string,unknown>) => api.get('/applications/petitions', { params: p }),
  get: (id: number) => api.get(`/applications/petitions/${id}`),
  create: (d: Record<string,unknown>) => api.post('/applications/petitions', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/applications/petitions/${id}`, d),
  delete: (id: number) => api.delete(`/applications/petitions/${id}`),
}
export const invoiceRequestsApi = {
  list: (p?: Record<string,unknown>) => api.get('/applications/invoice-requests', { params: p }),
  get: (id: number) => api.get(`/applications/invoice-requests/${id}`),
  create: (d: Record<string,unknown>) => api.post('/applications/invoice-requests', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/applications/invoice-requests/${id}`, d),
  delete: (id: number) => api.delete(`/applications/invoice-requests/${id}`),
}
export const announcementsApi = {
  list: (p?: Record<string,unknown>) => api.get('/applications/announcements', { params: p }),
  get: (id: number) => api.get(`/applications/announcements/${id}`),
  create: (d: Record<string,unknown>) => api.post('/applications/announcements', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/applications/announcements/${id}`, d),
  delete: (id: number) => api.delete(`/applications/announcements/${id}`),
}
