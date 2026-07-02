<template>
  <div class="page">
    <div class="page-header">
      <h2>B00 電訪名單總覽</h2>
      <button @click="openCreate">新增名單</button>
    </div>

    <!-- 篩選條件 -->
    <div class="filters">
      <input v-model="filters.keyword" placeholder="姓名/電話搜尋" @change="load" />
      <select v-model="filters.status" @change="load">
        <option value="">全部狀態</option>
        <option value="NEW">未聯繫</option>
        <option value="CONTACTED">已聯繫</option>
        <option value="VISITED">約訪</option>
        <option value="CLOSED">成交</option>
        <option value="INACTIVE">無效</option>
      </select>
      <input type="date" v-model="filters.from" @change="load" placeholder="開始日期" />
      <input type="date" v-model="filters.to" @change="load" placeholder="結束日期" />
      <button @click="resetFilters">重置</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr>
        <th>ID</th><th>姓名</th><th>電話</th><th>狀態</th><th>指派業務</th><th>建立日期</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td>
          <td>{{ row.name }}</td>
          <td>{{ row.phone }}</td>
          <td>{{ row.status }}</td>
          <td>{{ (row.assigned_staff as any)?.name ?? '-' }}</td>
          <td>{{ row.created_at ? String(row.created_at).slice(0, 10) : '-' }}</td>
          <td>
            <button @click="edit(row)">編輯</button>
            <button @click="remove(row.id as number)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && rows.length === 0" style="text-align:center;color:#999;padding:2rem">無符合條件的名單</p>

    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>{{ editId ? '編輯名單' : '新增名單' }}</h3>
        <form @submit.prevent="save">
          <div><label>姓名</label><input v-model="form.name" placeholder="姓名" required /></div>
          <div><label>電話</label><input v-model="form.phone" placeholder="電話" /></div>
          <div><label>Email</label><input v-model="form.email" placeholder="Email" /></div>
          <div><label>狀態</label>
            <select v-model="form.status">
              <option value="NEW">未聯繫</option>
              <option value="CONTACTED">已聯繫</option>
              <option value="VISITED">約訪</option>
              <option value="CLOSED">成交</option>
            </select>
          </div>
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
// 功能編號：B00 電訪名單總覽
import { ref, onMounted } from 'vue'
import { leadsApi } from '@/api/leads'

const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number | null>(null)
const form = ref<Record<string, unknown>>({})
const filters = ref({ keyword: '', status: '', from: '', to: '' })

async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await leadsApi.list(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}

function resetFilters() { filters.value = { keyword: '', status: '', from: '', to: '' }; load() }
function openCreate() { editId.value = null; form.value = { status: 'NEW' }; showForm.value = true }
function edit(row: Record<string, unknown>) { editId.value = row.id as number; form.value = { ...row }; showForm.value = true }
async function save() {
  if (editId.value) await leadsApi.update(editId.value, form.value)
  else await leadsApi.create(form.value)
  showForm.value = false; editId.value = null; form.value = {}; load()
}
async function remove(id: number) { if (confirm('確認刪除?')) { await leadsApi.delete(id); load() } }
onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem }
.filters { display: flex; gap: .5rem; margin-bottom: 1rem; flex-wrap: wrap }
.filters input, .filters select { padding: .4rem .6rem; border: 1px solid #d9d9d9; border-radius: 4px }
table { width: 100%; border-collapse: collapse; background: #fff }
th, td { padding: .6rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left }
th { background: #fafafa; font-weight: 600 }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff; margin-right: .25rem }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 360px }
.modal label { display: block; margin-bottom: .25rem; font-weight: 500 }
.modal input, .modal select { width: 100%; margin-bottom: .75rem; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px; box-sizing: border-box }
.modal-actions { display: flex; gap: .5rem; margin-top: .5rem }
</style>
