<template>
  <div class="page">
    <h2>F04 電訪名單匯入</h2>
    <div class="card">
      <div class="form-group">
        <label>選擇 CSV/Excel 檔案</label>
        <input type="file" accept=".csv,.xlsx,.xls" @change="onFile"/>
      </div>
      <button @click="upload" :disabled="!file">上傳匯入</button>
      <p v-if="msg" style="margin-top:1rem;color:green">{{msg}}</p>
      <p v-if="error" style="margin-top:1rem;color:red">{{error}}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：F04 電訪名單匯入
import { ref, onMounted } from 'vue'
import { leadsApi } from '@/api/leads'
const file = ref<File|null>(null); const msg = ref(''); const error = ref('')
function onFile(e: Event) { const t = e.target as HTMLInputElement; file.value = t.files?.[0] ?? null }
async function upload() {
  if (!file.value) return
  try { await leadsApi.import(file.value); msg.value='匯入成功'; error.value='' }
  catch(e: unknown) { const err = e as { response?: { data?: { message?: string } } }; error.value = err?.response?.data?.message ?? '匯入失敗'; msg.value='' }
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