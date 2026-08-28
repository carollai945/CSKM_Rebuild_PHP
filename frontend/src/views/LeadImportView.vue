<template>
  <div class="page">
    <h2>F04 電訪名單匯入</h2>

    <div class="card">
      <h3>格式說明</h3>
      <ul>
        <li>支援格式：CSV / XLS / XLSX</li>
        <li>必要欄位：name（姓名）、phone（電話）</li>
        <li>選填欄位：source_code（來源代碼）</li>
      </ul>
    </div>

    <div class="card">
      <div class="form-group">
        <label>選擇 CSV/Excel 檔案</label>
        <input type="file" accept=".csv,.xlsx,.xls" @change="onFile"/>
      </div>
      <button @click="upload" :disabled="!file || uploading">上傳匯入</button>
      <p v-if="msg" class="success">{{ msg }}</p>
      <p v-if="error" class="error">{{ error }}</p>
    </div>

    <div v-if="result" class="card">
      <h3>匯入結果</h3>
      <div class="result-grid">
        <div><strong>狀態：</strong>{{ result.status }}</div>
        <div><strong>總筆數：</strong>{{ result.total }}</div>
        <div><strong>成功筆數：</strong>{{ successCount }}</div>
        <div><strong>失敗筆數：</strong>{{ failureCount }}</div>
      </div>

      <table v-if="result.errors.length > 0" class="error-table">
        <thead>
          <tr><th>錯誤明細</th></tr>
        </thead>
        <tbody>
          <tr v-for="(err, idx) in result.errors" :key="`import-error-${idx}`">
            <td>{{ err }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue'
import { leadsApi } from '@/api/leads'

type ImportResult = {
  id: number
  status: string
  total: number
  processed: number
  errors: string[]
}

const file = ref<File|null>(null)
const msg = ref('')
const error = ref('')
const uploading = ref(false)
const result = ref<ImportResult | null>(null)

const failureCount = computed(() => result.value?.errors.length ?? 0)
const successCount = computed(() => {
  if (!result.value) return 0
  return Math.max(0, Number(result.value.processed) - failureCount.value)
})

function onFile(e: Event) {
  const target = e.target as HTMLInputElement
  file.value = target.files?.[0] ?? null
}

async function waitForImportResult(jobId: number) {
  const maxAttempts = 20
  for (let attempt = 0; attempt < maxAttempts; attempt++) {
    const statusResponse = await leadsApi.importStatus(jobId)
    const payload = statusResponse.data?.data ?? {}
    const current: ImportResult = {
      id: Number(payload.id ?? jobId),
      status: String(payload.status ?? 'PENDING'),
      total: Number(payload.total ?? 0),
      processed: Number(payload.processed ?? 0),
      errors: Array.isArray(payload.errors) ? payload.errors.map((item: unknown) => String(item)) : [],
    }
    result.value = current
    if (current.status === 'DONE' || current.status === 'FAILED') return current
    await new Promise((resolve) => window.setTimeout(resolve, 1200))
  }
  return result.value
}

async function upload() {
  if (!file.value) return
  uploading.value = true
  msg.value = ''
  error.value = ''
  result.value = null
  try {
    const submitResponse = await leadsApi.import(file.value)
    const jobId = Number(submitResponse.data?.job_id)
    if (!jobId) {
      throw new Error('匯入任務建立失敗')
    }
    const finalResult = await waitForImportResult(jobId)
    if (finalResult?.status === 'FAILED') {
      error.value = '匯入失敗，請檢查錯誤明細'
    } else if ((finalResult?.errors.length ?? 0) > 0) {
      msg.value = '匯入完成（部分成功）'
    } else {
      msg.value = '匯入成功'
    }
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } }; message?: string }
    error.value = err?.response?.data?.message ?? err?.message ?? '匯入失敗'
  } finally {
    uploading.value = false
  }
}
</script>
<style scoped>
.page { padding:1rem }
.card { background:#fff;padding:1.25rem;border-radius:8px;max-width:860px;margin-bottom:1rem }
.form-group { margin-bottom:1rem }
label { display:block;margin-bottom:.25rem;font-weight:500 }
input { width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px;box-sizing:border-box }
button { padding:.5rem 1.25rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer }
button:disabled { opacity:.5;cursor:not-allowed }
ul { margin: .5rem 0 0 1rem; color:#555 }
.success { margin-top:1rem;color:#389e0d }
.error { margin-top:1rem;color:#cf1322 }
.result-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.5rem;margin-top:.5rem }
.error-table { margin-top:1rem;width:100%;border-collapse:collapse }
.error-table th,.error-table td { border-bottom:1px solid #f0f0f0;padding:.6rem;text-align:left }
.error-table th { background:#fafafa;font-weight:600 }
@media (max-width: 768px) {
  .result-grid { grid-template-columns: 1fr; }
}
</style>
