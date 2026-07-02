import api from '@/api/axios'
export const approvalsApi = {
  pendingLeave: () => api.get('/approvals/leave-requests/pending'),
  approveLeave: (id: number) => api.post(`/approvals/leave-requests/${id}/approve`),
  rejectLeave: (id: number, reason?: string) => api.post(`/approvals/leave-requests/${id}/reject`, { reject_reason: reason }),
  pendingPetition: () => api.get('/approvals/petitions/pending'),
  approvePetition: (id: number) => api.post(`/approvals/petitions/${id}/approve`),
  rejectPetition: (id: number) => api.post(`/approvals/petitions/${id}/reject`),
  pendingAnnouncement: () => api.get('/approvals/announcements/pending'),
  approveAnnouncement: (id: number) => api.post(`/approvals/announcements/${id}/approve`),
  rejectAnnouncement: (id: number) => api.post(`/approvals/announcements/${id}/reject`),
  pendingReport: () => api.get('/approvals/reports/pending'),
  approveReport: (id: number) => api.post(`/approvals/reports/${id}/approve`),
  rejectReport: (id: number) => api.post(`/approvals/reports/${id}/reject`),
}
