<template>
  <div class="page">
    <div class="page-header">
      <h2>D02 公告審核</h2>
      <div v-if="selected.length">
        <button @click="batchApprove" style="background:#52c41a">批次核准({{ selected.length }})</button>
        <button @click="batchRejectModal=true" style="background:#ff4d4f;margin-left:.5rem">批次退回({{ selected.length }})</button>
      </div>
    </div>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr>
        <th><input type="checkbox" @change="toggleAll" :checked="selected.length===rows.length&&rows.length>0"/></th>
        <th>ID</th><th>申請人</th><th>狀態</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td><input type="checkbox" :value="r.id" v-model="selected"/></td>
          <td>{{ r.id }}</td>
          <td>{{ (r.staff as any)?.name ?? r.staff_id }}</td>
          <td>{{ r.status }}</td>
          <td>
            <button @click="approveSingle(r.id as number)">核准</button>
            <button class="danger" @click="rejectOne(r.id as number)">退回</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading&&rows.length===0" style="text-align:center;color:#999;padding:2rem">目前無待批核項目</p>
    <div v-if="batchRejectModal||rejectId!=null" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h3>D02 公告審核</h3>
        <textarea v-model="rejectReason" rows="3" placeholder="請輸入退回原因（選填）"/>
        <div class="modal-actions">
          <button @click="doReject">確認退回</button>
          <button @click="closeModal">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：D02 公告審核
import { ref, onMounted } from 'vue'
import { approvalsApi } from '@/api/approvals'
const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const selected = ref<number[]>([])
const batchRejectModal = ref(false)
const rejectId = ref<number | null>(null)
const rejectReason = ref('')
async function load() {
  loading.value = true
  const r = await approvalsApi.pendingAnnouncement()
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  selected.value = []
  loading.value = false
}
function toggleAll(e: Event) {
  selected.value = (e.target as HTMLInputElement).checked ? rows.value.map(r => r.id as number) : []
}
async function approveSingle(id: number) { await approvalsApi.approveAnnouncement(id); load() }
function rejectOne(id: number) { rejectId.value = id; rejectReason.value = ''; batchRejectModal.value = false }
function closeModal() { batchRejectModal.value = false; rejectId.value = null }
async function batchApprove() { await approvalsApi.batchApproveAnnouncement(selected.value); load() }
async function doReject() {
  if (rejectId.value != null) {
    await approvalsApi.rejectAnnouncement(rejectId.value, rejectReason.value)
    rejectId.value = null
  } else {
    await approvalsApi.batchRejectAnnouncement(selected.value, rejectReason.value)
    batchRejectModal.value = false
  }
  load()
}
onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem }
table { width: 100%; border-collapse: collapse; background: #fff }
th, td { padding: .6rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left }
th { background: #fafafa; font-weight: 600 }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff }
button.danger { background: #ff4d4f }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 360px }
.modal textarea { width: 100%; margin: .5rem 0; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px }
.modal-actions { margin-top: 1rem; display: flex; gap: .5rem }
</style>
