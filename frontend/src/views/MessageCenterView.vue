<template>
  <div class="page">
    <h2>訊息中心</h2>

    <div v-if="loading" class="state">載入中...</div>
    <div v-else-if="messages.length === 0" class="state">目前沒有訊息</div>

    <div v-else class="message-list">
      <button
        v-for="message in messages"
        :key="message.id"
        type="button"
        class="message-item"
        :class="{ unread: !isRead(message.id) }"
        @click="read(message.id)"
      >
        <div class="message-header">
          <span class="message-title">{{ message.title }}</span>
          <span class="message-time">{{ formatTime(message.created_at) }}</span>
        </div>
        <div class="message-content">{{ summary(message.content) }}</div>
        <span class="message-status" :class="{ unread: !isRead(message.id) }">
          {{ isRead(message.id) ? '已讀' : '未讀' }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getMessages, markMessageRead } from '@/api/messages'

type MessageItem = {
  id: number
  title: string
  content: string
  created_at?: string
}

const READ_KEY = 'message_read_ids'
const loading = ref(false)
const messages = ref<MessageItem[]>([])
const readIds = ref<Set<number>>(new Set())

function loadReadIds() {
  try {
    const ids = JSON.parse(localStorage.getItem(READ_KEY) ?? '[]')
    readIds.value = new Set(Array.isArray(ids) ? ids.map((id) => Number(id)) : [])
  } catch {
    readIds.value = new Set()
  }
}

function saveReadIds() {
  localStorage.setItem(READ_KEY, JSON.stringify([...readIds.value]))
  window.dispatchEvent(new Event('messages-read-updated'))
}

function isRead(id: number) {
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
  const rows = response.data?.data?.announcements ?? []
  messages.value = rows.map((row: Record<string, unknown>) => ({
    id: Number(row.id),
    title: String(row.title ?? ''),
    content: String(row.content ?? ''),
    created_at: typeof row.created_at === 'string' ? row.created_at : undefined,
  }))
  loading.value = false
}

async function read(id: number) {
  if (isRead(id)) return
  await markMessageRead(id)
  readIds.value.add(id)
  saveReadIds()
}

onMounted(load)
</script>

<style scoped>
.page { padding: 1rem; }
.state { color: #8c8c8c; padding: 1rem 0; }
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
