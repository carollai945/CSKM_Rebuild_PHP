<template>
  <div class="page">
    <h2>部門與職稱管理</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div>
        <div class="page-header"><h3>部門</h3><button @click="showDeptForm=true">新增部門</button></div>
        <table><thead><tr><th>ID</th><th>名稱</th><th>操作</th></tr></thead>
          <tbody><tr v-for="d in depts" :key="d.id"><td>{{d.id}}</td><td>{{d.name}}</td>
            <td><button @click="delDept(d.id as number)">刪除</button></td></tr></tbody></table>
        <div v-if="showDeptForm" class="modal-overlay" @click.self="showDeptForm=false">
          <div class="modal"><h3>新增部門</h3><input v-model="deptName" placeholder="部門名稱"/>
            <div class="modal-actions"><button @click="saveDept">儲存</button><button @click="showDeptForm=false">取消</button></div></div></div>
      </div>
      <div>
        <div class="page-header"><h3>職稱</h3><button @click="showTitleForm=true">新增職稱</button></div>
        <table><thead><tr><th>ID</th><th>名稱</th><th>操作</th></tr></thead>
          <tbody><tr v-for="t in titles" :key="t.id"><td>{{t.id}}</td><td>{{t.name}}</td>
            <td><button @click="delTitle(t.id as number)">刪除</button></td></tr></tbody></table>
        <div v-if="showTitleForm" class="modal-overlay" @click.self="showTitleForm=false">
          <div class="modal"><h3>新增職稱</h3><input v-model="titleName" placeholder="職稱名稱"/>
            <div class="modal-actions"><button @click="saveTitle">儲存</button><button @click="showTitleForm=false">取消</button></div></div></div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { departmentsApi, titlesApi } from '@/api/departments'
const depts = ref<Record<string,unknown>[]>([])
const titles = ref<Record<string,unknown>[]>([])
const showDeptForm = ref(false); const deptName = ref('')
const showTitleForm = ref(false); const titleName = ref('')
async function load() {
  const [d,t] = await Promise.all([departmentsApi.list(), titlesApi.list()])
  depts.value = d.data?.data ?? []; titles.value = t.data?.data ?? []
}
async function saveDept() { await departmentsApi.create({ name: deptName.value }); showDeptForm.value=false; deptName.value=''; load() }
async function delDept(id: number) { if(confirm('確認刪除?')) { await departmentsApi.delete(id); load() } }
async function saveTitle() { await titlesApi.create({ name: titleName.value }); showTitleForm.value=false; titleName.value=''; load() }
async function delTitle(id: number) { if(confirm('確認刪除?')) { await titlesApi.delete(id); load() } }
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