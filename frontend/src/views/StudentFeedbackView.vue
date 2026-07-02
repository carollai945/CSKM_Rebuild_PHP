<template>
  <div class="page">
    <div class="page-header">
      <h2>D04 學員意見管理</h2>
      <button @click="openCreate">新增意見</button>
    </div>

    <!-- 篩選條件 -->
    <div class="filters">
      <input v-model="filters.keyword" placeholder="學員姓名搜尋" @change="load" />
      <select v-model="filters.status" @change="load">
        <option value="">全部狀態</option>
        <option value="OPEN">待處理</option>
        <option value="IN_PROGRESS">處理中</option>
        <option value="RESOLVED">已結案</option>
      </select>
      <input type="date" v-model="filters.from" @change="load" />
      <input type="date" v-model="filters.to" @change="load" />
      <button @click="resetFilters">重置</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr>
        <th>ID</th><th>學員</th><th>意見摘要</th><th>狀態</th><th>日期</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td>
          <td>{{ (row.student as any)?.name ?? '-' }}</td>
          <td>{{ String(row.content ?? '').slice(0, 40) }}{{ String(row.content ?? '').length > 40 ? '...' : '' }}</td>
          <td><span :class="'badge badge-' + row.status">{{ statusLabel(row.status as string) }}</span></td>
          <td>{{ String(row.created_at ?? '').slice(0, 10) }}</td>
          <td>
            <RouterLink :to="`/student-feedbacks/${row.id}`"><button>明細</button></RouterLink>
            <button class="danger" @click="remove(row.id as number)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && rows.length === 0" style="text-align:center;color:#999;padding:2rem">無符合條件的意見記錄</p>

    <!-- 新增 Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>新增學員意見</h3>
        <form @submit.prevent="save">
          <div><label>學員 ID</label><input v-model="form.student_id" type="number" placeholder="學員 ID" required /></div>
          <div><label>意見分類</label><input v-model="form.category" placeholder="分類（選填）" /></div>
          <div><label>意見內容</label><textarea v-model="form.content" rows="4" required/></div>
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
// 功能編號：D04 學員意見管理
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { studentFeedbacksApi as feedbackApi } from '@/api/studentFeedbacks'

const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const form = ref<Record<string, unknown>>({})
const filters = ref({ keyword: '', status: '', from: '', to: '' })

async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.from) params.from = filters.value.from
  if (filters.value.to) params.to = filters.value.to
  const r = await feedbackApi.list(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}

function resetFilters() { filters.value = { keyword: '', status: '', from: '', to: '' }; load() }
function openCreate() { form.value = {}; showForm.value = true }
async function save() { await feedbackApi.create(form.value); showForm.value = false; load() }
async function remove(id: number) { if (confirm('確認刪除?')) { await feedbackApi.delete(id); load() } }
function statusLabel(s: string) {
  return { OPEN: '待處理', IN_PROGRESS: '處理中', RESOLVED: '已結案' }[s] ?? s
}
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
.badge { padding: .2rem .6rem; border-radius: 4px; font-size: .85rem }
.badge-OPEN { background: #fff7e6; color: #d46b08 }
.badge-IN_PROGRESS { background: #e6f7ff; color: #0958d9 }
.badge-RESOLVED { background: #f6ffed; color: #389e0d }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff; margin-right: .25rem }
button.danger { background: #ff4d4f }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 360px }
.modal label { display: block; margin-bottom: .25rem; font-weight: 500 }
.modal input, .modal select, .modal textarea { width: 100%; margin-bottom: .75rem; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px; box-sizing: border-box }
.modal-actions { display: flex; gap: .5rem }
</style>
