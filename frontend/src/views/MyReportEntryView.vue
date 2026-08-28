<template>
  <div class="page">
    <div class="header">
      <h2>{{ title }}</h2>
      <RouterLink to="/me/reports">← 返回 A02</RouterLink>
    </div>

    <div class="card">
      <div class="form-group">
        <label>報表類型</label>
        <input :value="reportTypeLabel(reportType)" readonly />
      </div>
      <div class="form-group">
        <label>填報身份</label>
        <input :value="staffKindLabel(staffKind)" readonly />
      </div>
      <div class="form-group">
        <label>日期</label>
        <input type="date" v-model="form.report_date" :disabled="isViewMode" />
      </div>
      <div class="form-group">
        <label>內容</label>
        <textarea v-model="form.content" rows="8" :readonly="isViewMode" />
      </div>
      <div class="actions">
        <button v-if="!isViewMode" @click="save">儲存</button>
        <button v-if="!isViewMode && reportId > 0" class="secondary" @click="submit">送審</button>
      </div>
      <p v-if="msg" class="success">{{ msg }}</p>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { reportsApi } from '@/api/reports'

type StaffKind = 'advisor' | 'admin'

const route = useRoute()
const router = useRouter()
const routeName = String(route.name ?? '')
const form = ref({ report_date: '', content: '' })
const reportId = ref(0)
const msg = ref('')
const error = ref('')

const reportType = computed<'DAILY' | 'WEEKLY'>(() => {
  if (routeName === 'a020' || routeName === 'a021' || routeName === 'a024' || routeName === 'a025') return 'DAILY'
  return 'WEEKLY'
})
const staffKind = computed<StaffKind>(() => {
  if (routeName === 'a021' || routeName === 'a023' || routeName === 'a025' || routeName === 'a027') return 'admin'
  return 'advisor'
})
const mode = computed(() => String(route.query.mode ?? 'create'))
const isViewMode = computed(() => mode.value === 'view')

const title = computed(() => {
  const codeMap: Record<string, string> = {
    a020: 'A020 學顧日報建立',
    a021: 'A021 行政日報建立',
    a022: 'A022 學顧週報建立',
    a023: 'A023 行政週報建立',
    a024: 'A024 學顧日報檢視/修改',
    a025: 'A025 行政日報檢視/修改',
    a026: 'A026 學顧週報檢視/修改',
    a027: 'A027 行政週報檢視/修改',
  }
  return codeMap[routeName] ?? 'A02 報表明細'
})

function reportTypeLabel(value: 'DAILY' | 'WEEKLY') {
  return value === 'DAILY' ? '日報' : '週報'
}

function staffKindLabel(value: StaffKind) {
  return value === 'advisor' ? '學顧' : '行政'
}

function taggedContent(content: string) {
  if (!content.trim()) return ''
  const prefix = staffKind.value === 'admin' ? '[行政]' : '[學顧]'
  const withoutPrefix = content.replace(/^\[(行政|學顧)\]\s*/, '')
  return `${prefix} ${withoutPrefix}`.trim()
}

async function loadDetail() {
  if (!route.params.id) return
  const id = Number(route.params.id)
  if (!id) return
  const response = await reportsApi.get(id)
  const payload = response.data?.data ?? {}
  reportId.value = id
  form.value = {
    report_date: String(payload.report_date ?? ''),
    content: String(payload.content ?? '').replace(/^\[(行政|學顧)\]\s*/, ''),
  }
}

async function save() {
  msg.value = ''
  error.value = ''
  if (!form.value.report_date) {
    error.value = '請選擇日期'
    return
  }
  const payload = {
    report_type: reportType.value,
    report_date: form.value.report_date,
    content: taggedContent(form.value.content),
  }
  try {
    if (reportId.value > 0) {
      await reportsApi.update(reportId.value, payload)
      msg.value = '儲存成功'
    } else {
      const created = await reportsApi.create(payload)
      reportId.value = Number(created.data?.data?.id ?? 0)
      msg.value = '建立成功'
      if (reportId.value > 0) {
        await router.replace({ name: routeName, params: { id: String(reportId.value) }, query: { mode: 'edit' } })
      }
    }
  } catch (e: unknown) {
    const err = e as { response?: { data?: { message?: string } } }
    error.value = err?.response?.data?.message ?? '儲存失敗'
  }
}

async function submit() {
  if (reportId.value <= 0) return
  await reportsApi.submit(reportId.value)
  await router.push('/me/reports')
}

onMounted(loadDetail)
</script>

<style scoped>
.page { padding: 1rem; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.card { background:#fff; padding:1.25rem; border-radius:8px; max-width:860px; }
.form-group { margin-bottom:1rem; }
label { display:block; font-weight:500; margin-bottom:.35rem; }
input, textarea { width:100%; border:1px solid #d9d9d9; border-radius:4px; padding:.5rem; box-sizing:border-box; }
.actions { display:flex; gap:.5rem; }
button { padding:.45rem 1rem; border:none; border-radius:4px; background:#1890ff; color:#fff; cursor:pointer; }
button.secondary { background:#52c41a; }
.success { color:#389e0d; margin-top:.5rem; }
.error { color:#cf1322; margin-top:.5rem; }
</style>
