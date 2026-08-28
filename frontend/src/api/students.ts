import api from '@/api/axios'
export const studentsApi = {
  list: (p?: Record<string,unknown>) => api.get('/students', { params: p }),
  get: (id: number) => api.get(`/students/${id}`),
  create: (d: Record<string,unknown>) => api.post('/students', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/students/${id}`, d),
  delete: (id: number) => api.delete(`/students/${id}`),
  assign: (d: Record<string,unknown>) => api.post('/students/assign', d),
  getCourses: (id: number) => api.get(`/students/${id}/courses`),
  updateCourses: (id: number, d: Record<string,unknown>) => api.put(`/students/${id}/courses`, d),
  export: (p?: Record<string,unknown>) => api.get('/students/export', { params: p, responseType: 'blob' }),
}
