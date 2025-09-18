<!-- src/views/HomeView.vue -->
<template>
  <div class="relative min-h-svh w-screen overflow-x-hidden">
    <!-- Full-bleed wallpaper and overlay -->
    <div class="fixed inset-0 -z-20 bg-[url('/background.png')] bg-cover bg-center"></div>
    <div class="fixed inset-0 -z-10 bg-black/55 backdrop-blur-sm"></div>

    <!-- Content container: full width on mobile, wider cap on desktop -->
    <div class="mx-auto w-full max-w-screen-2xl px-3 sm:px-4 md:px-6 lg:px-8 py-6 md:py-10 text-white">
      <!-- Header -->
      <header class="mb-6 md:mb-8">
        <h1 class="text-3xl sm:text-4xl md:text-6xl font-extrabold tracking-tight leading-tight drop-shadow">
          FIFA World Cup Hub
        </h1>
      </header>

      <!-- Main -->
      <main class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 md:gap-8 items-start">
       <!-- Countdown -->
      <section class="flex md:justify-start">
              <Countdown target="2026-06-11T00:00:00Z" />
      </section>
        <!-- History -->
        <section class="md:col-span-2">
          <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-3 sm:mb-4 drop-shadow">
            Historical World Cups
          </h2>

          <!-- Loading -->
          <div v-if="loading" class="text-center py-10">
            <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 text-white mx-auto" viewBox="0 0 24 24" role="status" aria-label="Loading">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <p class="mt-2">Loading data...</p>
          </div>

          <!-- Error -->
          <div v-else-if="error" class="text-center py-6 text-red-300">
            <p class="font-medium">Error loading data. Please try again later.</p>
            <p class="text-sm opacity-80 mt-1">{{ error }}</p>
          </div>

          <!-- Cards: auto-fit to fill the width -->
          <div v-else class="grid [grid-template-columns:repeat(auto-fit,minmax(220px,1fr))] gap-4 sm:gap-5">
            <HistoricalCard v-for="cup in worldCups" :key="cup.id || cup.year" :cup="cup" />
            <p v-if="worldCups.length === 0" class="text-center col-span-full text-white/80">No tournaments found.</p>
          </div>
        </section>
      </main>

      <!-- Footer -->
      <footer class="mt-10 md:mt-12 text-center text-white/70">
        © {{ new Date().getFullYear() }} World Cup Hub
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import HistoricalCard from '@/components/HistoricalCard.vue'
import { getWorldCupHistory } from '@/services/worldcup'
import { WORLD_CUP_START } from '@/config'
import Countdown from '@/components/Countdown.vue'

const worldCups = ref([])
const loading = ref(true)
const error = ref(null)

const countdown = ref({ days: '0', hours: '0', minutes: '0', seconds: '0' })
function updateCountdown() {
  const target = new Date(WORLD_CUP_START).getTime()
  const diff = target - Date.now()
  if (diff <= 0) { countdown.value = { days: '0', hours: '00', minutes: '00', seconds: '00' }; return }
  const s = Math.floor(diff / 1000)
  const days = Math.floor(s / 86400)
  const hours = Math.floor((s % 86400) / 3600)
  const mins = Math.floor((s % 3600) / 60)
  const secs = s % 60
  const pad = (n) => String(n).padStart(2, '0')
  countdown.value = { days: String(days), hours: pad(hours), minutes: pad(mins), seconds: pad(secs) }
}

let id = null
async function loadData() {
  try { worldCups.value = await getWorldCupHistory() }
  catch (e) { console.error(e); error.value = 'Failed to fetch World Cup data.' }
  finally { loading.value = false }
}

onMounted(() => { updateCountdown(); id = setInterval(updateCountdown, 1000); loadData() })
onBeforeUnmount(() => { if (id) clearInterval(id) })
</script>
