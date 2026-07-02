<template>
  <div class="page">
    <h2>請款單申請</h2>
    <div class="page-header"><div></div><button @click="showForm=true">新增請款單</button></div>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr><th>標題</th><th>金額</th><th>狀態</th><th>操作</th></tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{r.title}}</td><td>{{r.amount}}</td><td>{{r.status}}</td>
          <td><button v-if="r.status==='PENDING'" class="danger" @click="del(r.id as number)">刪除</button></td>
        </tr>
      </tbody>
    </table>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal"><h3>新增請款單</h3>
        <div><label>標題</label><input v-model="form.title"/></div>
        <div><label>金額</label><input type="number" v-model="form.amount"/></div>
        <div><label>說明</label><textarea v-model="form.description" rows="3"/></div>
        <div class="modal-actions"><button @click="save">送出</button><button @click="showForm=false">取消</button></div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { invoiceRequestsApi } from '@/api/applications'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false); const showForm = ref(false)
const form = ref<Record<string,unknown>>({ title:'', amount:0, description:'' })
async function load() { loading.value=true; const r=await invoiceRequestsApi.list(); rows.value=r.data?.data?.data??[]; loading.value=false }
async function save() { await invoiceRequestsApi.create(form.value); showForm.value=false; load() }
async function del(id: number) { if(confirm('確認刪除?')) { await invoiceRequestsApi.delete(id); load() } }
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