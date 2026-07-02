<template>
  <div class="page">
    <h2>E03 請款財務確認</h2>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr><th>ID</th><th>金額</th><th>狀態</th><th>操作</th></tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{r.id}}</td><td>{{r.amount}}</td><td>{{r.status}}</td>
          <td>
            <button @click="confirm(r.id as number)">確認</button>
            <button class="danger" @click="reject(r.id as number)">退回</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup lang="ts">
// 功能編號：E03 請款財務確認
import { ref, onMounted } from 'vue'
import { reimbursementsApi } from '@/api/reimbursements'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
async function load() { loading.value=true; const r=await reimbursementsApi.list({status:'PENDING'}); rows.value=r.data?.data?.data??[]; loading.value=false }
async function confirm(id: number) { await reimbursementsApi.financeConfirm(id); load() }
async function reject(id: number) { await reimbursementsApi.reject(id); load() }
onMounted(load)
</script>
<style scoped>
.page { padding:1rem }
.card { background:#fff;padding:1.5rem;border-radius:8px;max-width:600px }
.form-group { margin-bottom:1rem }
label { display:block;margin-bottom:.25rem;font-weight:500 }
input, select, textarea { width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px;box-sizing:border-box }
button { padding:.5rem 1.5rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer }
.info { margin-bottom:.5rem }.info span { color:#666 }
table { width:100%;border-collapse:collapse;background:#fff }
th,td { padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left }
th { background:#fafafa;font-weight:600 }
.page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem }
.modal-overlay { position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100 }
.modal { background:#fff;padding:2rem;border-radius:8px;min-width:360px }
.modal input,.modal select,.modal textarea { display:block;width:100%;margin:.5rem 0;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px }
.modal-actions { margin-top:1rem;display:flex;gap:.5rem }
button.danger { background:#ff4d4f }
</style>