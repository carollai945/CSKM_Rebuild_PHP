import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { usePermissionStore } from '@/stores/permission'

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
        { path: 'messages', name: 'messages', component: () => import('@/views/MessageCenterView.vue'), meta: { module: 'A03' } },
        // Personal
        { path: 'me/personal-data', name: 'personal-data', component: () => import('@/views/PersonalDataView.vue'), meta: { module: 'A00' } },
        { path: 'me/change-password', name: 'change-password', component: () => import('@/views/ChangePasswordView.vue'), meta: { module: 'A01' } },
        { path: 'me/reports', name: 'my-reports', component: () => import('@/views/MyReportView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a020', name: 'a020', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a021', name: 'a021', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a022', name: 'a022', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a023', name: 'a023', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a024/:id', name: 'a024', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a025/:id', name: 'a025', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a026/:id', name: 'a026', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        { path: 'me/reports/a027/:id', name: 'a027', component: () => import('@/views/MyReportEntryView.vue'), meta: { module: 'A02' } },
        // Applications
        { path: 'applications/leave-requests', name: 'leave-requests', component: () => import('@/views/LeaveRequestView.vue'), meta: { module: 'B00' } },
        { path: 'applications/petitions', name: 'petitions', component: () => import('@/views/PetitionView.vue'), meta: { module: 'B01' } },
        { path: 'applications/invoice-requests', name: 'invoice-requests', component: () => import('@/views/InvoiceRequestView.vue'), meta: { module: 'E03' } },
        { path: 'applications/announcements', name: 'announcements', component: () => import('@/views/AnnouncementView.vue'), meta: { module: 'B02' } },
        // Approvals
        { path: 'approvals/leave-requests', name: 'leave-approval', component: () => import('@/views/LeaveApprovalView.vue'), meta: { module: 'D00' } },
        { path: 'approvals/petitions', name: 'petition-approval', component: () => import('@/views/PetitionApprovalView.vue'), meta: { module: 'D01' } },
        { path: 'approvals/announcements', name: 'announcement-approval', component: () => import('@/views/AnnouncementApprovalView.vue'), meta: { module: 'D02' } },
        { path: 'approvals/reports', name: 'report-approval', component: () => import('@/views/ReportApprovalView.vue'), meta: { module: 'D03' } },
        { path: 'approvals/reports/d030/:id', name: 'd030', component: () => import('@/views/ReportApprovalDetailView.vue'), meta: { module: 'D03' } },
        { path: 'approvals/reports/d031/:id', name: 'd031', component: () => import('@/views/ReportApprovalDetailView.vue'), meta: { module: 'D03' } },
        { path: 'approvals/reports/d032/:id', name: 'd032', component: () => import('@/views/ReportApprovalDetailView.vue'), meta: { module: 'D03' } },
        { path: 'approvals/reports/d033/:id', name: 'd033', component: () => import('@/views/ReportApprovalDetailView.vue'), meta: { module: 'D03' } },
        // Student feedbacks
        { path: 'student-feedbacks', name: 'student-feedbacks', component: () => import('@/views/StudentFeedbackView.vue'), meta: { module: 'C05' } },
        { path: 'student-feedbacks/:id', name: 'student-feedback-detail', component: () => import('@/views/StudentFeedbackDetailView.vue'), meta: { module: 'C05' } },
        // Finance
        { path: 'payments', name: 'payments', component: () => import('@/views/PaymentView.vue'), meta: { module: 'E00' } },
        { path: 'reports/income', name: 'income-report', component: () => import('@/views/IncomeReportView.vue'), meta: { module: 'E01' } },
        { path: 'reimbursements', name: 'reimbursements', component: () => import('@/views/ReimbursementView.vue'), meta: { module: 'E02' } },
        { path: 'reimbursements/finance-confirm', name: 'finance-confirm', component: () => import('@/views/FinanceConfirmView.vue'), meta: { module: 'E02' } },
        // Academic
        { path: 'academic/settings', name: 'academic-settings', component: () => import('@/views/AcademicSettingsView.vue'), meta: { module: 'C00' } },
        { path: 'academic/professors', name: 'professors', component: () => import('@/views/ProfessorView.vue'), meta: { module: 'C01' } },
        { path: 'academic/classrooms', name: 'classrooms', component: () => import('@/views/ClassroomView.vue'), meta: { module: 'C00' } },
        { path: 'academic/fee-items', name: 'fee-items', component: () => import('@/views/FeeItemView.vue'), meta: { module: 'C00' } },
        // Students
        { path: 'students', name: 'students', component: () => import('@/views/StudentView.vue'), meta: { module: 'C02' } },
        { path: 'students/assign', name: 'student-assign', component: () => import('@/views/StudentAssignView.vue'), meta: { module: 'C04' } },
        { path: 'student-services', name: 'student-services', component: () => import('@/views/StudentServiceView.vue'), meta: { module: 'C03' } },
        // Leads
        { path: 'leads', name: 'leads', component: () => import('@/views/LeadView.vue'), meta: { module: 'F03' } },
        { path: 'leads/interviews', name: 'interviews', component: () => import('@/views/InterviewView.vue'), meta: { module: 'F03' } },
        { path: 'leads/import', name: 'lead-import', component: () => import('@/views/LeadImportView.vue'), meta: { module: 'F04' } },
        // Staff
        { path: 'staff', name: 'staff-manage', component: () => import('@/views/StaffManageView.vue'), meta: { module: 'F02' } },
        { path: 'staff/list', name: 'staff-list', component: () => import('@/views/StaffListView.vue'), meta: { module: 'F05' } },
        // Master
        { path: 'master/regions', name: 'regions', component: () => import('@/views/RegionView.vue'), meta: { module: 'F01' } },
        { path: 'master/departments', name: 'departments', component: () => import('@/views/DepartmentView.vue'), meta: { module: 'F01' } },
        // System
        { path: 'system/backup', name: 'system-backup', component: () => import('@/views/SystemBackupView.vue'), meta: { module: 'F00' } },
        { path: 'staff/:id/permissions', name: 'staff-permissions', component: () => import('@/views/StaffPermissionView.vue'), meta: { module: 'F02' } },
        { path: 'students/:id', name: 'student-detail', component: () => import('@/views/StudentDetailView.vue'), meta: { module: 'C04' } },
      ],
    },
    {
      path: '/staff/:id/personal-data',
      name: 'staff-personal-data-readonly',
      component: () => import('@/views/PersonalDataView.vue'),
      meta: { requiresAuth: true, module: 'A00' },
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  const permission = usePermissionStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return { name: 'login' }
  if (to.name === 'login' && auth.isLoggedIn) return { path: '/' }
  if (to.meta.requiresAuth && auth.isLoggedIn) {
    await permission.ensureLoaded()
    const moduleCode = typeof to.meta.module === 'string' ? to.meta.module : ''
    if (moduleCode && !permission.canAccess(moduleCode)) {
      return { name: 'forbidden' }
    }
  }
})

export default router
