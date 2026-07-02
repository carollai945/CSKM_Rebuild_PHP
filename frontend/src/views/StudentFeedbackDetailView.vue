<template>
  <div class="page">
    <div class="page-header">
      <h2>D04 學員意見明細（D040）</h2>
      <button @click="$router.back()">← 返回</button>
    </div>
    <div v-if="loading">載入中...</div>
    <div v-else-if="item" class="detail-card">
      <div class="info-grid">
        <div><span class="label">意見編號</span><span>{{ item.id }}</span></div>
        <div><span class="label">學員</span><span>{{ (item.student as any)?.name ?? '-' }}</span></div>
        <div><span class="label">狀態</span>
          <span :class="'badge badge-' + item.status">{{ statusLabel(item.status as string) }}</span>
        </div>
        <div><span class="label">建立日期</span><span>{{ String(item.created_at ?? '').slice(0, 10) }}</span></div>
      </div>

      <div class="content-section">
        <h4>意見內容</h4>
        <p>{{ item.content }}</p>
      </div>

      <div v-if="item.reply" class="content-section">
        <h4>回覆紀錄</h4>
        <p>{{ item.reply }}</p>
        <p style="color:#888;font-size:.85rem">處理人員：{{ (item.handler as any)?.name ?? '-' }}</p>
      </div>

      <div class="action-section" v-if="item.status !== 'RESOLVED'">
        <h4>更新狀態</h4>
        <select v-model="form.status">
          <option value="OPEN">待處理</option>
          <option value="IN_PROGRESS">處理中</option>
          <option value="RESOLVED">已結案</option>
        </select>
        <textarea v-model="form.reply" placeholder="回覆紀錄（選填）" rows="3"/>
        <button @click="submitUpdate">儲存</button>
      </div>
    </div>
    <p v-else style="text-align:center;color:#999;padding:2rem">找不到此意見記錄</p>
  </div>
</template>
<script setup lang="ts">
// 功能編號：D04 學員意見明細（D040）
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { studentFeedbacksApi as feedbackApi } from '@/api/studentFeedbacks'

const route = useRoute()
const id = Number(route.params.id)
const item = ref<Record<string, unknown> | null>(null)
const loading = ref(false)
const form = ref({ status: 'OPEN', reply: '' })

async function load() {
  loading.value = true
  const r = await feedbackApi.get(id)
  item.value = r.data?.data ?? null
  if (item.value) form.value.status = item.value.status as string
  loading.value = false
}

async function submitUpdate() {
  await feedbackApi.update(id, form.value)
  load()
}

function statusLabel(s: string) {
  return { OPEN: '待處理', IN_PROGRESS: '處理中', RESOLVED: '已結案' }[s] ?? s
}

onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem }
.detail-card { background: #fff; padding: 1.5rem; border-radius: 8px; max-width: 800px }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-bottom: 1.5rem }
.info-grid .label { color: #888; margin-right: .5rem; font-size: .9rem }
.content-section { margin-bottom: 1.5rem; padding: 1rem; background: #fafafa; border-radius: 6px }
.content-section h4 { margin: 0 0 .5rem; color: #333 }
.action-section { margin-top: 1.5rem }
.action-section select, .action-section textarea { display: block; width: 100%; margin: .5rem 0; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px }
.badge { padding: .2rem .6rem; border-radius: 4px; font-size: .85rem }
.badge-OPEN { background: #fff7e6; color: #d46b08 }
.badge-IN_PROGRESS { background: #e6f7ff; color: #0958d9 }
.badge-RESOLVED { background: #f6ffed; color: #389e0d }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff }
</style>
