import api from '@/api/axios'
export const leadsApi = {
  list: (p?: Record<string,unknown>) => api.get('/leads', { params: p }),
  create: (d: Record<string,unknown>) => api.post('/leads', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/leads/${id}`, d),
  delete: (id: number) => api.delete(`/leads/${id}`),
  assign: (d: Record<string,unknown>) => api.post('/leads/assign', d),
  import: (file: File) => { const fd = new FormData(); fd.append('file', file); return api.post('/leads/import', fd, { headers: { 'Content-Type': 'multipart/form-data' } }) },
}
