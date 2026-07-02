import api from '@/api/axios'
export const institutesApi = {
  list: () => api.get('/institutes'),
  create: (d: Record<string,unknown>) => api.post('/institutes', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/institutes/${id}`, d),
  delete: (id: number) => api.delete(`/institutes/${id}`),
}
export const coursesApi = {
  list: () => api.get('/courses'),
  create: (d: Record<string,unknown>) => api.post('/courses', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/courses/${id}`, d),
  delete: (id: number) => api.delete(`/courses/${id}`),
}
export const subjectsApi = {
  list: () => api.get('/subjects'),
  create: (d: Record<string,unknown>) => api.post('/subjects', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/subjects/${id}`, d),
  delete: (id: number) => api.delete(`/subjects/${id}`),
}
