<template>
  <div class="page">
    <h2>學員分配管理</h2>
    <div class="card">
      <p>批次指派學員顧問</p>
      <div class="form-group"><label>學員 ID（逗號分隔）</label><input v-model="studentIds" placeholder="1,2,3"/></div>
      <div class="form-group"><label>顧問 Staff ID</label><input v-model="staffId" type="number" placeholder="staff ID"/></div>
      <button @click="assign">指派</button>
      <p v-if="msg" style="margin-top:1rem;color:green">{{msg}}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { studentsApi } from '@/api/students'
const studentIds = ref(''); const staffId = ref<number|null>(null); const msg = ref('')
async function assign() {
  const ids = studentIds.value.split(',').map(s => parseInt(s.trim())).filter(n => !isNaN(n))
  await studentsApi.assign({ student_ids: ids, staff_id: staffId.value })
  msg.value = '指派成功'; setTimeout(() => msg.value = '', 3000)
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