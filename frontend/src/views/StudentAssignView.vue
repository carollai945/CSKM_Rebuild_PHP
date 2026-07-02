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
      <select v-model="batchAdvisorId">
        <option value="">選擇顧問</option>
        <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
      </select>
      <button :disabled="!batchAdvisorId" @click="batchAssign">批次指派</button>
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
            <select v-model="advisorMap[r.id as number]">
              <option value="">未指派</option>
              <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </td>
          <td>
            <button :disabled="!advisorMap[r.id as number]" @click="assignOne(r.id as number)">指派</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button :disabled="page <= 1" @click="page--; load()">上一頁</button>
      <span>第 {{ page }} 頁 / 共 {{ lastPage }} 頁</span>
      <button :disabled="page >= lastPage" @click="page++; load()">下一頁</button>
    </div>
  </div>
</template>

<script setup lang="ts">
// 功能編號：C04 學生分配管理
import { ref, computed, onMounted } from 'vue'
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

async function assignOne(studentId: number) {
  const advisorId = advisorMap.value[studentId]
  if (!advisorId) return
  await studentsApi.assign({ student_ids: [studentId], advisor_staff_id: advisorId })
  load()
}

async function batchAssign() {
  if (!batchAdvisorId.value || selected.value.size === 0) return
  await studentsApi.assign({ student_ids: [...selected.value], advisor_staff_id: batchAdvisorId.value })
  selectedArr.value = []
  batchAdvisorId.value = ''
  load()
}

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
button:disabled { opacity:.5;cursor:not-allowed }
.pagination { display:flex;align-items:center;gap:1rem;margin-top:1rem }
</style>
