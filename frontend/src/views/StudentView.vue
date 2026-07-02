<template>
  <div class="page">
    <div class="page-header">
      <h2>學員管理</h2>
      <button @click="showForm = true">新增</button>
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
        <h3>{{ editId ? '編輯' : '新增' }}學員管理</h3>
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
import { ref, onMounted } from 'vue'
import { studentsApi } from '@/api/students'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number|null>(null)
const form = ref<Record<string,unknown>>({})
async function load() { loading.value = true; const r = await studentsApi.list(); rows.value = r.data?.data?.data ?? r.data?.data ?? []; loading.value = false }
function edit(row: Record<string,unknown>) { editId.value = row.id as number; form.value = {...row}; showForm.value = true }
async function save() { if (editId.value) await studentsApi.update(editId.value, form.value); else await studentsApi.create(form.value); showForm.value = false; editId.value = null; form.value = {}; load() }
async function remove(id: number) { if (confirm('確認刪除?')) { await studentsApi.delete(id); load() } }
onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem }
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