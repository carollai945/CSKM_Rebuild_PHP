<template>
  <div class="page">
    <h2>訊息中心</h2>
    <div class="tabs">
      <button :class="{active:tab==='announcements'}" @click="tab='announcements'">📢 公告</button>
      <button :class="{active:tab==='pending'}" @click="tab='pending'">📋 待辦事項</button>
    </div>

    <div v-if="loading" class="loading">載入中...</div>

    <div v-else-if="tab==='announcements'">
      <div v-if="announcements.length===0" class="empty">目前無公告</div>
      <div v-for="ann in announcements" :key="ann.id" class="ann-card">
        <div class="ann-header">
          <span class="ann-title">{{ ann.title }}</span>
          <span class="ann-date">{{ ann.created_at?.slice(0,10) }}</span>
          <button @click="markRead(ann.id)" class="read-btn">已讀</button>
        </div>
        <div class="ann-content">{{ ann.content }}</div>
      </div>
    </div>

    <div v-else-if="tab==='pending'">
      <div class="pending-grid">
        <RouterLink to="/approvals/leave-requests" class="pending-card">
          <div class="pending-count">{{ pending.leave_requests }}</div>
          <div class="pending-label">待批請假單</div>
        </RouterLink>
        <RouterLink to="/approvals/petitions" class="pending-card">
          <div class="pending-count">{{ pending.petitions }}</div>
          <div class="pending-label">待批簽呈</div>
        </RouterLink>
        <RouterLink to="/approvals/reports" class="pending-card">
          <div class="pending-count">{{ pending.reports }}</div>
          <div class="pending-label">待批報表</div>
        </RouterLink>
        <RouterLink to="/approvals/announcements" class="pending-card">
          <div class="pending-count">{{ pending.invoice_requests }}</div>
          <div class="pending-label">待批請款單</div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '@/api/axios'
const tab = ref('announcements')
const loading = ref(false)
const announcements = ref<Record<string,unknown>[]>([])
const pending = ref({ leave_requests:0, petitions:0, reports:0, invoice_requests:0 })
async function load() {
  loading.value=true
  const r = await api.get('/messages')
  announcements.value = r.data?.data?.announcements ?? []
  pending.value = r.data?.data?.pending_counts ?? pending.value
  loading.value=false
}
async function markRead(id: unknown) {
  await api.patch(`/messages/${id}/read`)
}
onMounted(load)
</script>
<style scoped>
.page{padding:1rem}
.tabs{display:flex;gap:.5rem;margin-bottom:1.5rem}
.tabs button{padding:.5rem 1rem;border:1px solid #d9d9d9;border-radius:4px;cursor:pointer;background:#fff}
.tabs button.active{background:#1890ff;color:#fff;border-color:#1890ff}
.loading{padding:2rem;text-align:center;color:#999}.empty{padding:2rem;text-align:center;color:#999}
.ann-card{background:#fff;border-radius:8px;padding:1rem;margin-bottom:.75rem;border-left:4px solid #1890ff}
.ann-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem}
.ann-title{font-weight:600}.ann-date{color:#999;font-size:.85rem}
.ann-content{color:#666;font-size:.9rem}
.read-btn{padding:.2rem .6rem;background:#f0f0f0;border:none;border-radius:4px;cursor:pointer;color:#666}
.pending-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem}
.pending-card{background:#fff;border-radius:8px;padding:1.5rem;text-align:center;text-decoration:none;color:inherit;border:1px solid #f0f0f0;transition:box-shadow .2s}
.pending-card:hover{box-shadow:0 2px 8px #0002}
.pending-count{font-size:2rem;font-weight:700;color:#1890ff}
.pending-label{color:#666;margin-top:.25rem}
</style>
