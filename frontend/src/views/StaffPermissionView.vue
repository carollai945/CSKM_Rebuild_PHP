<template>
  <div class="page">
    <div class="page-header">
      <h2>F02 人員權限管理</h2>
      <RouterLink to="/staff">← 返回人員列表</RouterLink>
    </div>

    <section class="card">
      <h3>查詢條件</h3>
      <div class="filter-grid">
        <div class="form-group">
          <label>姓名 autocomplete</label>
          <input
            v-model.trim="search.keyword"
            placeholder="輸入姓名或員工編號"
            @input="onSearchKeywordInput"
          />
          <div v-if="candidateStaff.length > 0" class="autocomplete">
            <button
              v-for="item in candidateStaff"
              :key="item.id"
              type="button"
              class="autocomplete-item"
              @click="selectStaff(item)"
            >
              {{ item.staff_no }} - {{ item.name }}
            </button>
          </div>
        </div>
        <div class="form-group">
          <label>區域</label>
          <select v-model="search.region_id" @change="onRegionChange">
            <option value="">全部</option>
            <option v-for="region in regions" :key="region.id" :value="region.id">{{ region.name }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>部門</label>
          <select v-model="search.department_id">
            <option value="">全部</option>
            <option v-for="dep in filteredDepartments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="button" @click="queryStaff">查詢</button>
        </div>
      </div>
    </section>

    <section v-if="selectedStaff" class="card">
      <h3>人員資訊</h3>
      <div class="info-grid">
        <div><strong>人員：</strong>{{ selectedStaff.name }}</div>
        <div><strong>員工編號：</strong>{{ selectedStaff.staff_no }}</div>
        <div><strong>區域：</strong>{{ selectedStaff.region?.name ?? '—' }}</div>
        <div><strong>部門：</strong>{{ selectedStaff.department?.name ?? '—' }}</div>
        <div><strong>職稱：</strong>{{ selectedStaff.title?.name ?? '—' }}</div>
        <div><strong>到職日期：</strong>{{ selectedStaff.join_date ?? '—' }}</div>
        <div><strong>離職日期：</strong>{{ selectedStaff.leave_date ?? '—' }}</div>
      </div>
    </section>

    <div v-if="loading" class="loading">載入中...</div>
    <div v-else class="card">
      <div class="form-group">
        <label>角色</label>
        <select v-model="form.role">
          <option v-for="r in roles" :key="r.value" :value="r.value">{{ roleLabel(r.value, r.label) }}</option>
        </select>
      </div>

      <div class="form-group">
        <label>管理部門（showDepByReg）</label>
        <div class="module-actions">
          <button type="button" @click="selectAllManagedDepartments" style="background:#52c41a">全選</button>
          <button type="button" @click="managedDepartmentIds = []" style="background:#ff4d4f">全部取消</button>
        </div>
        <div class="module-grid">
          <label v-for="dep in filteredDepartments" :key="`managed-${dep.id}`" class="checkbox-label">
            <input type="checkbox" :value="dep.id" v-model="managedDepartmentIds" />
            {{ dep.name }}
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>功能模組存取權限</label>
        <div class="module-actions">
          <button type="button" @click="selectAllModules" style="background:#52c41a">全選</button>
          <button type="button" @click="form.modules=[]" style="background:#ff4d4f">全部取消</button>
        </div>
        <div class="module-grid">
          <label v-for="mod in modules" :key="mod" class="checkbox-label">
            <input type="checkbox" :value="mod" v-model="form.modules" />
            {{ mod }}
          </label>
        </div>
      </div>

      <button @click="save">儲存權限</button>
      <p v-if="msg" class="success">{{ msg }}</p>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { permissionsApi } from '@/api/permissions'
import { regionsApi } from '@/api/regions'
import { departmentsApi } from '@/api/departments'
import { staffApi } from '@/api/staff'

type StaffCandidate = { id: number; name: string; staff_no: string }
type RegionRow = { id: number; name: string }
type DepartmentRow = { id: number; name: string; region_id?: number | null }
type StaffInfo = {
  id: number
  name: string
  staff_no: string
  join_date?: string | null
  leave_date?: string | null
  region?: { id?: number; name?: string } | null
  department?: { id?: number; name?: string } | null
  title?: { id?: number; name?: string } | null
}

const route = useRoute()
const loading = ref(false)
const msg = ref('')
const error = ref('')
const roles = ref<{ value: string; label: string }[]>([])
const modules = ref<string[]>([])
const regions = ref<RegionRow[]>([])
const departments = ref<DepartmentRow[]>([])
const candidateStaff = ref<StaffCandidate[]>([])
const selectedStaff = ref<StaffInfo | null>(null)
const selectedStaffId = ref(0)

const search = ref({ keyword: '', region_id: '', department_id: '' })
const managedDepartmentIds = ref<number[]>([])
const form = ref<{ role: string; modules: string[] }>({ role: '', modules: [] })

const routeStaffId = Number(route.params.id)

const filteredDepartments = computed(() => {
  if (!search.value.region_id) return departments.value
  const regionId = Number(search.value.region_id)
  return departments.value.filter((dep) => Number(dep.region_id ?? 0) === regionId)
})

function roleLabel(role: string, fallback: string) {
  const map: Record<string, string> = {
    ceo: '執行長',
    regmgr: '區域主管',
    staff: '行政人員',
    teacher: '部門主管',
    finance: '財務主管',
    admin: '系統管理員',
  }
  return map[role] ?? fallback
}

function normalizeRows<T>(payload: unknown): T[] {
  if (Array.isArray(payload)) return payload as T[]
  if (payload && typeof payload === 'object' && Array.isArray((payload as { data?: unknown[] }).data)) {
    return (payload as { data: T[] }).data
  }
  return []
}

async function loadPermissionContext() {
  const [groupsRes, regionsRes, departmentsRes] = await Promise.all([
    permissionsApi.getPermissionGroups(),
    regionsApi.list(),
    departmentsApi.list(),
  ])
  roles.value = groupsRes.data?.data?.roles ?? []
  modules.value = groupsRes.data?.data?.modules ?? []
  regions.value = normalizeRows<RegionRow>(regionsRes.data?.data)
  departments.value = normalizeRows<DepartmentRow>(departmentsRes.data?.data)
}

async function loadStaffPermissions(staffId: number) {
  selectedStaffId.value = staffId
  const [permissionRes, staffRes] = await Promise.all([
    permissionsApi.getStaffPermissions(staffId),
    staffApi.get(staffId),
  ])
  const permissionPayload = permissionRes.data?.data ?? {}
  form.value = {
    role: String(permissionPayload.role ?? ''),
    modules: Array.isArray(permissionPayload.modules) ? permissionPayload.modules : [],
  }

  const staffPayload = (staffRes.data?.data ?? {}) as Record<string, unknown>
  selectedStaff.value = {
    id: Number(staffPayload.id ?? staffId),
    name: String(staffPayload.name ?? ''),
    staff_no: String(staffPayload.staff_no ?? ''),
    join_date: typeof staffPayload.join_date === 'string' ? staffPayload.join_date : null,
    leave_date: typeof staffPayload.leave_date === 'string' ? staffPayload.leave_date : null,
    region: (staffPayload.region as StaffInfo['region']) ?? null,
    department: (staffPayload.department as StaffInfo['department']) ?? null,
    title: (staffPayload.title as StaffInfo['title']) ?? null,
  }

  if (selectedStaff.value.region?.id) {
    search.value.region_id = String(selectedStaff.value.region.id)
  }
  if (selectedStaff.value.department?.id) {
    search.value.department_id = String(selectedStaff.value.department.id)
    managedDepartmentIds.value = [Number(selectedStaff.value.department.id)]
  }
  search.value.keyword = selectedStaff.value.name
}

function onRegionChange() {
  search.value.department_id = ''
  managedDepartmentIds.value = []
}

async function onSearchKeywordInput() {
  if (!search.value.keyword || search.value.keyword.length < 1) {
    candidateStaff.value = []
    return
  }
  const response = await staffApi.autocomplete({ keyword: search.value.keyword })
  candidateStaff.value = normalizeRows<StaffCandidate>(response.data?.data)
}

async function selectStaff(item: StaffCandidate) {
  candidateStaff.value = []
  await loadStaffPermissions(item.id)
}

async function queryStaff() {
  error.value = ''
  const response = await staffApi.overview({
    keyword: search.value.keyword || undefined,
    region_id: search.value.region_id || undefined,
    department_id: search.value.department_id || undefined,
  })
  const rows = normalizeRows<StaffInfo>(response.data?.data)
  if (!rows.length) {
    selectedStaff.value = null
    error.value = '查無符合人員'
    return
  }
  await loadStaffPermissions(Number(rows[0].id))
}

function selectAllModules() {
  form.value.modules = [...modules.value]
}

function selectAllManagedDepartments() {
  managedDepartmentIds.value = filteredDepartments.value.map((dep) => dep.id)
}

async function save() {
  msg.value = ''
  error.value = ''
  if (!form.value.role) {
    error.value = '請選擇角色'
    return
  }
  if (form.value.role === 'teacher' && managedDepartmentIds.value.length === 0) {
    error.value = '部門主管至少需選擇一個管理部門'
    return
  }
  await permissionsApi.updateStaffPermissions(selectedStaffId.value, form.value)
  msg.value = '儲存成功'
  setTimeout(() => (msg.value = ''), 3000)
}

onMounted(async () => {
  loading.value = true
  await loadPermissionContext()
  await loadStaffPermissions(routeStaffId)
  loading.value = false
})
</script>

<style scoped>
.page{padding:1rem}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.card{background:#fff;padding:1.25rem;border-radius:8px;max-width:960px;margin-bottom:1rem}
.filter-grid{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.75rem;align-items:end}
.form-group{margin-bottom:1rem;position:relative}
label{display:block;margin-bottom:.5rem;font-weight:500}
input,select{width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px;box-sizing:border-box}
.form-actions{display:flex;align-items:flex-end;gap:.5rem}
.autocomplete{position:absolute;left:0;right:0;top:100%;background:#fff;border:1px solid #d9d9d9;border-radius:4px;z-index:5;max-height:200px;overflow:auto}
.autocomplete-item{display:block;width:100%;text-align:left;padding:.45rem .6rem;border:none;background:#fff;color:#333;cursor:pointer}
.autocomplete-item:hover{background:#f5f5f5}
.info-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.5rem 1rem;color:#333}
.module-actions{display:flex;gap:.5rem;margin-bottom:.5rem}
.module-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:.5rem}
.checkbox-label{display:flex;align-items:center;gap:.25rem;font-weight:400;cursor:pointer}
button{padding:.45rem 1rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer}
.loading{padding:2rem;text-align:center;color:#999}
.success{color:#389e0d;margin-top:.5rem}
.error{color:#cf1322;margin-top:.5rem}
@media (max-width: 960px) {
  .filter-grid { grid-template-columns: 1fr; }
  .info-grid { grid-template-columns: 1fr; }
  .module-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
  if (!selectedStaffId.value) {
    error.value = '請先查詢並選擇人員'
    return
  }
