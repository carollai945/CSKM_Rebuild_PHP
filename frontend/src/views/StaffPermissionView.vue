<template>
  <div class="page">
    <div class="page-header">
      <h2>人員權限管理 — {{ staffName }}</h2>
      <RouterLink to="/staff">← 返回人員列表</RouterLink>
    </div>
    <div v-if="loading" class="loading">載入中...</div>
    <div v-else class="card">
      <div class="form-group">
        <label>角色</label>
        <select v-model="form.role">
          <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
        </select>
      </div>
      <div class="form-group">
        <label>功能模組存取權限</label>
        <div class="module-grid">
          <label v-for="mod in modules" :key="mod" class="checkbox-label">
            <input type="checkbox" :value="mod" v-model="form.modules" />
            {{ mod }}
          </label>
        </div>
        <div style="margin-top:.5rem">
          <button type="button" @click="selectAll" style="background:#52c41a">全選</button>
          <button type="button" @click="form.modules=[]" style="margin-left:.5rem;background:#ff4d4f">全部取消</button>
        </div>
      </div>
      <button @click="save">儲存權限</button>
      <p v-if="msg" style="color:green;margin-top:.5rem">{{ msg }}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import api from '@/api/axios'
const route = useRoute()
const staffId = route.params.id as string
const staffName = ref('')
const loading = ref(false)
const msg = ref('')
const roles = ref<{value:string;label:string}[]>([])
const modules = ref<string[]>([])
const form = ref<{role:string;modules:string[]}>({ role:'', modules:[] })
async function load() {
  loading.value=true
  const [perms, groups] = await Promise.all([
    api.get(`/staff/${staffId}/permissions`),
    api.get('/permission-groups'),
  ])
  const d = perms.data?.data ?? {}
  staffName.value = d.staff_name ?? ''
  form.value = { role: d.role ?? '', modules: d.modules ?? [] }
  roles.value = groups.data?.data?.roles ?? []
  modules.value = groups.data?.data?.modules ?? []
  loading.value=false
}
function selectAll() { form.value.modules = [...modules.value] }
async function save() {
  await api.put(`/staff/${staffId}/permissions`, form.value)
  msg.value='儲存成功'; setTimeout(()=>msg.value='',3000)
}
onMounted(load)
</script>
<style scoped>
.page{padding:1rem}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.card{background:#fff;padding:1.5rem;border-radius:8px;max-width:700px}
.form-group{margin-bottom:1.5rem}label{display:block;margin-bottom:.5rem;font-weight:500}
select{width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px}
.module-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:.5rem}
.checkbox-label{display:flex;align-items:center;gap:.25rem;font-weight:400;cursor:pointer}
button{padding:.4rem 1rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer}
.loading{padding:2rem;text-align:center;color:#999}
</style>
