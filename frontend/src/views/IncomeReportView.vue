<template>
  <div class="page">
    <h2>E01 收入報表</h2>
    <div class="card">
      <div class="form-group" style="display:flex;gap:1rem;align-items:flex-end">
        <div><label>起始日期</label><input type="date" v-model="from"/></div>
        <div><label>結束日期</label><input type="date" v-model="to"/></div>
        <button @click="load">查詢</button>
      </div>
      <div v-if="loading">載入中...</div>
      <table v-else>
        <thead><tr><th>學員</th><th>金額</th><th>日期</th><th>狀態</th></tr></thead>
        <tbody>
          <tr v-for="r in rows" :key="r.id">
            <td>{{(r.student as Record<string,unknown>)?.name ?? r.student_id}}</td>
            <td>{{r.amount}}</td><td>{{r.payment_date}}</td><td>{{r.status}}</td>
          </tr>
        </tbody>
      </table>
      <p v-if="rows.length" style="margin-top:1rem;font-weight:600">合計：{{total}}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
// 功能編號：E01 收入報表
import { ref, computed, onMounted } from 'vue'
import { reportsApi } from '@/api/reports'
const rows = ref<Record<string,unknown>[]>([])
const loading = ref(false); const from = ref(''); const to = ref('')
const total = computed(() => rows.value.reduce((s, r) => s + (r.amount as number ?? 0), 0))
async function load() {
  loading.value=true
  const r=await reportsApi.income(from.value?{from:from.value,to:to.value}:undefined)
  rows.value=r.data?.data?.data??r.data?.data??[]; loading.value=false
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
button.danger { background:#ff4d4f }
</style>