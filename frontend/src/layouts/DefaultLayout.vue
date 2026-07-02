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
        <div class="nav-group">申請單</div>
        <RouterLink to="/applications/leave-requests">🏖 請假申請</RouterLink>
        <RouterLink to="/applications/petitions">📝 簽呈申請</RouterLink>
        <RouterLink to="/applications/invoice-requests">💳 請款單</RouterLink>
        <RouterLink to="/applications/announcements">📢 公告管理</RouterLink>
        <div class="nav-group">審核</div>
        <RouterLink to="/approvals/leave-requests">✅ 請假批核</RouterLink>
        <RouterLink to="/approvals/petitions">✅ 簽呈批核</RouterLink>
        <RouterLink to="/approvals/announcements">✅ 公告批核</RouterLink>
        <RouterLink to="/approvals/reports">✅ 報表批核</RouterLink>
        <div class="nav-group">學員</div>
        <RouterLink to="/students">🎓 學員管理</RouterLink>
        <RouterLink to="/students/assign">📌 學員分配</RouterLink>
        <RouterLink to="/student-services">🛎 服務紀錄</RouterLink>
        <RouterLink to="/student-feedbacks">💬 學員意見</RouterLink>
        <div class="nav-group">電訪</div>
        <RouterLink to="/leads">📞 電訪名單</RouterLink>
        <RouterLink to="/leads/interviews">🗒 電訪紀錄</RouterLink>
        <RouterLink to="/leads/import">📥 名單匯入</RouterLink>
        <div class="nav-group">財務</div>
        <RouterLink to="/payments">💰 繳費記錄</RouterLink>
        <RouterLink to="/reports/income">📈 收入報表</RouterLink>
        <RouterLink to="/reimbursements">🧾 請款列表</RouterLink>
        <RouterLink to="/reimbursements/finance-confirm">🏦 財務確認</RouterLink>
        <div class="nav-group">學術</div>
        <RouterLink to="/academic/settings">⚙️ 課程設定</RouterLink>
        <RouterLink to="/academic/professors">👩‍🏫 師資管理</RouterLink>
        <RouterLink to="/academic/classrooms">🏫 教室管理</RouterLink>
        <RouterLink to="/academic/fee-items">💵 費用項目</RouterLink>
        <div class="nav-group">人員</div>
        <RouterLink to="/staff">👥 人員管理</RouterLink>
        <RouterLink to="/staff/list">📄 人員列表</RouterLink>
        <RouterLink to="/staff">🔐 權限管理</RouterLink>
        <div class="nav-group">基本設定</div>
        <RouterLink to="/master/regions">🌏 區域管理</RouterLink>
        <RouterLink to="/master/departments">🏢 部門職稱</RouterLink>
        <div class="nav-group">系統</div>
        <RouterLink to="/system/backup">💾 資料庫備份</RouterLink>
        <a href="#" @click.prevent="logout">🚪 登出</a>
      </nav>
    </aside>
    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
const auth = useAuthStore()
const router = useRouter()
async function logout() { await auth.logout(); router.push('/login') }
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar { width: 220px; background: #001529; color: #fff; padding: 1rem; overflow-y: auto; }
.sidebar-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 1rem; padding: .5rem 0; border-bottom: 1px solid #ffffff20; }
.sidebar a { display: block; color: #ffffffa0; padding: .35rem .5rem; text-decoration: none; font-size: .875rem; }
.sidebar a:hover, .sidebar a.router-link-active { color: #fff; background: #ffffff15; border-radius: 4px; }
.nav-group { color: #ffffff50; font-size: .7rem; text-transform: uppercase; margin: .75rem 0 .25rem; padding: 0 .5rem; }
.content { flex: 1; padding: 1.5rem; background: #f0f2f5; overflow-y: auto; }
</style>
