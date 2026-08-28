<template>
  <div class="page">
    <h2>A02 個人報表</h2>
    <div class="toolbar">
      <div class="filters">
        <label>起日 <input type="date" v-model="filters.from" /></label>
        <label>迄日 <input type="date" v-model="filters.to" /></label>
        <button @click="load">搜尋</button>
      </div>
      <div class="actions">
        <button @click="openCreate('DAILY', 'advisor')">學顧日報</button>
        <button @click="openCreate('DAILY', 'admin')">行政日報</button>
        <button @click="openCreate('WEEKLY', 'advisor')">學顧週報</button>
        <button @click="openCreate('WEEKLY', 'admin')">行政週報</button>
      </div>
    </div>

    <div v-if="loading">載入中...</div>
    <template v-else>
      <h3>日報</h3>
      <table>
        <thead><tr><th>日期</th><th>狀態</th><th>內容</th><th>操作</th></tr></thead>
        <tbody>
          <tr v-for="r in dailyRows" :key="`daily-${r.id}`">
            <td>{{ r.report_date }}</td>
            <td>{{ reportStatusLabel(String(r.status ?? '')) }}</td>
            <td>{{ (r.content as string) || '-' }}</td>
            <td>
              <button @click="goDetail(r, r.status === 'DRAFT')">{{ r.status === 'DRAFT' ? '修改' : '檢視' }}</button>
              <button v-if="r.status==='DRAFT'" @click="submit(r.id as number)">送審</button>
            </td>
          </tr>
          <tr v-if="dailyRows.length === 0"><td colspan="4" class="empty">查無資料</td></tr>
        </tbody>
      </table>

      <h3 style="margin-top:1rem">週報</h3>
      <table>
        <thead><tr><th>日期</th><th>狀態</th><th>內容</th><th>操作</th></tr></thead>
        <tbody>
          <tr v-for="r in weeklyRows" :key="`weekly-${r.id}`">
            <td>{{ r.report_date }}</td>
            <td>{{ reportStatusLabel(String(r.status ?? '')) }}</td>
            <td>{{ (r.content as string) || '-' }}</td>
            <td>
              <button @click="goDetail(r, r.status === 'DRAFT')">{{ r.status === 'DRAFT' ? '修改' : '檢視' }}</button>
              <button v-if="r.status==='DRAFT'" @click="submit(r.id as number)">送審</button>
            </td>
          </tr>
          <tr v-if="weeklyRows.length === 0"><td colspan="4" class="empty">查無資料</td></tr>
        </tbody>
      </table>
    </template>
  </div>
</template>
<script setup lang="ts">
// 功能編號：A02 個人報表
import { computed, ref, onMounted } from 'vue'
import { reportsApi } from '@/api/reports'
import { useRouter } from 'vue-router'

type StaffKind = 'advisor' | 'admin'

const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const filters = ref({ from: '', to: '' })
const router = useRouter()
const dailyRows = computed(() => rows.value.filter(r => r.report_type === 'DAILY'))
const weeklyRows = computed(() => rows.value.filter(r => r.report_type === 'WEEKLY'))

function reportStatusLabel(value: string): string {
  const map = {
    DRAFT: '草稿',
    SUBMITTED: '已送審',
    APPROVED: '已核准',
    REJECTED: '已退回',
  } as const
  return map[value as keyof typeof map] ?? value
}

function openCreate(reportType: 'DAILY' | 'WEEKLY', kind: StaffKind) {
  const routeByKey: Record<string, string> = {
    'DAILY-advisor': 'a020',
    'DAILY-admin': 'a021',
    'WEEKLY-advisor': 'a022',
    'WEEKLY-admin': 'a023',
  }
  router.push({ name: routeByKey[`${reportType}-${kind}`], query: { mode: 'create' } })
}

function guessKind(row: Record<string, unknown>): StaffKind {
  const value = String((row as { content?: string }).content ?? '')
  return value.startsWith('[行政]') ? 'admin' : 'advisor'
}

function goDetail(row: Record<string, unknown>, editable: boolean) {
  const kind = guessKind(row)
  const reportType = String(row.report_type ?? 'DAILY')
  const routeByKey: Record<string, string> = {
    'DAILY-advisor': 'a024',
    'DAILY-admin': 'a025',
    'WEEKLY-advisor': 'a026',
    'WEEKLY-admin': 'a027',
  }
  router.push({
    name: routeByKey[`${reportType}-${kind}`],
    params: { id: String(row.id) },
    query: { mode: editable ? 'edit' : 'view' },
  })
}

async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await reportsApi.list(params)
  rows.value = r.data?.data?.data ?? []
  loading.value = false
}

async function submit(id: number) {
  await reportsApi.submit(id)
  await load()
}

onMounted(load)
</script>
<style scoped>
.page { padding:1rem }
table { width:100%;border-collapse:collapse;background:#fff }
th,td { padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left }
th { background:#fafafa;font-weight:600 }
button { padding:.5rem 1rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer }
.toolbar { display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:1rem; flex-wrap:wrap }
.filters { display:flex; gap:.5rem; align-items:flex-end; flex-wrap:wrap }
.filters label { margin-bottom:0 }
.actions { display:flex; gap:.5rem; flex-wrap:wrap }
.empty { text-align:center; color:#999 }
</style>
