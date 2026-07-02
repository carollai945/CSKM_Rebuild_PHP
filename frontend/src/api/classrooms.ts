import api from '@/api/axios'
export const classroomsApi = {
  list: () => api.get('/classrooms'),
  create: (d: Record<string,unknown>) => api.post('/classrooms', d),
  update: (id: number, d: Record<string,unknown>) => api.put(`/classrooms/${id}`, d),
  delete: (id: number) => api.delete(`/classrooms/${id}`),
}
