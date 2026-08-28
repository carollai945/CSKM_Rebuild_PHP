<template>
  <div class="page">
    <h2>E00 繳費記錄</h2>
    <div class="page-header">
      <div class="filters">
        <input v-model="filters.keyword" placeholder="學生姓名" @keyup.enter="load" />
        <input v-model.number="filters.student_id" type="number" min="1" placeholder="學生 ID" @keyup.enter="load" />
        <select v-model="filters.status" @change="load">
          <option value="">全部狀態</option>
          <option value="PENDING">待審</option>
          <option value="FINANCE_CONFIRMED">財務已確認</option>
          <option value="ACADEMIC_CONFIRMED">學務已確認</option>
          <option value="REJECTED">已退回</option>
        </select>
        <input type="date" v-model="filters.from" @change="load" />
        <span>~</span>
        <input type="date" v-model="filters.to" @change="load" />
        <button @click="load">搜尋</button>
        <button @click="resetFilters">清除</button>
      </div>
      <button @click="openCreate">＋ 新增繳費</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead>
        <tr><th>ID</th><th>學生</th><th>費用項目</th><th>金額</th><th>繳費日期</th><th>狀態</th><th>備註</th><th>操作</th></tr>
      </thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{ r.id }}</td>
          <td>{{ (r.student as any)?.name ?? r.student_id }}</td>
          <td>{{ (r.fee_item as any)?.name ?? '-' }}</td>
          <td>{{ r.amount }}</td>
          <td>{{ r.payment_date ?? '-' }}</td>
          <td><span :class="'status-' + r.status">{{ statusLabel(r.status as string) }}</span></td>
          <td>{{ r.note ?? '-' }}</td>
          <td>
            <button v-if="r.status === 'PENDING'" @click="financeConfirm(r.id as number)">財務確認</button>
            <button v-if="r.status === 'FINANCE_CONFIRMED'" @click="academicConfirm(r.id as number)">學務確認</button>
            <button v-if="r.status === 'PENDING' || r.status === 'FINANCE_CONFIRMED'" class="danger" @click="openReject(r.id as number)">退回</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button :disabled="page <= 1" @click="page--; load()">上一頁</button>
      <span>第 {{ page }} 頁 / 共 {{ lastPage }} 頁</span>
      <button :disabled="page >= lastPage" @click="page++; load()">下一頁</button>
    </div>

    <!-- 新增繳費 Modal -->
    <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
      <div class="modal">
        <h3>新增繳費記錄</h3>
        <label>學生 ID<input v-model.number="form.student_id" type="number" /></label>
        <label>費用項目 ID<input v-model.number="form.fee_item_id" type="number" /></label>
        <label>金額<input v-model.number="form.amount" type="number" step="0.01" /></label>
        <label>幣別<input v-model="form.currency" placeholder="TWD" /></label>
        <label>付款方式<input v-model="form.payment_method" /></label>
        <label>繳費日期<input v-model="form.payment_date" type="date" /></label>
        <label>備註<textarea v-model="form.note" rows="2"></textarea></label>
        <div class="modal-actions">
          <button @click="submitCreate">確認</button>
          <button @click="showCreate = false">取消</button>
        </div>
      </div>
    </div>

    <div v-if="rejectTargetId !== null" class="modal-overlay" @click.self="closeReject">
      <div class="modal">
        <h3>退回繳費記錄</h3>
        <label>退回原因<textarea v-model="rejectReason" rows="3" placeholder="請輸入退回原因（選填）"></textarea></label>
        <div class="modal-actions">
          <button class="danger" @click="submitReject">確認退回</button>
          <button @click="closeReject">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// 功能編號：E00 繳費記錄
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { paymentsApi } from '@/api/payments'

const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const page = ref(1)
const lastPage = ref(1)
const route = useRoute()

const filters = ref({ keyword: '', student_id: '' as number | '', status: '', from: '', to: '' })
const showCreate = ref(false)
const form = ref({ student_id: 0, fee_item_id: 0 as number|null, amount: 0, currency: 'TWD', payment_method: '', payment_date: '', note: '' })
const rejectTargetId = ref<number | null>(null)
const rejectReason = ref('')

async function load() {
  loading.value = true
  const params: Record<string,unknown> = { page: page.value }
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.student_id) params.student_id = filters.value.student_id
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await paymentsApi.list(params)
  const data = r.data?.data
  rows.value = data?.data ?? []
  lastPage.value = data?.last_page ?? 1
  loading.value = false
}

function resetFilters() {
  filters.value = { keyword: '', student_id: '', status: '', from: '', to: '' }
  page.value = 1
  load()
}

function openCreate() {
  form.value = { student_id: 0, fee_item_id: null as any, amount: 0, currency: 'TWD', payment_method: '', payment_date: '', note: '' }
  showCreate.value = true
}

async function submitCreate() {
  await paymentsApi.create({ ...form.value, fee_item_id: form.value.fee_item_id || null })
  showCreate.value = false
  load()
}

async function financeConfirm(id: number) { await paymentsApi.financeConfirm(id); load() }
async function academicConfirm(id: number) { await paymentsApi.academicConfirm(id); load() }
function openReject(id: number) { rejectTargetId.value = id; rejectReason.value = '' }
function closeReject() { rejectTargetId.value = null; rejectReason.value = '' }
async function submitReject() {
  if (rejectTargetId.value === null) return
  await paymentsApi.reject(rejectTargetId.value, rejectReason.value)
  closeReject()
  load()
}

function statusLabel(s: string) {
  const map: Record<string,string> = {
    PENDING: '待審', FINANCE_CONFIRMED: '財務已確認',
    ACADEMIC_CONFIRMED: '學務已確認', REJECTED: '已退回'
  }
  return map[s] ?? s
}

onMounted(() => {
  const queryStudentId = route.query.student_id ? Number(route.query.student_id) : NaN
  if (!Number.isNaN(queryStudentId) && queryStudentId > 0) {
    filters.value.student_id = queryStudentId
  }
  load()
})
</script>

<style scoped>
.page { padding:1rem }
.page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem }
.filters { display:flex;gap:.5rem;flex-wrap:wrap;align-items:center }
.filters input, .filters select { padding:.4rem .6rem;border:1px solid #d9d9d9;border-radius:4px }
table { width:100%;border-collapse:collapse;background:#fff }
th,td { padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left }
th { background:#fafafa;font-weight:600 }
button { padding:.4rem 1rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer }
button.danger { background:#ff4d4f }
button:disabled { opacity:.5;cursor:not-allowed }
.pagination { display:flex;align-items:center;gap:1rem;margin-top:1rem }
.status-PENDING { color:#fa8c16 }
.status-FINANCE_CONFIRMED { color:#1890ff }
.status-ACADEMIC_CONFIRMED { color:#52c41a }
.status-REJECTED { color:#ff4d4f }
.modal-overlay { position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100 }
.modal { background:#fff;padding:2rem;border-radius:8px;min-width:380px;max-height:90vh;overflow-y:auto }
.modal label { display:block;margin:.6rem 0 }
.modal input, .modal select, .modal textarea { display:block;width:100%;padding:.4rem .6rem;border:1px solid #d9d9d9;border-radius:4px;margin-top:.25rem }
.modal-actions { margin-top:1rem;display:flex;gap:.5rem }
</style>
