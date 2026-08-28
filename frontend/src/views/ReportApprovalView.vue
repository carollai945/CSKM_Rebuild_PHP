<template>
  <div class="page">
    <div class="page-header">
      <h2>D03 報表審核</h2>
      <div v-if="selected.length">
        <button @click="batchApprove" style="background:#52c41a">批次核准({{ selected.length }})</button>
        <button @click="batchRejectModal=true" style="background:#ff4d4f;margin-left:.5rem">批次退回({{ selected.length }})</button>
      </div>
    </div>
    <div class="filters">
      <input v-model="filters.keyword" placeholder="填報人姓名" @keyup.enter="load" />
      <select v-model="filters.report_type" @change="load">
        <option value="">全部類型</option>
        <option value="DAILY">日報</option>
        <option value="WEEKLY">週報</option>
      </select>
      <input type="date" v-model="filters.from" @change="load" />
      <span>~</span>
      <input type="date" v-model="filters.to" @change="load" />
      <button @click="load">查詢</button>
    </div>
    <div v-if="loading">載入中...</div>
    <h3 v-else>學顧報表</h3>
    <table v-if="!loading">
      <thead><tr>
        <th><input type="checkbox" @change="toggleAll" :checked="selected.length===rows.length&&rows.length>0"/></th>
        <th>ID</th><th>日期</th><th>類型</th><th>申請人</th><th>狀態</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="r in advisorRows" :key="`advisor-${r.id}`">
          <td><input type="checkbox" :value="r.id" v-model="selected"/></td>
          <td>{{ r.id }}</td>
          <td>{{ r.report_date }}</td>
          <td>{{ reportTypeLabel(r.report_type as string) }}</td>
          <td>{{ (r.staff as any)?.name ?? r.staff_id }}</td>
          <td>{{ r.status }}</td>
          <td>
            <button class="secondary" @click="view(r.id as number)">檢視</button>
            <button @click="approveSingle(r.id as number)">核准</button>
            <button class="danger" @click="rejectOne(r.id as number)">退回</button>
          </td>
        </tr>
        <tr v-if="advisorRows.length === 0"><td colspan="6" class="empty">目前無待批核項目</td></tr>
      </tbody>
    </table>

    <h3 v-if="!loading" style="margin-top:1rem">行政報表</h3>
    <table v-if="!loading">
      <thead><tr>
        <th><input type="checkbox" @change="toggleAllOffice" :checked="selected.length===rows.length&&rows.length>0"/></th>
        <th>ID</th><th>日期</th><th>類型</th><th>申請人</th><th>狀態</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="r in officeRows" :key="`office-${r.id}`">
          <td><input type="checkbox" :value="r.id" v-model="selected"/></td>
          <td>{{ r.id }}</td>
          <td>{{ r.report_date }}</td>
          <td>{{ reportTypeLabel(r.report_type as string) }}</td>
          <td>{{ (r.staff as any)?.name ?? r.staff_id }}</td>
          <td>{{ r.status }}</td>
          <td>
            <button class="secondary" @click="view(r.id as number)">檢視</button>
            <button @click="approveSingle(r.id as number)">核准</button>
            <button class="danger" @click="rejectOne(r.id as number)">退回</button>
          </td>
        </tr>
        <tr v-if="officeRows.length === 0"><td colspan="6" class="empty">目前無待批核項目</td></tr>
      </tbody>
    </table>

    <div v-if="batchRejectModal||rejectId!=null" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h3>D03 報表審核</h3>
        <textarea v-model="rejectReason" rows="3" placeholder="請輸入退回原因（選填）"/>
        <div class="modal-actions">
          <button @click="doReject">確認退回</button>
          <button @click="closeModal">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：D03 報表審核
import { computed, ref, onMounted } from 'vue'
import { approvalsApi } from '@/api/approvals'
import { useRouter } from 'vue-router'
const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const selected = ref<number[]>([])
const batchRejectModal = ref(false)
const rejectId = ref<number | null>(null)
const rejectReason = ref('')
const filters = ref({ keyword: '', report_type: '', from: '', to: '' })
const router = useRouter()
const advisorRows = computed(() => rows.value.filter((r) => (((r.staff as any)?.user?.role ?? 'staff') === 'staff')))
const officeRows = computed(() => rows.value.filter((r) => (((r.staff as any)?.user?.role ?? 'staff') !== 'staff')))
async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.report_type) params.report_type = filters.value.report_type
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await approvalsApi.pendingReport(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  selected.value = []
  loading.value = false
}
function toggleAll(e: Event) {
  selected.value = (e.target as HTMLInputElement).checked ? advisorRows.value.map(r => r.id as number) : selected.value.filter((id) => !advisorRows.value.some((r) => r.id === id))
}
function toggleAllOffice(e: Event) {
  selected.value = (e.target as HTMLInputElement).checked ? [...new Set([...selected.value, ...officeRows.value.map(r => r.id as number)])] : selected.value.filter((id) => !officeRows.value.some((r) => r.id === id))
}
async function approveSingle(id: number) { await approvalsApi.approveReport(id); load() }
function rejectOne(id: number) { rejectId.value = id; rejectReason.value = ''; batchRejectModal.value = false }
function closeModal() { batchRejectModal.value = false; rejectId.value = null }
async function batchApprove() { await approvalsApi.batchApproveReport(selected.value); load() }
async function doReject() {
  if (rejectId.value != null) {
    await approvalsApi.rejectReport(rejectId.value, rejectReason.value)
    rejectId.value = null
  } else {
    await approvalsApi.batchRejectReport(selected.value, rejectReason.value)
    batchRejectModal.value = false
  }
  load()
}
function reportTypeLabel(value: string): string {
  return value === 'WEEKLY' ? '週報' : '日報'
}
function view(id: number) {
  const report = rows.value.find((row) => Number(row.id) === id) ?? {}
  const reportType = String((report as { report_type?: string }).report_type ?? 'DAILY')
  const role = String((report as { staff?: { user?: { role?: string } } }).staff?.user?.role ?? 'staff')
  const isAdvisor = role === 'staff'
  const routeName =
    reportType === 'WEEKLY'
      ? (isAdvisor ? 'd032' : 'd033')
      : (isAdvisor ? 'd030' : 'd031')
  router.push({ name: routeName, params: { id: String(id) } })
}
onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem }
.filters { display:flex; gap:.5rem; margin-bottom:1rem; align-items:center; flex-wrap:wrap }
.filters input, .filters select { padding:.4rem .6rem; border:1px solid #d9d9d9; border-radius:4px }
table { width: 100%; border-collapse: collapse; background: #fff }
th, td { padding: .6rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left }
th { background: #fafafa; font-weight: 600 }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff }
button.danger { background: #ff4d4f }
button.secondary { background: #13c2c2 }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 360px }
.modal textarea { width: 100%; margin: .5rem 0; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px }
.modal-actions { margin-top: 1rem; display: flex; gap: .5rem }
.empty { text-align:center; color:#999 }
</style>
