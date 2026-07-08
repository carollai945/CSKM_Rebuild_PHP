import api from '@/api/axios'

interface ApiResponse<T> {
  data: T
}

export interface Course {
  id: number
  name: string
  institute_id?: number
}

export interface Subject {
  id: number
  name: string
  course_id?: number
}

export interface CoursePayload {
  name: string
  institute_id?: number
}

export interface SubjectPayload {
  name: string
  course_id?: number
}

export const coursesApi = {
  getCourses: () => api.get<ApiResponse<Course[]>>('/courses'),
  createCourse: (payload: CoursePayload) => api.post<ApiResponse<Course>>('/courses', payload),
  updateCourse: (id: number, payload: CoursePayload) => api.put<ApiResponse<Course>>(`/courses/${id}`, payload),
  deleteCourse: (id: number) => api.delete<ApiResponse<null>>(`/courses/${id}`),
  getSubjects: () => api.get<ApiResponse<Subject[]>>('/subjects'),
  createSubject: (payload: SubjectPayload) => api.post<ApiResponse<Subject>>('/subjects', payload),
  updateSubject: (id: number, payload: SubjectPayload) =>
    api.put<ApiResponse<Subject>>(`/subjects/${id}`, payload),
  deleteSubject: (id: number) => api.delete<ApiResponse<null>>(`/subjects/${id}`),
}
