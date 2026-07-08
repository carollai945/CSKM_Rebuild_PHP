import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { usePermissionStore } from '@/stores/permission'
import { useNotificationStore } from '@/stores/notification'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue') },
    { path: '/403', name: 'forbidden', component: () => import('@/views/ForbiddenView.vue') },
    {
      path: '/',
      component: () => import('@/layouts/DefaultLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: '/dashboard' },
        { path: 'dashboard', name: 'dashboard', component: () => import('@/views/DashboardView.vue') },
        { path: 'messages', name: 'messages', component: () => import('@/views/MessageCenterView.vue') },
        // Personal
        { path: 'me/personal-data', name: 'personal-data', component: () => import('@/views/PersonalDataView.vue') },
        { path: 'me/change-password', name: 'change-password', component: () => import('@/views/ChangePasswordView.vue') },
        { path: 'me/reports', name: 'my-reports', component: () => import('@/views/MyReportView.vue') },
        // Applications
        { path: 'applications/leave-requests', name: 'leave-requests', component: () => import('@/views/LeaveRequestView.vue') },
        { path: 'applications/petitions', name: 'petitions', component: () => import('@/views/PetitionView.vue') },
        { path: 'applications/invoice-requests', name: 'invoice-requests', component: () => import('@/views/InvoiceRequestView.vue') },
        { path: 'applications/announcements', name: 'announcements', component: () => import('@/views/AnnouncementView.vue') },
        // Approvals
        { path: 'approvals/leave-requests', name: 'leave-approval', component: () => import('@/views/LeaveApprovalView.vue') },
        { path: 'approvals/petitions', name: 'petition-approval', component: () => import('@/views/PetitionApprovalView.vue') },
        { path: 'approvals/announcements', name: 'announcement-approval', component: () => import('@/views/AnnouncementApprovalView.vue') },
        { path: 'approvals/reports', name: 'report-approval', component: () => import('@/views/ReportApprovalView.vue') },
        // Student feedbacks
        { path: 'student-feedbacks', name: 'student-feedbacks', component: () => import('@/views/StudentFeedbackView.vue') },
        { path: 'student-feedbacks/:id', name: 'student-feedback-detail', component: () => import('@/views/StudentFeedbackDetailView.vue') },
        // Finance
        { path: 'payments', name: 'payments', component: () => import('@/views/PaymentView.vue') },
        { path: 'reports/income', name: 'income-report', component: () => import('@/views/IncomeReportView.vue') },
        { path: 'reimbursements', name: 'reimbursements', component: () => import('@/views/ReimbursementView.vue') },
        { path: 'reimbursements/finance-confirm', name: 'finance-confirm', component: () => import('@/views/FinanceConfirmView.vue') },
        // Academic
        { path: 'academic/settings', name: 'academic-settings', component: () => import('@/views/AcademicSettingsView.vue') },
        { path: 'academic/professors', name: 'professors', component: () => import('@/views/ProfessorView.vue') },
        { path: 'academic/classrooms', name: 'classrooms', component: () => import('@/views/ClassroomView.vue') },
        { path: 'academic/fee-items', name: 'fee-items', component: () => import('@/views/FeeItemView.vue') },
        // Students
        { path: 'students', name: 'students', component: () => import('@/views/StudentView.vue') },
        { path: 'students/assign', name: 'student-assign', component: () => import('@/views/StudentAssignView.vue') },
        { path: 'student-services', name: 'student-services', component: () => import('@/views/StudentServiceView.vue') },
        // Leads
        { path: 'leads', name: 'leads', component: () => import('@/views/LeadView.vue') },
        { path: 'leads/interviews', name: 'interviews', component: () => import('@/views/InterviewView.vue') },
        { path: 'leads/import', name: 'lead-import', component: () => import('@/views/LeadImportView.vue') },
        // Staff
        { path: 'staff', name: 'staff-manage', component: () => import('@/views/StaffManageView.vue') },
        { path: 'staff/list', name: 'staff-list', component: () => import('@/views/StaffListView.vue') },
        // Master
        { path: 'master/regions', name: 'regions', component: () => import('@/views/RegionView.vue') },
        { path: 'master/departments', name: 'departments', component: () => import('@/views/DepartmentView.vue') },
        // System
        { path: 'system/backup', name: 'system-backup', component: () => import('@/views/SystemBackupView.vue') },
        { path: 'staff/:id/permissions', name: 'staff-permissions', component: () => import('@/views/StaffPermissionView.vue') },
        { path: 'students/:id', name: 'student-detail', component: () => import('@/views/StudentDetailView.vue') },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return { name: 'login' }
  if (to.name === 'login' && auth.isLoggedIn) return { path: '/' }
})

export default router
