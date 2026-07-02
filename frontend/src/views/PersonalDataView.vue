<template>
  <div class="page">
    <h2>個人資料維護</h2>
    <div class="card">
      <div class="photo-section">
        <img :src="photoPreview || form.photo_url || '/default-avatar.png'" class="avatar" alt="個人照片" />
        <div>
          <input ref="fileInput" type="file" accept="image/*" style="display:none" @change="onPhotoChange" />
          <button type="button" @click="(fileInput as HTMLInputElement).click()">更換照片</button>
          <button v-if="photoFile" type="button" @click="uploadPhoto" style="margin-left:.5rem">上傳</button>
        </div>
      </div>
      <form @submit.prevent="save">
        <div class="form-group"><label>姓名</label><input v-model="form.name" placeholder="姓名"/></div>
        <div class="form-group"><label>電話</label><input v-model="form.phone" placeholder="電話"/></div>
        <div class="form-group"><label>性別</label>
          <select v-model="form.gender"><option value="">選擇</option><option value="M">男</option><option value="F">女</option></select></div>
        <div class="form-group"><label>血型</label>
          <select v-model="form.blood_type"><option value="">選擇</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select></div>
        <div class="form-group"><label>生日</label><input type="date" v-model="form.birth_date"/></div>
        <div class="form-group info"><label>員工編號</label><span>{{ form.staff_no }}</span></div>
        <div class="form-group info"><label>所屬區域</label><span>{{ (form.region as Record<string,unknown>)?.name }}</span></div>
        <div class="form-group info"><label>部門</label><span>{{ (form.department as Record<string,unknown>)?.name }}</span></div>
        <div class="form-group info"><label>職稱</label><span>{{ (form.title as Record<string,unknown>)?.name }}</span></div>
        <button type="submit">儲存</button>
        <p v-if="msg" style="color:green;margin-top:.5rem">{{ msg }}</p>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { personalDataApi } from '@/api/personalData'
const form = ref<Record<string,unknown>>({})
const photoFile = ref<File|null>(null)
const photoPreview = ref<string|null>(null)
const fileInput = ref<HTMLInputElement|null>(null)
const msg = ref('')
async function load() { const r = await personalDataApi.get(); form.value = r.data?.data ?? {} }
async function save() { await personalDataApi.update(form.value); msg.value='儲存成功'; setTimeout(()=>msg.value='',3000) }
function onPhotoChange(e: Event) {
  const t = e.target as HTMLInputElement
  photoFile.value = t.files?.[0] ?? null
  if (photoFile.value) photoPreview.value = URL.createObjectURL(photoFile.value)
}
async function uploadPhoto() {
  if (!photoFile.value) return
  const fd = new FormData(); fd.append('photo', photoFile.value)
  const r = await import('@/api/axios').then(m => m.default.post('/me/personal-data/photo', fd, { headers: { 'Content-Type': 'multipart/form-data' } }))
  form.value.photo_url = r.data?.data?.photo_url; photoFile.value = null; photoPreview.value = null
  msg.value = '照片上傳成功'; setTimeout(()=>msg.value='',3000)
}
onMounted(load)
</script>
<style scoped>
.page{padding:1rem}.card{background:#fff;padding:1.5rem;border-radius:8px;max-width:600px}
.photo-section{display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem}
.avatar{width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid #d9d9d9;background:#f0f0f0}
.form-group{margin-bottom:1rem}label{display:block;margin-bottom:.25rem;font-weight:500}
input,select{width:100%;padding:.5rem;border:1px solid #d9d9d9;border-radius:4px;box-sizing:border-box}
.info input,.info span{color:#666;background:#fafafa}button{padding:.5rem 1.5rem;background:#1890ff;color:#fff;border:none;border-radius:4px;cursor:pointer}
</style>
