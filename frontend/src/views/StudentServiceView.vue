<template>
  <div class="page">
    <div class="page-header">
      <h2>B02 學員服務記錄</h2>
      <button @click="showForm = true">新增</button>
    </div>
    <div class="filters">
      <input v-model.number="filters.student_id" type="number" min="1" placeholder="學員 ID" @keyup.enter="load" />
      <input v-model="filters.service_type" placeholder="服務類型" @keyup.enter="load" />
      <button @click="load">搜尋</button>
      <button @click="resetFilters">清除</button>
    </div>
    <div v-if="loading" class="loading">載入中...</div>
    <table v-else>
      <thead><tr>
          <th>ID</th>
          <th>學員</th>
          <th>類型</th>
          <th>日期</th>
          <th>分流</th>
        <th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td>
          <td>{{ (row.student as any)?.name ?? row.student_id }}</td>
          <td>{{ row.service_type }}</td>
          <td>{{ row.service_date }}</td>
          <td>
            <button class="secondary" @click="goPayments(row.student_id as number)">繳費</button>
            <button class="secondary" @click="goFeedbacks(row.student_id as number)">意見</button>
            <button class="secondary" @click="goStudentDetail(row.student_id as number)">明細</button>
          </td>
          <td>
            <button @click="edit(row)">編輯</button>
            <button @click="remove(row.id)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>B02 學員服務記錄</h3>
        <form @submit.prevent="save">
          <div><label>類型</label><input v-model="form.service_type" placeholder="類型" /></div>
          <div><label>日期</label><input v-model="form.service_date" placeholder="日期" /></div>
          <div><label>描述</label><input v-model="form.description" placeholder="描述" /></div>
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
// 功能編號：B02 學員服務記錄
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { studentServicesApi } from '@/api/studentServices'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number|null>(null)
const form = ref<Record<string,unknown>>({})
const filters = ref({ student_id: '' as number | '', service_type: '' })
const route = useRoute()
const router = useRouter()
async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.student_id) params.student_id = filters.value.student_id
  if (filters.value.service_type) params.service_type = filters.value.service_type
  const r = await studentServicesApi.list(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}
function edit(row: Record<string,unknown>) { editId.value = row.id as number; form.value = {...row}; showForm.value = true }
async function save() { if (editId.value) await studentServicesApi.update(editId.value, form.value); else await studentServicesApi.create(form.value); showForm.value = false; editId.value = null; form.value = {}; load() }
async function remove(id: number) { if (confirm('確認刪除?')) { await studentServicesApi.delete(id); load() } }
function resetFilters() { filters.value = { student_id: '', service_type: '' }; load() }
function goPayments(studentId: number) { if (studentId) router.push({ name: 'payments', query: { student_id: String(studentId) } }) }
function goFeedbacks(studentId: number) { if (studentId) router.push({ name: 'student-feedbacks', query: { student_id: String(studentId) } }) }
function goStudentDetail(studentId: number) { if (studentId) router.push({ name: 'student-detail', params: { id: String(studentId) } }) }
onMounted(() => {
  const studentId = route.query.student_id ? Number(route.query.student_id) : NaN
  if (!Number.isNaN(studentId) && studentId > 0) filters.value.student_id = studentId
  load()
})
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem }
.filters { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap }
.filters input { padding:.4rem .6rem; border:1px solid #d9d9d9; border-radius:4px }
table { width:100%; border-collapse:collapse; background:#fff }
th,td { padding:.6rem 1rem; border-bottom:1px solid #f0f0f0; text-align:left }
th { background:#fafafa; font-weight:600 }
.modal-overlay { position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100 }
.modal { background:#fff;padding:2rem;border-radius:8px;min-width:360px }
.modal input { display:block;width:100%;margin:.5rem 0;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px }
.modal-actions { margin-top:1rem;display:flex;gap:.5rem }
button { padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;background:#1890ff;color:#fff }
button.secondary { background:#13c2c2 }
button:last-child { background:#fff;color:#333;border:1px solid #d9d9d9 }
.loading { padding:2rem;text-align:center;color:#999 }
</style>