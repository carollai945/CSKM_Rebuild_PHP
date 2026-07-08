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
        // Personal
        { path: 'me/personal-data', name: 'personal-data', component: () => import('@/views/PersonalDataView.vue') },
        { path: 'me/change-password', name: 'change-password', component: () => import('@/views/ChangePasswordView.vue') },
        { path: 'me/reports', name: 'my-reports', component: () => import('@/views/MyReportView.vue') },
        // Applications
        { path: 'applications/leave-requests', name: 'leave-requests', meta: { module: 'leave-requests' }, component: () => import('@/views/LeaveRequestView.vue') },
        { path: 'applications/petitions', name: 'petitions', meta: { module: 'petitions' }, component: () => import('@/views/PetitionView.vue') },
        { path: 'applications/invoice-requests', name: 'invoice-requests', meta: { module: 'invoice-requests' }, component: () => import('@/views/InvoiceRequestView.vue') },
        { path: 'applications/announcements', name: 'announcements', meta: { module: 'announcements' }, component: () => import('@/views/AnnouncementView.vue') },
        // Approvals
        { path: 'approvals/leave-requests', name: 'leave-approval', meta: { module: 'leave-approval' }, component: () => import('@/views/LeaveApprovalView.vue') },
        { path: 'approvals/petitions', name: 'petition-approval', meta: { module: 'petition-approval' }, component: () => import('@/views/PetitionApprovalView.vue') },
        { path: 'approvals/announcements', name: 'announcement-approval', meta: { module: 'announcement-approval' }, component: () => import('@/views/AnnouncementApprovalView.vue') },
        { path: 'approvals/reports', name: 'report-approval', meta: { module: 'report-approval' }, component: () => import('@/views/ReportApprovalView.vue') },
        // Student feedbacks
        { path: 'student-feedbacks', name: 'student-feedbacks', meta: { module: 'student-feedbacks' }, component: () => import('@/views/StudentFeedbackView.vue') },
        { path: 'student-feedbacks/:id', name: 'student-feedback-detail', meta: { module: 'student-feedbacks' }, component: () => import('@/views/StudentFeedbackDetailView.vue') },
        // Finance
        { path: 'payments', name: 'payments', meta: { module: 'payments' }, component: () => import('@/views/PaymentView.vue') },
        { path: 'reports/income', name: 'income-report', meta: { module: 'income-report' }, component: () => import('@/views/IncomeReportView.vue') },
        { path: 'reimbursements', name: 'reimbursements', meta: { module: 'reimbursements' }, component: () => import('@/views/ReimbursementView.vue') },
        { path: 'reimbursements/finance-confirm', name: 'finance-confirm', meta: { module: 'finance-confirm' }, component: () => import('@/views/FinanceConfirmView.vue') },
        // Academic
        { path: 'academic/settings', name: 'academic-settings', meta: { module: 'academic-settings' }, component: () => import('@/views/AcademicSettingsView.vue') },
        { path: 'academic/professors', name: 'professors', meta: { module: 'professors' }, component: () => import('@/views/ProfessorView.vue') },
        { path: 'academic/classrooms', name: 'classrooms', meta: { module: 'classrooms' }, component: () => import('@/views/ClassroomView.vue') },
        { path: 'academic/fee-items', name: 'fee-items', meta: { module: 'fee-items' }, component: () => import('@/views/FeeItemView.vue') },
        // Students
        { path: 'students', name: 'students', meta: { module: 'students' }, component: () => import('@/views/StudentView.vue') },
        { path: 'students/assign', name: 'student-assign', meta: { module: 'students' }, component: () => import('@/views/StudentAssignView.vue') },
        { path: 'student-services', name: 'student-services', meta: { module: 'student-services' }, component: () => import('@/views/StudentServiceView.vue') },
        // Leads
        { path: 'leads', name: 'leads', meta: { module: 'leads' }, component: () => import('@/views/LeadView.vue') },
        { path: 'leads/interviews', name: 'interviews', meta: { module: 'leads' }, component: () => import('@/views/InterviewView.vue') },
        { path: 'leads/import', name: 'lead-import', meta: { module: 'leads' }, component: () => import('@/views/LeadImportView.vue') },
        // Staff
        { path: 'staff', name: 'staff-manage', meta: { module: 'staff' }, component: () => import('@/views/StaffManageView.vue') },
        { path: 'staff/list', name: 'staff-list', meta: { module: 'staff' }, component: () => import('@/views/StaffListView.vue') },
        // Master
        { path: 'master/regions', name: 'regions', meta: { module: 'master' }, component: () => import('@/views/RegionView.vue') },
        { path: 'master/departments', name: 'departments', meta: { module: 'master' }, component: () => import('@/views/DepartmentView.vue') },
        // System
        { path: 'system/backup', name: 'system-backup', meta: { module: 'system' }, component: () => import('@/views/SystemBackupView.vue') },
        { path: 'staff/:id/permissions', name: 'staff-permissions', meta: { module: 'staff' }, component: () => import('@/views/StaffPermissionView.vue') },
        { path: 'students/:id', name: 'student-detail', meta: { module: 'students' }, component: () => import('@/views/StudentDetailView.vue') },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return { name: 'login' }
  if (to.name === 'login' && auth.isLoggedIn) return { path: '/' }

  if (auth.isLoggedIn) {
    const permission = usePermissionStore()
    const notification = useNotificationStore()

    if (!auth.user) {
      await auth.fetchMe().catch(() => {})
    }
    if (permission.allowedModules.length === 0 && auth.user) {
      await permission.fetchPermissions().catch(() => {})
      notification.fetchMessages().catch(() => {})
    }

    const requiredModule = to.meta.module as string | undefined
    if (requiredModule && !permission.canAccess(requiredModule)) {
      return { name: 'forbidden' }
    }
  }
})

export default router
