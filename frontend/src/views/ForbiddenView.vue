<template>
  <div class="forbidden">
    <h1>403</h1>
    <p>您沒有存取此頁面的權限。</p>
    <RouterLink to="/dashboard">返回儀表板</RouterLink>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.forbidden {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  gap: 1rem;
  text-align: center;
}
.forbidden h1 { font-size: 4rem; color: #cf1322; margin: 0; }
.forbidden p { color: #595959; }
.forbidden a { color: #1677ff; text-decoration: none; }
</style>
