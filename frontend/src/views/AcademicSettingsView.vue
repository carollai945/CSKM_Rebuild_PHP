<template>
  <div class="page">
    <h2>C00 課程/機構總覽</h2>
    <div class="tabs">
      <button :class="{active:tab==='institute'}" @click="tab='institute'">機構</button>
      <button :class="{active:tab==='course'}" @click="tab='course'">課程</button>
      <button :class="{active:tab==='subject'}" @click="tab='subject'">科目</button>
    </div>
    <div v-if="tab==='institute'">
      <div class="page-header"><h3>C00 課程/機構總覽</h3><button @click="showForm('institute')">新增</button></div>
      <table><thead><tr><th>ID</th><th>名稱</th><th>操作</th></tr></thead>
        <tbody><tr v-for="r in institutes" :key="r.id"><td>{{r.id}}</td><td>{{r.name}}</td>
          <td><button @click="del('institute',r.id as number)">刪除</button></td></tr></tbody></table>
    </div>
    <div v-if="tab==='course'">
      <div class="page-header"><h3>C00 課程/機構總覽</h3><button @click="showForm('course')">新增</button></div>
      <table><thead><tr><th>ID</th><th>名稱</th><th>操作</th></tr></thead>
        <tbody><tr v-for="r in courses" :key="r.id"><td>{{r.id}}</td><td>{{r.name}}</td>
          <td><button @click="del('course',r.id as number)">刪除</button></td></tr></tbody></table>
    </div>
    <div v-if="tab==='subject'">
      <div class="page-header"><h3>C00 課程/機構總覽</h3><button @click="showForm('subject')">新增</button></div>
      <table><thead><tr><th>ID</th><th>名稱</th><th>操作</th></tr></thead>
        <tbody><tr v-for="r in subjects" :key="r.id"><td>{{r.id}}</td><td>{{r.name}}</td>
          <td><button @click="del('subject',r.id as number)">刪除</button></td></tr></tbody></table>
    </div>
    <div v-if="activeForm" class="modal-overlay" @click.self="activeForm=null">
      <div class="modal"><h3>C00 課程/機構總覽</h3><input v-model="formName" placeholder="名稱"/>
        <div class="modal-actions"><button @click="save">儲存</button><button @click="activeForm=null">取消</button></div></div></div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：C00 課程/機構總覽
import { ref, onMounted } from 'vue'
import { institutesApi, coursesApi, subjectsApi } from '@/api/institutes'
const tab = ref('institute')
const institutes = ref<Record<string,unknown>[]>([])
const courses = ref<Record<string,unknown>[]>([])
const subjects = ref<Record<string,unknown>[]>([])
const activeForm = ref<string|null>(null); const formName = ref('')
async function load() {
  const [i,c,s] = await Promise.all([institutesApi.list(),coursesApi.list(),subjectsApi.list()])
  institutes.value=i.data?.data??[]; courses.value=c.data?.data??[]; subjects.value=s.data?.data??[]
}
function showForm(t: string) { activeForm.value=t; formName.value='' }
async function save() {
  const apis: Record<string,typeof institutesApi> = {institute:institutesApi,course:coursesApi,subject:subjectsApi}
  await apis[activeForm.value!].create({name:formName.value}); activeForm.value=null; load()
}
async function del(type: string, id: number) {
  if(!confirm('確認刪除?')) return
  const apis: Record<string,typeof institutesApi> = {institute:institutesApi,course:coursesApi,subject:subjectsApi}
  await apis[type].delete(id); load()
}
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