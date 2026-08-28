<template>
  <div class="page">
    <div class="page-header">
      <h2>C02 學生管理</h2>
      <div class="header-actions">
        <button v-if="canExport" @click="exportExcel">匯出 Excel</button>
        <button @click="showForm = true">新增</button>
      </div>
    </div>
    <div class="filters">
      <input v-model="filters.keyword" placeholder="姓名 / 學號 / 電話" @keyup.enter="load" />
      <select v-model="filters.status" @change="load">
        <option value="">全部狀態</option>
        <option value="ACTIVE">在學</option>
        <option value="INACTIVE">停學</option>
        <option value="GRADUATED">畢業</option>
      </select>
      <input v-model.number="filters.region_id" type="number" min="1" placeholder="區域 ID" @keyup.enter="load" />
      <button @click="load">搜尋</button>
      <button @click="resetFilters">清除</button>
    </div>
    <div v-if="loading" class="loading">載入中...</div>
    <table v-else>
      <thead><tr>
          <th>ID</th>
          <th>學號</th>
          <th>姓名</th>
          <th>狀態</th>
        <th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td>
          <td>{{ row.student_no }}</td>
          <td>{{ row.name }}</td>
          <td>{{ row.status }}</td>
          <td>
            <button @click="edit(row)">編輯</button>
            <button @click="remove(row.id)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>C02 學生管理</h3>
        <form @submit.prevent="save">
          <div><label>學號</label><input v-model="form.student_no" placeholder="學號" /></div>
          <div><label>姓名</label><input v-model="form.name" placeholder="姓名" /></div>
          <div class="modal-actions">
            <button type="submit">儲存</button>
            <button type="button" @click="showForm = false">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：C02 學生管理
import { computed, ref, onMounted } from 'vue'
import { studentsApi } from '@/api/students'
import { useAuthStore } from '@/stores/auth'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number|null>(null)
const form = ref<Record<string,unknown>>({})
const filters = ref({ keyword: '', status: '', region_id: '' as number | '' })
const auth = useAuthStore()
const canExport = computed(() => {
  const role = String(auth.user?.role ?? '')
  return ['admin', 'ceo', 'regmgr'].includes(role)
})
async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.region_id) params.region_id = filters.value.region_id
  const r = await studentsApi.list(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}
function edit(row: Record<string,unknown>) { editId.value = row.id as number; form.value = {...row}; showForm.value = true }
async function save() { if (editId.value) await studentsApi.update(editId.value, form.value); else await studentsApi.create(form.value); showForm.value = false; editId.value = null; form.value = {}; load() }
async function remove(id: number) { if (confirm('確認刪除?')) { await studentsApi.delete(id); load() } }
function resetFilters() { filters.value = { keyword: '', status: '', region_id: '' }; load() }
async function exportExcel() {
  const params = new URLSearchParams()
  if (filters.value.keyword) params.set('keyword', filters.value.keyword)
  if (filters.value.status) params.set('status', filters.value.status)
  if (filters.value.region_id) params.set('region_id', String(filters.value.region_id))
  const r = await import('@/api/axios').then(m => m.default.get('/students/export', { params, responseType: 'blob' }))
  const url = URL.createObjectURL(new Blob([r.data]))
  const a = document.createElement('a'); a.href = url
  a.download = '學生資料_' + new Date().toISOString().slice(0,10) + '.xlsx'
  a.click(); URL.revokeObjectURL(url)
}
onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem }
.header-actions { display:flex; gap:.5rem }
.filters { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap }
.filters input,.filters select { padding:.4rem .6rem; border:1px solid #d9d9d9; border-radius:4px }
table { width:100%; border-collapse:collapse; background:#fff }
th,td { padding:.6rem 1rem; border-bottom:1px solid #f0f0f0; text-align:left }
th { background:#fafafa; font-weight:600 }
.modal-overlay { position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100 }
.modal { background:#fff;padding:2rem;border-radius:8px;min-width:360px }
.modal input { display:block;width:100%;margin:.5rem 0;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px }
.modal-actions { margin-top:1rem;display:flex;gap:.5rem }
button { padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;background:#1890ff;color:#fff }
button:last-child { background:#fff;color:#333;border:1px solid #d9d9d9 }
.loading { padding:2rem;text-align:center;color:#999 }
</style>