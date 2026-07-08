<template>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-title">CSKM 管理系統</div>
      <nav>
        <RouterLink to="/dashboard">📊 儀表板</RouterLink>
        <div class="nav-group">個人</div>
        <RouterLink to="/me/personal-data">👤 個人資料</RouterLink>
        <RouterLink to="/me/change-password">🔑 修改密碼</RouterLink>
        <RouterLink to="/me/reports">📋 個人報表</RouterLink>
        <template v-if="canAccess('leave-requests') || canAccess('petitions') || canAccess('invoice-requests') || canAccess('announcements')">
          <div class="nav-group">申請單</div>
          <RouterLink v-if="canAccess('leave-requests')" to="/applications/leave-requests">🏖 請假申請</RouterLink>
          <RouterLink v-if="canAccess('petitions')" to="/applications/petitions">📝 簽呈申請</RouterLink>
          <RouterLink v-if="canAccess('invoice-requests')" to="/applications/invoice-requests">💳 請款單</RouterLink>
          <RouterLink v-if="canAccess('announcements')" to="/applications/announcements">📢 公告管理</RouterLink>
        </template>
        <template v-if="canAccess('leave-approval') || canAccess('petition-approval') || canAccess('announcement-approval') || canAccess('report-approval')">
          <div class="nav-group">審核</div>
          <RouterLink v-if="canAccess('leave-approval')" to="/approvals/leave-requests">✅ 請假批核</RouterLink>
          <RouterLink v-if="canAccess('petition-approval')" to="/approvals/petitions">✅ 簽呈批核</RouterLink>
          <RouterLink v-if="canAccess('announcement-approval')" to="/approvals/announcements">✅ 公告批核</RouterLink>
          <RouterLink v-if="canAccess('report-approval')" to="/approvals/reports">✅ 報表批核</RouterLink>
        </template>
        <template v-if="canAccess('students') || canAccess('student-services') || canAccess('student-feedbacks')">
          <div class="nav-group">學員</div>
          <RouterLink v-if="canAccess('students')" to="/students">🎓 學員管理</RouterLink>
          <RouterLink v-if="canAccess('students')" to="/students/assign">📌 學員分配</RouterLink>
          <RouterLink v-if="canAccess('student-services')" to="/student-services">🛎 服務紀錄</RouterLink>
          <RouterLink v-if="canAccess('student-feedbacks')" to="/student-feedbacks">💬 學員意見</RouterLink>
        </template>
        <template v-if="canAccess('leads')">
          <div class="nav-group">電訪</div>
          <RouterLink to="/leads">📞 電訪名單</RouterLink>
          <RouterLink to="/leads/interviews">🗒 電訪紀錄</RouterLink>
          <RouterLink to="/leads/import">📥 名單匯入</RouterLink>
        </template>
        <template v-if="canAccess('payments') || canAccess('income-report') || canAccess('reimbursements') || canAccess('finance-confirm')">
          <div class="nav-group">財務</div>
          <RouterLink v-if="canAccess('payments')" to="/payments">💰 繳費記錄</RouterLink>
          <RouterLink v-if="canAccess('income-report')" to="/reports/income">📈 收入報表</RouterLink>
          <RouterLink v-if="canAccess('reimbursements')" to="/reimbursements">🧾 請款列表</RouterLink>
          <RouterLink v-if="canAccess('finance-confirm')" to="/reimbursements/finance-confirm">🏦 財務確認</RouterLink>
        </template>
        <template v-if="canAccess('academic-settings') || canAccess('professors') || canAccess('classrooms') || canAccess('fee-items')">
          <div class="nav-group">學術</div>
          <RouterLink v-if="canAccess('academic-settings')" to="/academic/settings">⚙️ 課程設定</RouterLink>
          <RouterLink v-if="canAccess('professors')" to="/academic/professors">👩‍🏫 師資管理</RouterLink>
          <RouterLink v-if="canAccess('classrooms')" to="/academic/classrooms">🏫 教室管理</RouterLink>
          <RouterLink v-if="canAccess('fee-items')" to="/academic/fee-items">💵 費用項目</RouterLink>
        </template>
        <template v-if="canAccess('staff')">
          <div class="nav-group">人員</div>
          <RouterLink to="/staff">👥 人員管理</RouterLink>
          <RouterLink to="/staff/list">📄 人員列表</RouterLink>
          <RouterLink to="/staff">🔐 權限管理</RouterLink>
        </template>
        <template v-if="canAccess('master')">
          <div class="nav-group">基本設定</div>
          <RouterLink to="/master/regions">🌏 區域管理</RouterLink>
          <RouterLink to="/master/departments">🏢 部門職稱</RouterLink>
        </template>
        <template v-if="canAccess('system')">
          <div class="nav-group">系統</div>
          <RouterLink to="/system/backup">💾 資料庫備份</RouterLink>
        </template>
        <RouterLink to="/messages" class="message-link">
          <span>📨 訊息中心</span>
          <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
        </RouterLink>
        <a href="#" @click.prevent="logout">🚪 登出</a>
      </nav>
    </aside>
    <main class="content">
      <div class="topbar">
        <span class="user-info">{{ auth.user?.name ?? '' }}</span>
        <RouterLink to="/me/personal-data" class="notification-bell">
          🔔
          <span v-if="notification.unreadCount > 0" class="badge">{{ notification.unreadCount }}</span>
        </RouterLink>
      </div>
      <RouterView />
    </main>
  </div>
</template>


<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getMessages } from '@/api/messages'
import { usePermissionStore } from '@/stores/permission'
import { useNotificationStore } from '@/stores/notification'
import { useRouter } from 'vue-router'

const READ_KEY = 'message_read_ids'
const auth = useAuthStore()
const permission = usePermissionStore()
const notification = useNotificationStore()
const router = useRouter()
const canAccess = (module: string) => permission.canAccess(module)
const unreadCount = ref(0)

function getReadIds() {
  try {
    const ids = JSON.parse(localStorage.getItem(READ_KEY) ?? '[]')
    return new Set(Array.isArray(ids) ? ids.map((id) => Number(id)) : [])
  } catch {
    return new Set<number>()
  }
}

async function loadUnreadCount() {
  const response = await getMessages()
  const rows = response.data?.data?.announcements ?? []
  const readIds = getReadIds()
  unreadCount.value = rows.filter((row: Record<string, unknown>) => !readIds.has(Number(row.id))).length
}

function onReadUpdated() {
  loadUnreadCount()
}

async function logout() {
  permission.reset()
  notification.reset()
  await auth.logout()
  router.push('/login')
}

onMounted(() => {
  loadUnreadCount()
  window.addEventListener('messages-read-updated', onReadUpdated)
})

onBeforeUnmount(() => {
  window.removeEventListener('messages-read-updated', onReadUpdated)
})
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar { width: 220px; background: #001529; color: #fff; padding: 1rem; overflow-y: auto; }
.sidebar-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 1rem; padding: .5rem 0; border-bottom: 1px solid #ffffff20; }
.sidebar a { display: block; color: #ffffffa0; padding: .35rem .5rem; text-decoration: none; font-size: .875rem; }
.sidebar a:hover, .sidebar a.router-link-active { color: #fff; background: #ffffff15; border-radius: 4px; }
.message-link { display: flex !important; justify-content: space-between; align-items: center; }
.badge { background: #ff4d4f; color: #fff; border-radius: 999px; font-size: .75rem; padding: 0 .4rem; min-width: 1.25rem; text-align: center; }
.nav-group { color: #ffffff50; font-size: .7rem; text-transform: uppercase; margin: .75rem 0 .25rem; padding: 0 .5rem; }
.content { flex: 1; padding: 1.5rem; background: #f0f2f5; overflow-y: auto; }
</style>
