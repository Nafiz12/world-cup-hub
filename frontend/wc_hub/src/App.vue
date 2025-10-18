<template>
  <!-- Background (kept) -->
  <div class="fixed inset-0 -z-20 bg-[url('/background.png')] bg-cover bg-center"></div>

  <!-- Nav + content -->
  <NavBar />
  <main class="pt-14 min-h-screen overflow-y-auto">
    <RouterView />
    <WorldCupChat />
  </main>
</template>

<script setup>
import { RouterView } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
import WorldCupChat from '@/components/WorldCupChat.vue'
</script>
