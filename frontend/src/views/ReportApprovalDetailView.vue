<template>
  <div class="page">
    <div class="header">
      <h2>{{ title }}</h2>
      <RouterLink to="/approvals/reports">← 返回 D03</RouterLink>
    </div>

    <div class="card" v-if="report">
      <div class="row"><strong>報表 ID：</strong>{{ report.id }}</div>
      <div class="row"><strong>日期：</strong>{{ report.report_date }}</div>
      <div class="row"><strong>類型：</strong>{{ reportTypeLabel(report.report_type) }}</div>
      <div class="row"><strong>填報人：</strong>{{ report.staff_name }}</div>
      <div class="row"><strong>狀態：</strong>{{ report.status }}</div>
      <div class="row"><strong>內容：</strong></div>
      <pre class="content">{{ report.content || '-' }}</pre>

      <div class="actions" v-if="report.status === 'SUBMITTED'">
        <button @click="approve">核准</button>
        <button class="danger" @click="showReject = true">退回</button>
      </div>
      <p v-if="msg" class="success">{{ msg }}</p>
    </div>

    <div v-if="showReject" class="modal-overlay" @click.self="showReject=false">
      <div class="modal">
        <h3>輸入退回原因</h3>
        <textarea v-model="rejectReason" rows="4" placeholder="請輸入退回原因（選填）" />
        <div class="modal-actions">
          <button class="danger" @click="reject">確認退回</button>
          <button @click="showReject=false">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { reportsApi } from '@/api/reports'
import { approvalsApi } from '@/api/approvals'

const route = useRoute()
const router = useRouter()
const showReject = ref(false)
const rejectReason = ref('')
const msg = ref('')
const report = ref<{
  id: number
  report_date: string
  report_type: string
  status: string
  content: string
  staff_name: string
} | null>(null)

const title = computed(() => {
  const map: Record<string, string> = {
    d030: 'D030 學顧日報審核明細',
    d031: 'D031 行政日報審核明細',
    d032: 'D032 學顧週報審核明細',
    d033: 'D033 行政週報審核明細',
  }
  return map[String(route.name ?? '')] ?? 'D03 報表審核明細'
})

function reportTypeLabel(value: string) {
  return value === 'WEEKLY' ? '週報' : '日報'
}

async function load() {
  const id = Number(route.params.id)
  if (!id) return
  const response = await reportsApi.get(id)
  const payload = response.data?.data ?? {}
  report.value = {
    id,
    report_date: String(payload.report_date ?? ''),
    report_type: String(payload.report_type ?? ''),
    status: String(payload.status ?? ''),
    content: String(payload.content ?? ''),
    staff_name: String(payload.staff?.name ?? payload.staff_id ?? '-'),
  }
}

async function approve() {
  if (!report.value) return
  await approvalsApi.approveReport(report.value.id)
  msg.value = '核准成功'
  await router.push('/approvals/reports')
}

async function reject() {
  if (!report.value) return
  await approvalsApi.rejectReport(report.value.id, rejectReason.value)
  showReject.value = false
  await router.push('/approvals/reports')
}

onMounted(load)
</script>

<style scoped>
.page{padding:1rem}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.card{background:#fff;padding:1.25rem;border-radius:8px;max-width:860px}
.row{margin-bottom:.5rem}
.content{white-space:pre-wrap;background:#fafafa;border:1px solid #eee;padding:.75rem;border-radius:4px}
.actions{display:flex;gap:.5rem;margin-top:1rem}
button{padding:.45rem 1rem;border:none;border-radius:4px;background:#1890ff;color:#fff;cursor:pointer}
.danger{background:#ff4d4f}
.success{color:#389e0d;margin-top:.5rem}
.modal-overlay{position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100}
.modal{background:#fff;padding:1.25rem;border-radius:8px;min-width:320px}
.modal textarea{width:100%;border:1px solid #d9d9d9;border-radius:4px;padding:.5rem;margin:.5rem 0}
.modal-actions{display:flex;gap:.5rem}
</style>
