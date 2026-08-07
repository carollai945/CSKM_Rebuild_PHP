import api from '@/api/axios'
export const personalDataApi = {
  get: () => api.get('/me/personal-data'),
  update: (d: Record<string,unknown>) => api.put('/me/personal-data', d),
  changePassword: (d: Record<string,unknown>) => api.post('/me/change-password', d),
  uploadPhoto: (file: File) => {
    const fd = new FormData()
    fd.append('photo', file)
    return api.post('/me/personal-data/photo', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },
}
