import api from '@/api/axios'

interface ApiResponse<T> {
  data: T
}

export interface AuthUser {
  id: number
  name?: string
  email?: string
  role?: string
}

export interface LoginPayload {
  email: string
  password: string
}

export interface LoginResponse {
  token: string
  user: AuthUser
}

export interface ChangePasswordPayload {
  current_password: string
  new_password: string
  new_password_confirmation: string
}

export interface ResetPasswordPayload {
  staff_id: number
  new_password: string
  new_password_confirmation: string
}

export interface MessageResponse {
  message: string
}

export const authApi = {
  login: (payload: LoginPayload) => api.post<ApiResponse<LoginResponse>>('/auth/login', payload),
  logout: () => api.post<ApiResponse<MessageResponse>>('/auth/logout'),
  me: () => api.get<ApiResponse<AuthUser>>('/auth/me'),
  changePassword: (payload: ChangePasswordPayload) =>
    api.post<ApiResponse<MessageResponse>>('/auth/change-password', payload),
  resetPassword: (payload: ResetPasswordPayload) =>
    api.post<ApiResponse<MessageResponse>>('/auth/reset-password', payload),
}
