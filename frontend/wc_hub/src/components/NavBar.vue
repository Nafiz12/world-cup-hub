<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <nav class="fixed top-0 left-0 right-0 h-14 z-50 bg-white/90 border-b px-4 flex items-center gap-6">
    <RouterLink to="/" class="font-semibold">Home</RouterLink>
    <RouterLink to="/players">Players</RouterLink>
  </nav>
</template>
