<template>
  <div class="page">
    <h2>個人資料維護</h2>
    <div class="card">
      <form @submit.prevent="save">
        <div class="form-group"><label>姓名</label><input v-model="form.name" placeholder="姓名"/></div>
        <div class="form-group"><label>電話</label><input v-model="form.phone" placeholder="電話"/></div>
        <div class="form-group"><label>性別</label>
          <select v-model="form.gender"><option value="">選擇</option><option value="M">男</option><option value="F">女</option></select></div>
        <div class="form-group"><label>生日</label><input type="date" v-model="form.birth_date"/></div>
        <button type="submit">儲存</button>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { personalDataApi } from '@/api/personalData'
const form = ref<Record<string,unknown>>({})
async function load() { const r = await personalDataApi.get(); form.value = r.data?.data ?? {} }
async function save() { await personalDataApi.update(form.value); alert('儲存成功') }
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