<template>
  <div class="page">
    <h2>個人報表</h2>
    <div class="page-header">
      <div></div><button @click="showForm=true">新增報表</button>
    </div>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr><th>日期</th><th>類型</th><th>狀態</th><th>操作</th></tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{r.report_date}}</td><td>{{r.report_type}}</td><td>{{r.status}}</td>
          <td><button v-if="r.status==='DRAFT'" @click="submit(r.id as number)">送審</button></td>
        </tr>
      </tbody>
    </table>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal"><h3>新增報表</h3>
        <div><label>類型</label><select v-model="form.report_type"><option value="DAILY">日報</option><option value="WEEKLY">週報</option></select></div>
        <div><label>日期</label><input type="date" v-model="form.report_date"/></div>
        <div><label>內容</label><textarea v-model="form.content" rows="4"/></div>
        <div class="modal-actions"><button @click="save">儲存</button><button @click="showForm=false">取消</button></div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { reportsApi } from '@/api/reports'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false); const showForm = ref(false)
const form = ref<Record<string,unknown>>({ report_type:'DAILY', report_date:'', content:'' })
async function load() { loading.value=true; const r=await reportsApi.list(); rows.value=r.data?.data?.data??[]; loading.value=false }
async function save() { await reportsApi.create(form.value); showForm.value=false; load() }
async function submit(id: number) { await reportsApi.submit(id); load() }
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