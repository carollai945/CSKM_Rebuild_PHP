<template>
  <div class="page">
    <div class="page-header">
      <h2>F02 人員管理</h2>
      <button @click="showForm = true">新增</button>
    </div>
    <div v-if="loading" class="loading">載入中...</div>
    <table v-else>
      <thead><tr>
        <th>ID</th><th>姓名</th><th>狀態</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td><td>{{ row.name }}</td><td>{{ row.status }}</td>
          <td>
            <button @click="edit(row)">編輯</button>
            <button @click="openReset(row)" style="margin-left:.25rem;background:#fa8c16">重設密碼</button>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- Create/Edit Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>F02 人員管理</h3>
        <form @submit.prevent="save">
          <div><label>姓名</label><input v-model="form.name" placeholder="姓名" /></div>
          <div><label>電話</label><input v-model="form.phone" placeholder="電話" /></div>
          <div><label>到職日</label><input type="date" v-model="form.join_date" /></div>
          <div class="modal-actions">
            <button type="submit">儲存</button>
            <button type="button" @click="showForm = false">取消</button>
          </div>
        </form>
      </div>
    </div>
    <!-- Reset Password Modal -->
    <div v-if="resetModal" class="modal-overlay" @click.self="resetModal = false">
      <div class="modal">
        <h3>F02 人員管理</h3>
        <div><label>新密碼</label><input type="password" v-model="resetForm.new_password" /></div>
        <div><label>確認新密碼</label><input type="password" v-model="resetForm.new_password_confirmation" /></div>
        <p v-if="resetMsg" style="color:green">{{ resetMsg }}</p>
        <p v-if="resetError" style="color:red">{{ resetError }}</p>
        <div class="modal-actions">
          <button @click="doReset">確認重設</button>
          <button @click="resetModal = false">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：F02 人員管理
import { ref, onMounted } from 'vue'
import { staffApi } from '@/api/staff'
import api from '@/api/axios'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number|null>(null)
const form = ref<Record<string,unknown>>({})
const resetModal = ref(false)
const resetStaff = ref<Record<string,unknown>|null>(null)
const resetForm = ref({ new_password: '', new_password_confirmation: '' })
const resetMsg = ref(''); const resetError = ref('')
async function load() { loading.value=true; const r = await staffApi.list(); rows.value = r.data?.data?.data ?? r.data?.data ?? []; loading.value=false }
function edit(row: Record<string,unknown>) { editId.value = row.id as number; form.value = {...row}; showForm.value = true }
async function save() { if (editId.value) await staffApi.update(editId.value, form.value); else await staffApi.create(form.value); showForm.value=false; editId.value=null; form.value={}; load() }
function openReset(row: Record<string,unknown>) { resetStaff.value=row; resetForm.value={new_password:'',new_password_confirmation:''}; resetMsg.value=''; resetError.value=''; resetModal.value=true }
async function doReset() {
  try {
    await api.post('/auth/reset-password', { staff_id: resetStaff.value?.id, ...resetForm.value })
    resetMsg.value='密碼重設成功'; setTimeout(()=>{ resetModal.value=false },1500)
  } catch(e: unknown) { const err = e as {response?:{data?:{message?:string}}}; resetError.value=err?.response?.data?.message??'重設失敗' }
}
onMounted(load)
</script>
<style scoped>
.page{padding:1rem}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:.6rem 1rem;border-bottom:1px solid #f0f0f0;text-align:left}th{background:#fafafa;font-weight:600}
.modal-overlay{position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;z-index:100}
.modal{background:#fff;padding:2rem;border-radius:8px;min-width:360px}
.modal input{display:block;width:100%;margin:.5rem 0;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px}
.modal-actions{margin-top:1rem;display:flex;gap:.5rem}
button{padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;background:#1890ff;color:#fff}
button:last-child{background:#fff;color:#333;border:1px solid #d9d9d9}.loading{padding:2rem;text-align:center;color:#999}
label{display:block;margin-bottom:.25rem;font-weight:500}
</style>
