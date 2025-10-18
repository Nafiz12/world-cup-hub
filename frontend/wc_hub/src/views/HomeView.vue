<!-- src/views/HomeView.vue -->
<template>
  <div class="w-max">
    <!-- Background -->
    <div class="fixed inset-0 -z-20 bg-[url('/background.png')] bg-cover bg-center"></div>
    <div class="fixed inset-0 -z-10 bg-black/55 backdrop-blur-sm"></div>

    <!-- Content -->
    <div class="mx-auto w-full max-w-[1280px] px-4 sm:px-6 md:px-8 py-6 md:py-10 pb-32 text-white">
      <!-- Header -->
      <header class="mb-6 md:mb-8">
        <h1 class="font-extrabold tracking-tight leading-tight drop-shadow text-[clamp(2rem,3.2vw,3.75rem)]">
          FIFA World Cup Hub
        </h1>
      </header>

      <!-- Main (stacked) -->
      <main class="space-y-8">
        <!-- Countdown -->
        <section>
          <div class="max-w-[420px] sm:max-w-none">
            <Countdown :target="WORLD_CUP_START"/>
          </div>
        </section>

        <!-- History -->
        <section>
          <h2 class="font-bold drop-shadow text-[clamp(1.25rem,2.2vw,2rem)] mb-4">
            Historical World Cups
          </h2>

          <div v-if="loading" class="text-center py-10">
            <svg class="animate-spin h-10 w-10 text-white mx-auto" viewBox="0 0 24 24" role="status" aria-label="Loading">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <p class="mt-2">Loading data...</p>
          </div>

          <div v-else-if="error" class="text-center py-6 text-red-300">
            <p class="font-medium">Error loading data. Please try again later.</p>
            <p class="text-sm opacity-80 mt-1">{{ error }}</p>
          </div>

          <!-- Cards: predictable columns -->
          <div v-else class="grid gap-4 sm:gap-5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <HistoricalCard v-for="cup in worldCups" :key="cup.id || cup.year" :cup="cup" />
            <p v-if="worldCups.length === 0" class="text-center col-span-full text-white/80">No tournaments found.</p>
          </div>
        </section>
      </main>

      <footer class="mt-12 text-center text-white/70">
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

let id = null
async function loadData () {
  try { worldCups.value = await getWorldCupHistory() }
  catch (e) { console.error(e); error.value = 'Failed to fetch World Cup data.' }
  finally { loading.value = false }
}

onMounted(() => { loadData() })
onBeforeUnmount(() => { if (id) clearInterval(id) })
</script>
