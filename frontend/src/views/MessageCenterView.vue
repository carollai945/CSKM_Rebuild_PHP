<template>
  <div class="page">
    <h2>訊息中心</h2>
    <div class="toolbar">
      <div class="tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          class="tab"
          :class="{ active: activeTab === tab.key }"
          @click="activeTab = tab.key"
        >
          {{ tab.label }}
          <span v-if="tab.count > 0" class="count">{{ tab.count }}</span>
        </button>
      </div>
      <input v-model.trim="keyword" type="text" placeholder="關鍵字搜尋標題或內容" />
    </div>

    <div v-if="loading" class="state">載入中...</div>
    <div v-else-if="filteredMessages.length === 0" class="state">目前沒有訊息</div>

    <div v-else class="message-list">
      <button
        v-for="message in filteredMessages"
        :key="message.key"
        type="button"
        class="message-item"
        :class="{ unread: !isRead(message.key) }"
        @click="markRead(message)"
      >
        <div class="message-header">
          <span class="message-title">{{ message.title }}</span>
          <span class="message-time">{{ message.created_at ? formatTime(message.created_at) : '' }}</span>
        </div>
        <div class="message-content">{{ summary(message.content) }}</div>
        <span class="message-status" :class="{ unread: !isRead(message.key) }">
          {{ isRead(message.key) ? '已讀' : '未讀' }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { getMessages, markMessageRead } from '@/api/messages'

type TabKey = 'announcement' | 'leave' | 'report' | 'petition' | 'invoice'
type MessageListItem = {
  key: string
  category: TabKey
  announcementId?: number
  title: string
  content: string
  created_at?: string
}

const READ_KEY = 'message_read_ids'
const loading = ref(false)
const keyword = ref('')
const activeTab = ref<TabKey>('announcement')
const announcements = ref<MessageListItem[]>([])
const pendingCounts = ref({ leave_requests: 0, reports: 0, petitions: 0, invoice_requests: 0 })
const readIds = ref<Set<string>>(new Set())

function loadReadIds() {
  try {
    const ids = JSON.parse(localStorage.getItem(READ_KEY) ?? '[]')
    readIds.value = new Set(Array.isArray(ids) ? ids.map((id) => String(id)) : [])
  } catch {
    readIds.value = new Set()
  }
}

function saveReadIds() {
  localStorage.setItem(READ_KEY, JSON.stringify([...readIds.value]))
  window.dispatchEvent(new Event('messages-read-updated'))
}

function isRead(id: string) {
  return readIds.value.has(id)
}

function summary(content: string) {
  return content.length > 80 ? `${content.slice(0, 80)}...` : content
}

function formatTime(createdAt?: string) {
  return createdAt?.slice(0, 16).replace('T', ' ') ?? ''
}

async function load() {
  loading.value = true
  loadReadIds()
  const response = await getMessages()
  const payload = response.data?.data ?? {}
  const rows = payload.announcements ?? []
  announcements.value = rows.map((row: Record<string, unknown>) => ({
    key: `announcement-${String(row.id ?? '')}`,
    category: 'announcement',
    announcementId: Number(row.id),
    title: String(row.title ?? ''),
    content: String(row.content ?? ''),
    created_at: typeof row.created_at === 'string' ? row.created_at : undefined,
  }))
  pendingCounts.value = {
    leave_requests: Number(payload.pending_counts?.leave_requests ?? 0),
    reports: Number(payload.pending_counts?.reports ?? 0),
    petitions: Number(payload.pending_counts?.petitions ?? 0),
    invoice_requests: Number(payload.pending_counts?.invoice_requests ?? 0),
  }
  loading.value = false
}

async function markRead(message: MessageListItem) {
  if (isRead(message.key)) return
  if (message.announcementId) {
    await markMessageRead(message.announcementId)
    readIds.value.add(String(message.announcementId))
  }
  readIds.value.add(message.key)
  saveReadIds()
}

const tabs = computed(() => [
  { key: 'announcement' as TabKey, label: '公告', count: announcements.value.length },
  { key: 'leave' as TabKey, label: '假單', count: pendingCounts.value.leave_requests },
  { key: 'report' as TabKey, label: '報表', count: pendingCounts.value.reports },
  { key: 'petition' as TabKey, label: '簽呈', count: pendingCounts.value.petitions },
  { key: 'invoice' as TabKey, label: '請款', count: pendingCounts.value.invoice_requests },
])

const categoryMessages = computed<MessageListItem[]>(() => {
  if (activeTab.value === 'announcement') return announcements.value
  if (activeTab.value === 'leave') {
    return [{
      key: 'pending-leave',
      category: 'leave',
      title: '假單待處理通知',
      content: `目前待處理假單共 ${pendingCounts.value.leave_requests} 筆。`,
    }]
  }
  if (activeTab.value === 'report') {
    return [{
      key: 'pending-report',
      category: 'report',
      title: '報表待審通知',
      content: `目前待審報表共 ${pendingCounts.value.reports} 筆。`,
    }]
  }
  if (activeTab.value === 'petition') {
    return [{
      key: 'pending-petition',
      category: 'petition',
      title: '簽呈待審通知',
      content: `目前待審簽呈共 ${pendingCounts.value.petitions} 筆。`,
    }]
  }
  return [{
    key: 'pending-invoice',
    category: 'invoice',
    title: '請款待審通知',
    content: `目前待審請款共 ${pendingCounts.value.invoice_requests} 筆。`,
  }]
})

const filteredMessages = computed(() => {
  const query = keyword.value.toLowerCase()
  if (!query) return categoryMessages.value
  return categoryMessages.value.filter((message) =>
    message.title.toLowerCase().includes(query) || message.content.toLowerCase().includes(query),
  )
})

onMounted(load)
</script>

<style scoped>
.page { padding: 1rem; }
.state { color: #8c8c8c; padding: 1rem 0; }
.toolbar { display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: 1rem; }
.tabs { display: flex; gap: .5rem; flex-wrap: wrap; }
.tab { border: 1px solid #d9d9d9; background: #fff; color: #333; border-radius: 999px; padding: .35rem .75rem; cursor: pointer; }
.tab.active { border-color: #1890ff; color: #1890ff; }
.count { margin-left: .25rem; color: #8c8c8c; font-size: .75rem; }
.toolbar input { border: 1px solid #d9d9d9; border-radius: 8px; padding: .45rem .65rem; min-width: 260px; }
.message-list { display: grid; gap: .75rem; }
.message-item {
  width: 100%;
  text-align: left;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  background: #fff;
  padding: 1rem;
  cursor: pointer;
}
.message-item.unread { border-color: #1890ff; }
.message-header { display: flex; justify-content: space-between; gap: 1rem; }
.message-title { font-weight: 500; color: #1f1f1f; }
.message-item.unread .message-title { font-weight: 700; }
.message-time { color: #8c8c8c; font-size: .85rem; }
.message-content { color: #595959; margin-top: .5rem; }
.message-status {
  display: inline-block;
  margin-top: .5rem;
  font-size: .75rem;
  color: #8c8c8c;
}
.message-status.unread {
  color: #1890ff;
  font-weight: 700;
}
</style>
