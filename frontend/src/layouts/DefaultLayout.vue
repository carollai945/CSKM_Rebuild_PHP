<template>
  <div class="layout">
    <aside class="sidebar">
      <nav>
        <RouterLink to="/dashboard">儀表板</RouterLink>
      </nav>
    </aside>
    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.sidebar { width: 220px; background: #001529; color: #fff; padding: 1rem; }
.sidebar a { display: block; color: #ffffffa0; padding: .5rem; text-decoration: none; }
.sidebar a:hover, .sidebar a.router-link-active { color: #fff; }
.content { flex: 1; padding: 1.5rem; background: #f0f2f5; }
</style>
