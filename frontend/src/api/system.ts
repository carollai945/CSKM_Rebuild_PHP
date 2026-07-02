import api from '@/api/axios'
export const systemApi = {
  listBackups: () => api.get('/system/backup'),
  createBackup: () => api.post('/system/backup'),
}
