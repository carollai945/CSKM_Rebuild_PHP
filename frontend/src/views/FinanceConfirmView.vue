<template>
  <div class="page">
    <h2>E03 請款財務確認</h2>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr><th>ID</th><th>標題</th><th>金額</th><th>狀態</th><th>操作</th></tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{ r.id }}</td>
          <td>{{ r.title }}</td>
          <td>{{ r.amount }}</td>
          <td>{{ r.status }}</td>
          <td>
            <button @click="doConfirm(r.id as number)">財務確認</button>
            <button class="danger" @click="openRejectModal(r.id as number)">退回</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && rows.length === 0" style="text-align:center;color:#999;padding:2rem">目前無待確認請款單</p>

    <!-- 退回原因 Modal -->
    <div v-if="rejectModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h3>退回請款單</h3>
        <p style="color:#666;margin-bottom:.5rem">退件原因（必填）</p>
        <textarea v-model="rejectReason" rows="4" placeholder="請輸入退件原因..." style="width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px"/>
        <p v-if="rejectError" style="color:#ff4d4f;font-size:.85rem;margin-top:.25rem">{{ rejectError }}</p>
        <div class="modal-actions">
          <button @click="submitReject">確認退回</button>
          <button @click="closeModal">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：E03 請款財務確認
import { ref, onMounted } from 'vue'
import { reimbursementsApi } from '@/api/reimbursements'

const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const rejectModal = ref(false)
const rejectId = ref<number | null>(null)
const rejectReason = ref('')
const rejectError = ref('')

async function load() {
  loading.value = true
  const r = await reimbursementsApi.list({ status: 'PENDING' })
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}

async function doConfirm(id: number) {
  await reimbursementsApi.financeConfirm(id)
  load()
}

function openRejectModal(id: number) {
  rejectId.value = id
  rejectReason.value = ''
  rejectError.value = ''
  rejectModal.value = true
}

function closeModal() {
  rejectModal.value = false
  rejectId.value = null
}

async function submitReject() {
  if (!rejectReason.value.trim()) {
    rejectError.value = '退件原因為必填項目'
    return
  }
  await reimbursementsApi.reject(rejectId.value!, rejectReason.value)
  closeModal()
  load()
}

onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
table { width: 100%; border-collapse: collapse; background: #fff }
th, td { padding: .6rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left }
th { background: #fafafa; font-weight: 600 }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff; margin-right: .5rem }
button.danger { background: #ff4d4f }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 400px }
.modal-actions { margin-top: 1rem; display: flex; gap: .5rem }
</style>
