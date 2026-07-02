<template>
  <div class="page">
    <h2>修改密碼</h2>
    <div class="card">
      <form @submit.prevent="submit">
        <div class="form-group"><label>目前密碼</label><input type="password" v-model="form.current_password" required/></div>
        <div class="form-group"><label>新密碼</label><input type="password" v-model="form.new_password" required/></div>
        <div class="form-group"><label>確認新密碼</label><input type="password" v-model="form.new_password_confirmation" required/></div>
        <button type="submit">修改</button>
        <p v-if="msg" style="margin-top:1rem;color:green">{{msg}}</p>
        <p v-if="error" style="margin-top:1rem;color:red">{{error}}</p>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { personalDataApi } from '@/api/personalData'
const form = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const msg = ref(''); const error = ref('')
async function submit() {
  try { await personalDataApi.changePassword(form.value); msg.value='密碼修改成功'; error.value='' }
  catch(e: unknown) { const err = e as { response?: { data?: { message?: string } } }; error.value = err?.response?.data?.message ?? '修改失敗'; msg.value='' }
}
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