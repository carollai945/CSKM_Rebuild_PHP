<template>
  <div class="page">
    <h2>C04 學生分配管理</h2>

    <div class="filters">
      <input v-model="filters.keyword" placeholder="學生姓名 / 手機" @keyup.enter="load" />
      <select v-model="filters.advisor_staff_id" @change="load">
        <option value="">全部顧問</option>
        <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
      <select v-model="filters.status" @change="load">
        <option value="">全部狀態</option>
        <option value="ACTIVE">在學</option>
        <option value="INACTIVE">停學</option>
        <option value="GRADUATED">畢業</option>
      </select>
      <button @click="load">搜尋</button>
      <button @click="resetFilters">清除</button>
    </div>

    <div v-if="selected.size > 0" class="batch-bar">
      <span>已選 {{ selected.size }} 位學生</span>
      <button @click="openAssignPopup()">學顧變更</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead>
        <tr>
          <th><input type="checkbox" @change="toggleAll($event)" /></th>
          <th>學生</th>
          <th>狀態</th>
          <th>目前顧問</th>
          <th>指派顧問</th>
          <th>分流</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td><input type="checkbox" :value="r.id" v-model="selectedArr" /></td>
          <td>{{ (r as any).name }}</td>
          <td>{{ r.status }}</td>
          <td>{{ (r.advisor as any)?.name ?? '-' }}</td>
          <td>
            {{ advisorNameById(advisorMap[r.id as number] as number | '') || '未指派' }}
          </td>
          <td>
            <div class="entry-actions">
              <button class="secondary" @click="goPayments(r.id as number)">繳費</button>
              <button class="secondary" @click="goServices(r.id as number)">服務</button>
              <button class="secondary" @click="goFeedbacks(r.id as number)">意見</button>
              <button class="secondary" @click="goDetail(r.id as number)">明細</button>
            </div>
          </td>
          <td>
            <button @click="openAssignPopup(r.id as number)">學顧變更</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button :disabled="page <= 1" @click="page--; load()">上一頁</button>
      <span>第 {{ page }} 頁 / 共 {{ lastPage }} 頁</span>
      <button :disabled="page >= lastPage" @click="page++; load()">下一頁</button>
    </div>

    <div v-if="assignModalVisible" class="modal-overlay" @click.self="closeAssignPopup">
      <div class="modal">
        <h3>C04 學顧變更</h3>
        <p>目標學生數：{{ assignTargetIds.length }}</p>
        <label>選擇顧問</label>
        <select v-model="batchAdvisorId">
          <option value="">選擇顧問</option>
          <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
        <p v-if="assignError" class="error">{{ assignError }}</p>
        <div class="modal-actions">
          <button :disabled="!batchAdvisorId || assigning" @click="confirmAssign">確認變更</button>
          <button class="secondary" @click="closeAssignPopup">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// 功能編號：C04 學生分配管理
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { studentsApi } from '@/api/students'
import { staffApi } from '@/api/staff'

const rows = ref<Record<string,unknown>[]>([])
const staffList = ref<{id: number; name: string}[]>([])
const loading = ref(false)
const page = ref(1)
const lastPage = ref(1)
const filters = ref({ keyword: '', advisor_staff_id: '' as string|number, status: '' })
const selectedArr = ref<number[]>([])
const selected = computed(() => new Set(selectedArr.value))
const batchAdvisorId = ref<number|''>('')
const advisorMap = ref<Record<number, number|''>>({})
const assignModalVisible = ref(false)
const assignTargetIds = ref<number[]>([])
const assignError = ref('')
const assigning = ref(false)
const router = useRouter()

async function load() {
  loading.value = true
  const params: Record<string,unknown> = { page: page.value }
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.advisor_staff_id !== '') params.advisor_staff_id = filters.value.advisor_staff_id
  if (filters.value.status) params.status = filters.value.status
  const r = await studentsApi.list(params)
  const d = r.data?.data
  rows.value = d?.data ?? d ?? []
  lastPage.value = d?.last_page ?? 1
  // Init advisorMap
  rows.value.forEach((row) => {
    const id = row.id as number
    if (advisorMap.value[id] === undefined) {
      advisorMap.value[id] = (row.advisor_staff_id as number) || ''
    }
  })
  loading.value = false
}

function resetFilters() {
  filters.value = { keyword: '', advisor_staff_id: '', status: '' }
  page.value = 1
  selectedArr.value = []
  load()
}

function toggleAll(e: Event) {
  const checked = (e.target as HTMLInputElement).checked
  selectedArr.value = checked ? rows.value.map(r => r.id as number) : []
}

function advisorNameById(id: number | '') {
  if (!id) return ''
  return staffList.value.find((staff) => staff.id === Number(id))?.name ?? ''
}

function openAssignPopup(studentId?: number) {
  assignError.value = ''
  if (typeof studentId === 'number') {
    assignTargetIds.value = [studentId]
    batchAdvisorId.value = advisorMap.value[studentId] ?? ''
  } else {
    assignTargetIds.value = [...selected.value]
    batchAdvisorId.value = ''
  }
  if (assignTargetIds.value.length === 0) {
    assignError.value = '請先選取至少一位學生'
    return
  }
  assignModalVisible.value = true
}

function closeAssignPopup() {
  assignModalVisible.value = false
  assigning.value = false
  assignError.value = ''
}

async function confirmAssign() {
  if (!batchAdvisorId.value) {
    assignError.value = '請先選擇顧問'
    return
  }
  assigning.value = true
  await studentsApi.assign({ student_ids: assignTargetIds.value, advisor_staff_id: batchAdvisorId.value })
  assignTargetIds.value.forEach((studentId) => {
    advisorMap.value[studentId] = batchAdvisorId.value
  })
  selectedArr.value = []
  closeAssignPopup()
  await load()
}
function goPayments(studentId: number) { router.push({ name: 'payments', query: { student_id: String(studentId) } }) }
function goServices(studentId: number) { router.push({ name: 'student-services', query: { student_id: String(studentId) } }) }
function goFeedbacks(studentId: number) { router.push({ name: 'student-feedbacks', query: { student_id: String(studentId) } }) }
function goDetail(studentId: number) { router.push({ name: 'student-detail', params: { id: String(studentId) } }) }

onMounted(async () => {
  const r = await staffApi.list()
  staffList.value = (r.data?.data?.data ?? r.data?.data ?? []).map((s: Record<string,unknown>) => ({
    id: s.id as number,
    name: s.name as string
  }))
  load()
})
</script>

<style scoped>
.page { padding:1rem }
.filters { display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem }
.filters input, .filters select { padding:.4rem .6rem;border:1px solid #d9d9d9;border-radius:4px }
.batch-bar { display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;background:#e6f7ff;padding:.5rem 1rem;border-radius:4px }
.batch-bar select { padding:.35rem .6rem;border:1px solid #d9d9d9;border-radius:4px }
table { width:100%;border-collapse:collapse;background:#fff }
th,td { padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left }
th { background:#fafafa;font-weight:600 }
td select { padding:.25rem .5rem;border:1px solid #d9d9d9;border-radius:4px;width:100% }
button { padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;background:#1890ff;color:#fff }
button.secondary { background:#13c2c2 }
button:disabled { opacity:.5;cursor:not-allowed }
.pagination { display:flex;align-items:center;gap:1rem;margin-top:1rem }
.entry-actions { display:flex; gap:.25rem; flex-wrap:wrap }
.modal-overlay { position:fixed; inset:0; background:#0005; display:flex; align-items:center; justify-content:center; z-index:100 }
.modal { background:#fff; padding:1.25rem; border-radius:8px; min-width:320px; max-width:420px; width:100% }
.modal select { width:100%; padding:.4rem .6rem; border:1px solid #d9d9d9; border-radius:4px; margin:.5rem 0 }
.modal-actions { display:flex; gap:.5rem; margin-top:1rem }
.error { color:#cf1322; margin-top:.5rem }
</style>
