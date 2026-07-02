<template>
  <div class="page">
    <div class="page-header">
      <h2>B01 電訪進度追蹤</h2>
      <button @click="openCreate">＋ 新增電訪</button>
    </div>

    <div class="filters">
      <select v-model="filters.result_code" @change="load">
        <option value="">全部結果</option>
        <option value="INTERESTED">有興趣</option>
        <option value="NOT_INTERESTED">無興趣</option>
        <option value="NO_ANSWER">未接通</option>
        <option value="CALLBACK">約回電</option>
      </select>
      <input type="date" v-model="filters.from" @change="load" />
      <span>~</span>
      <input type="date" v-model="filters.to" @change="load" />
      <button @click="load">搜尋</button>
      <button @click="resetFilters">清除</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead>
        <tr><th>ID</th><th>姓名（潛力生）</th><th>電訪日期</th><th>結果</th><th>人員</th><th>備註</th><th>操作</th></tr>
      </thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{ r.id }}</td>
          <td>{{ (r.lead as any)?.name ?? r.lead_id }}</td>
          <td>{{ r.interview_date }}</td>
          <td>{{ resultLabel(r.result_code as string) }}</td>
          <td>{{ (r.staff as any)?.name ?? r.staff_id }}</td>
          <td>{{ r.content ?? '-' }}</td>
          <td>
            <button @click="openEdit(r)">編輯</button>
            <button class="danger" @click="remove(r.id as number)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button :disabled="page <= 1" @click="page--; load()">上一頁</button>
      <span>第 {{ page }} 頁 / 共 {{ lastPage }} 頁</span>
      <button :disabled="page >= lastPage" @click="page++; load()">下一頁</button>
    </div>

    <!-- 新增/編輯 Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>{{ editId ? '編輯電訪記錄' : '新增電訪記錄' }}</h3>
        <label>潛力生 ID<input v-model.number="form.lead_id" type="number" /></label>
        <label>電訪日期<input v-model="form.interview_date" type="date" /></label>
        <label>結果
          <select v-model="form.result_code">
            <option value="INTERESTED">有興趣</option>
            <option value="NOT_INTERESTED">無興趣</option>
            <option value="NO_ANSWER">未接通</option>
            <option value="CALLBACK">約回電</option>
          </select>
        </label>
        <label>備註<textarea v-model="form.content" rows="3"></textarea></label>
        <div class="modal-actions">
          <button @click="save">儲存</button>
          <button @click="showForm = false">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// 功能編號：B01 電訪進度追蹤
import { ref, onMounted } from 'vue'
import { interviewsApi } from '@/api/interviews'

const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const page = ref(1)
const lastPage = ref(1)
const showForm = ref(false)
const editId = ref<number|null>(null)
const form = ref<Record<string,unknown>>({})
const filters = ref({ result_code: '', from: '', to: '' })

async function load() {
  loading.value = true
  const params: Record<string,unknown> = {}
  if (filters.value.result_code) params.result_code = filters.value.result_code
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await interviewsApi.list(params)
  rows.value = r.data?.data ?? []
  loading.value = false
}

function resetFilters() {
  filters.value = { result_code: '', from: '', to: '' }
  page.value = 1
  load()
}

function openCreate() {
  editId.value = null
  form.value = { lead_id: '', interview_date: '', result_code: 'INTERESTED', content: '' }
  showForm.value = true
}

function openEdit(row: Record<string,unknown>) {
  editId.value = row.id as number
  form.value = { ...row }
  showForm.value = true
}

async function save() {
  if (editId.value) {
    await interviewsApi.update(editId.value, form.value)
  } else {
    await interviewsApi.create(form.value)
  }
  showForm.value = false; editId.value = null
  load()
}

async function remove(id: number) {
  if (confirm('確認刪除?')) { await interviewsApi.delete(id); load() }
}

function resultLabel(code: string) {
  const map: Record<string,string> = {
    INTERESTED: '有興趣', NOT_INTERESTED: '無興趣',
    NO_ANSWER: '未接通', CALLBACK: '約回電'
  }
  return map[code] ?? code
}

onMounted(load)
</script>

<style scoped>
.page { padding:1rem }
.page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem }
.filters { display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem }
.filters input, .filters select { padding:.4rem .6rem;border:1px solid #d9d9d9;border-radius:4px }
table { width:100%;border-collapse:collapse;background:#fff }
th,td { padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left }
th { background:#fafafa;font-weight:600 }
button { padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;background:#1890ff;color:#fff }
button:disabled { opacity:.5;cursor:not-allowed }
button.danger { background:#ff4d4f }
.pagination { display:flex;align-items:center;gap:1rem;margin-top:1rem }
.modal-overlay { position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100 }
.modal { background:#fff;padding:2rem;border-radius:8px;min-width:380px;max-height:90vh;overflow-y:auto }
.modal label { display:block;margin:.6rem 0 }
.modal input, .modal select, .modal textarea { display:block;width:100%;padding:.4rem .6rem;border:1px solid #d9d9d9;border-radius:4px;margin-top:.25rem }
.modal-actions { margin-top:1rem;display:flex;gap:.5rem }
</style>
