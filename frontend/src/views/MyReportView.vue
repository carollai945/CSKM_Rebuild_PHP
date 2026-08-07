<template>
  <div class="page">
    <h2>A02 個人報表</h2>
    <div class="page-header">
      <div></div><button @click="showForm=true">新增報表</button>
    </div>
    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr><th>日期</th><th>類型</th><th>狀態</th><th>內容</th><th>操作</th></tr></thead>
      <tbody>
        <tr v-for="r in rows" :key="r.id">
          <td>{{r.report_date}}</td><td>{{reportTypeLabel(r.report_type as string)}}</td><td>{{reportStatusLabel(r.status as string)}}</td>
          <td>{{(r.content as string) || '-'}}</td>
          <td>
            <button @click="view(r.id as number)">檢視</button>
            <button v-if="r.status==='DRAFT'" @click="submit(r.id as number)">送審</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal"><h3>A02 個人報表</h3>
        <div><label>類型</label><select v-model="form.report_type"><option value="DAILY">{{ reportTypeLabel('DAILY') }}</option><option value="WEEKLY">{{ reportTypeLabel('WEEKLY') }}</option></select></div>
        <div><label>日期</label><input type="date" v-model="form.report_date"/></div>
        <div><label>內容</label><textarea v-model="form.content" rows="4"/></div>
        <div class="modal-actions"><button @click="save">儲存</button><button @click="showForm=false">取消</button></div>
      </div>
    </div>
    <div v-if="showDetail" class="modal-overlay" @click.self="showDetail=false">
      <div class="modal">
        <h3>報表內容</h3>
        <div><label>日期</label><div>{{detail.report_date}}</div></div>
        <div><label>類型</label><div>{{reportTypeLabel(detail.report_type as string)}}</div></div>
        <div><label>狀態</label><div>{{reportStatusLabel(detail.status as string)}}</div></div>
        <div><label>內容</label><pre class="content">{{detail.content || '-'}}</pre></div>
        <div class="modal-actions"><button @click="showDetail=false">關閉</button></div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：A02 個人報表
import { ref, onMounted } from 'vue'
import { reportsApi } from '@/api/reports'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false); const showForm = ref(false)
const form = ref<Record<string,unknown>>({ report_type:'DAILY', report_date:'', content:'' })
const showDetail = ref(false)
const detail = ref<Record<string,unknown>>({})
const locale = (typeof window !== 'undefined' && (window.localStorage.getItem('locale') || navigator.language))
  ? String((window.localStorage.getItem('locale') || navigator.language)).toLowerCase()
  : 'zh-tw'
const isZh = locale.startsWith('zh')

function reportTypeLabel(value: string): string {
  const map = {
    DAILY: isZh ? '日報' : 'Daily',
    WEEKLY: isZh ? '週報' : 'Weekly',
  } as const
  return map[value as keyof typeof map] ?? value
}

function reportStatusLabel(value: string): string {
  const map = {
    DRAFT: isZh ? '草稿' : 'Draft',
    SUBMITTED: isZh ? '已送審' : 'Submitted',
    APPROVED: isZh ? '已核准' : 'Approved',
    REJECTED: isZh ? '已退回' : 'Rejected',
  } as const
  return map[value as keyof typeof map] ?? value
}
async function load() { loading.value=true; const r=await reportsApi.list(); rows.value=r.data?.data?.data??[]; loading.value=false }
async function save() {
  const r = await reportsApi.create(form.value)
  const created = r.data?.data ?? {}
  form.value = { report_type:'DAILY', report_date:'', content:'' }
  showForm.value = false
  await load()
  if (created.id) await view(created.id as number)
}
async function submit(id: number) { await reportsApi.submit(id); load() }
async function view(id: number) {
  const r = await reportsApi.get(id)
  detail.value = r.data?.data ?? {}
  showDetail.value = true
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
.content { white-space:pre-wrap;background:#fafafa;padding:.5rem;border:1px solid #eee;border-radius:4px }
button.danger { background:#ff4d4f }
</style>