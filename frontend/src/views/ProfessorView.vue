<template>
  <div class="page">
    <div class="page-header">
      <h2>C01 師資管理</h2>
      <button @click="openCreate">新增師資</button>
    </div>

    <!-- 搜尋 -->
    <div class="filters">
      <input v-model="keyword" placeholder="姓名搜尋" @change="load" />
      <button @click="() => { keyword = ''; load() }">重置</button>
    </div>

    <div v-if="loading">載入中...</div>
    <table v-else>
      <thead><tr>
        <th>ID</th><th>照片</th><th>姓名</th><th>Email</th><th>電話</th><th>專長</th><th>狀態</th><th>操作</th>
      </tr></thead>
      <tbody>
        <tr v-for="row in rows" :key="row.id">
          <td>{{ row.id }}</td>
          <td>
            <img v-if="row.photo_path" :src="`/api/v1/professors/${row.id}/photo-thumb`"
              style="width:40px;height:40px;object-fit:cover;border-radius:50%;border:1px solid #ddd"
              alt="照片" @error="(e:any) => e.target.style.display='none'"/>
            <span v-else style="color:#ccc">—</span>
          </td>
          <td>{{ row.name }}</td>
          <td>{{ row.email }}</td>
          <td>{{ row.phone }}</td>
          <td>{{ row.specialty }}</td>
          <td>{{ row.status ?? 'ACTIVE' }}</td>
          <td>
            <button @click="edit(row)">編輯</button>
            <button @click="openUploadPhoto(row.id as number)">📷</button>
            <button class="danger" @click="remove(row.id as number)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="!loading && rows.length === 0" style="text-align:center;color:#999;padding:2rem">無師資記錄</p>

    <!-- 新增/編輯 Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <h3>{{ editId ? '編輯師資' : '新增師資' }}</h3>
        <form @submit.prevent="save">
          <div><label>姓名</label><input v-model="form.name" placeholder="姓名" required /></div>
          <div><label>Email</label><input v-model="form.email" placeholder="Email" type="email" /></div>
          <div><label>電話</label><input v-model="form.phone" placeholder="電話" /></div>
          <div><label>專長</label><input v-model="form.specialty" placeholder="專長領域" /></div>
          <div class="modal-actions">
            <button type="submit">儲存</button>
            <button type="button" @click="showForm = false">取消</button>
          </div>
        </form>
      </div>
    </div>

    <!-- 照片上傳 Modal -->
    <div v-if="photoModal" class="modal-overlay" @click.self="photoModal = false">
      <div class="modal">
        <h3>上傳師資照片</h3>
        <input type="file" accept="image/*" @change="onFileChange" style="margin:.5rem 0;display:block"/>
        <p style="color:#888;font-size:.85rem">支援 JPG / PNG，最大 5MB</p>
        <div class="modal-actions">
          <button @click="submitPhoto" :disabled="!photoFile">上傳</button>
          <button @click="photoModal = false">取消</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：C01 師資管理
import { ref, onMounted } from 'vue'
import { professorsApi } from '@/api/professors'
import api from '@/api/axios'

const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const showForm = ref(false)
const editId = ref<number | null>(null)
const form = ref<Record<string, unknown>>({})
const keyword = ref('')
const photoModal = ref(false)
const photoTargetId = ref<number | null>(null)
const photoFile = ref<File | null>(null)

async function load() {
  loading.value = true
  const params: Record<string, unknown> = {}
  if (keyword.value) params.keyword = keyword.value
  const r = await professorsApi.list(params)
  rows.value = r.data?.data?.data ?? r.data?.data ?? []
  loading.value = false
}

function openCreate() { editId.value = null; form.value = {}; showForm.value = true }
function edit(row: Record<string, unknown>) {
  editId.value = row.id as number
  form.value = { name: row.name, email: row.email, phone: row.phone, specialty: row.specialty }
  showForm.value = true
}
async function save() {
  if (editId.value) await professorsApi.update(editId.value, form.value)
  else await professorsApi.create(form.value)
  showForm.value = false; editId.value = null; form.value = {}; load()
}
async function remove(id: number) {
  if (confirm('確認刪除此師資？')) { await professorsApi.delete(id); load() }
}

function openUploadPhoto(id: number) {
  photoTargetId.value = id; photoFile.value = null; photoModal.value = true
}
function onFileChange(e: Event) {
  const files = (e.target as HTMLInputElement).files
  photoFile.value = files ? files[0] : null
}
async function submitPhoto() {
  if (!photoFile.value || !photoTargetId.value) return
  const fd = new FormData()
  fd.append('photo', photoFile.value)
  await api.post(`/professors/${photoTargetId.value}/photo`, fd, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
  photoModal.value = false; load()
}

onMounted(load)
</script>
<style scoped>
.page { padding: 1rem }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem }
.filters { display: flex; gap: .5rem; margin-bottom: 1rem }
.filters input { padding: .4rem .6rem; border: 1px solid #d9d9d9; border-radius: 4px }
table { width: 100%; border-collapse: collapse; background: #fff }
th, td { padding: .6rem 1rem; border-bottom: 1px solid #f0f0f0; text-align: left }
th { background: #fafafa; font-weight: 600 }
button { padding: .4rem .8rem; border: none; border-radius: 4px; cursor: pointer; background: #1890ff; color: #fff; margin-right: .25rem }
button.danger { background: #ff4d4f }
button:disabled { background: #d9d9d9; cursor: not-allowed }
.modal-overlay { position: fixed; inset: 0; background: #0005; display: flex; align-items: center; justify-content: center; z-index: 100 }
.modal { background: #fff; padding: 2rem; border-radius: 8px; min-width: 380px }
.modal label { display: block; margin-bottom: .25rem; font-weight: 500 }
.modal input[type=text], .modal input[type=email] { width: 100%; margin-bottom: .75rem; padding: .5rem; border: 1px solid #d9d9d9; border-radius: 4px; box-sizing: border-box }
.modal-actions { display: flex; gap: .5rem; margin-top: .5rem }
</style>
