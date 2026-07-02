<template>
  <div class="page">
    <div class="page-header">
      <h2>學員明細 — {{ form.name }}</h2>
      <RouterLink to="/students">← 返回列表</RouterLink>
    </div>
    <div v-if="loading" class="loading">載入中...</div>
    <div v-else class="card">
      <h3>基本資料</h3>
      <div class="grid-2">
        <div class="form-group"><label>姓名 <span class="req">*</span></label><input v-model="form.name" required /></div>
        <div class="form-group"><label>學號</label><input :value="form.student_no" readonly style="background:#fafafa"/></div>
        <div class="form-group"><label>性別</label>
          <select v-model="form.gender"><option value="">選擇</option><option value="M">男</option><option value="F">女</option></select></div>
        <div class="form-group"><label>手機 <span class="req">*</span></label><input v-model="form.phone" /></div>
        <div class="form-group"><label>電話</label><input v-model="form.tel" /></div>
        <div class="form-group"><label>Email</label><input type="email" v-model="form.email" /></div>
        <div class="form-group"><label>生日</label><input type="date" v-model="form.birth_date" /></div>
        <div class="form-group"><label>地址</label><input v-model="form.address" /></div>
      </div>

      <h3 style="margin-top:1.5rem">個人背景</h3>
      <div class="grid-2">
        <div class="form-group"><label>學歷</label><input v-model="form.education" /></div>
        <div class="form-group"><label>職業</label><input v-model="form.occupation" /></div>
        <div class="form-group"><label>公司</label><input v-model="form.company" /></div>
      </div>

      <h3 style="margin-top:1.5rem">課程與學顧</h3>
      <div class="grid-2">
        <div class="form-group">
          <label>機構</label>
          <select v-model="selectedInstitute" @change="onInstituteChange">
            <option value="">選擇機構</option>
            <option v-for="i in institutes" :key="i.id" :value="i.id">{{ i.name }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>學顧</label>
          <select v-model="form.advisor_id">
            <option value="">選擇學顧</option>
            <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>可選課程（多選）</label>
        <div class="course-grid">
          <label v-for="c in availableCourses" :key="c.id" class="checkbox-label">
            <input type="checkbox" :value="c.id" v-model="selectedCourses" />
            {{ c.name }}
          </label>
        </div>
        <div v-if="selectedCourses.length" style="margin-top:.5rem;color:#666;font-size:.85rem">
          已選：{{ selectedCourses.length }} 門課程
        </div>
      </div>

      <h3 style="margin-top:1.5rem">建檔資訊</h3>
      <div class="grid-2">
        <div class="form-group"><label>建檔日期</label><input type="date" v-model="form.enrolled_date" /></div>
        <div class="form-group"><label>下次聯繫日</label><input type="date" v-model="form.next_contact_date" /></div>
        <div class="form-group" style="grid-column:span 2"><label>備註</label><textarea v-model="form.notes" rows="3" /></div>
      </div>

      <div class="actions">
        <button @click="save">確認儲存</button>
        <RouterLink to="/students"><button type="button" style="margin-left:.5rem;background:#fff;color:#333;border:1px solid #d9d9d9">返回列表</button></RouterLink>
      </div>
      <p v-if="msg" style="color:green;margin-top:.5rem">{{ msg }}</p>
      <p v-if="error" style="color:red;margin-top:.5rem">{{ error }}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import api from '@/api/axios'
const route = useRoute()
const studentId = route.params.id as string
const loading = ref(false)
const msg = ref(''); const error = ref('')
const form = ref<Record<string,unknown>>({})
const institutes = ref<Record<string,unknown>[]>([])
const availableCourses = ref<Record<string,unknown>[]>([])
const staffList = ref<Record<string,unknown>[]>([])
const selectedInstitute = ref<number|null>(null)
const selectedCourses = ref<number[]>([])
async function load() {
  loading.value=true
  const [student, instResp, staffResp] = await Promise.all([
    api.get(`/students/${studentId}`),
    api.get('/institutes'),
    api.get('/staff/autocomplete'),
  ])
  const d = student.data?.data ?? {}
  form.value = { ...d }
  institutes.value = instResp.data?.data ?? []
  staffList.value = staffResp.data?.data ?? []
  const coursesResp = await api.get(`/students/${studentId}/courses`)
  selectedCourses.value = (coursesResp.data?.data ?? []).map((c: Record<string,unknown>) => c.id as number)
  loading.value=false
}
async function onInstituteChange() {
  if (!selectedInstitute.value) { availableCourses.value=[]; return }
  const r = await api.get('/courses', { params: { institute_id: selectedInstitute.value } })
  availableCourses.value = r.data?.data ?? []
}
async function save() {
  try {
    await Promise.all([
      api.put(`/students/${studentId}`, form.value),
      api.put(`/students/${studentId}/courses`, { course_ids: selectedCourses.value }),
    ])
    msg.value='儲存成功'; error.value=''; setTimeout(()=>msg.value='',3000)
  } catch(e: unknown) {
    const err = e as {response?:{data?:{message?:string}}}
    error.value = err?.response?.data?.message ?? '儲存失敗'
  }
}
onMounted(load)
</script>
<style scoped>
.page{padding:1rem}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.card{background:#fff;padding:1.5rem;border-radius:8px;max-width:900px}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-group{margin-bottom:.75rem}label{display:block;margin-bottom:.25rem;font-weight:500}
input,select,textarea{width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px;box-sizing:border-box}
.req{color:red}.course-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.4rem}
.checkbox-label{display:flex;align-items:center;gap:.25rem;font-weight:400;cursor:pointer}
.actions{margin-top:1.5rem;display:flex;align-items:center}
button{padding:.5rem 1.5rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer}
.loading{padding:2rem;text-align:center;color:#999}h3{margin:0 0 .75rem;color:#1890ff}
</style>
