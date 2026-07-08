import api from '@/api/axios'

interface ApiResponse<T> {
  data: T
}

export interface PermissionRole {
  value: string
  label: string
}

export interface PermissionGroups {
  roles: PermissionRole[]
  modules: string[]
}

export interface StaffPermissions {
  staff_name?: string
  role: string
  modules: string[]
}

export interface UpdateStaffPermissionsPayload {
  role: string
  modules: string[]
}

export interface PermissionUpdateResponse {
  message: string
}

export const permissionsApi = {
  getPermissionGroups: () => api.get<ApiResponse<PermissionGroups>>('/permission-groups'),
  getStaffPermissions: (staffId: number) => api.get<ApiResponse<StaffPermissions>>(`/staff/${staffId}/permissions`),
  updateStaffPermissions: (staffId: number, payload: UpdateStaffPermissionsPayload) =>
    api.put<ApiResponse<PermissionUpdateResponse>>(`/staff/${staffId}/permissions`, payload),
}
