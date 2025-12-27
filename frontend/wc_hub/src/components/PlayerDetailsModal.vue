<!-- src/components/PlayerDetailsModal.vue -->
<template>
  <!-- backdrop -->
  <transition name="fade">
    <div v-if="open" class="fixed inset-0 z-50 bg-black/60" @click.self="close" />
  </transition>

  <!-- dialog -->
  <transition name="pop">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-start md:items-center justify-center p-4 md:p-6"
      role="dialog"
      aria-modal="true"
      aria-labelledby="player-modal-title"
      ref="dialogRef"
      tabindex="-1"
      @keydown.esc="close"
    >
      <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden">
        <!-- header -->
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <h3 id="player-modal-title" class="text-lg font-semibold text-gray-500">
            {{ display.name }} <span v-if="display.number" class="text-gray-500">#{{ display.number }}</span>
          </h3>
          <button class="rounded-full p-2 text-black hover:bg-gray-100 focus:outline-none focus:ring" @click="close" aria-label="Close">✕</button>
        </div>

        <!-- body -->
              <div class="px-5 py-4">
          <div class="flex flex-col md:flex-row gap-4">
            <img :src="display.photo" alt="" class="h-28 w-28 rounded-xl object-cover ring-1 ring-gray-200" />
            <div class="space-y-1">
              <div class="text-md text-gray-700">
                <span v-if="display.position">Position: {{ display.position }}</span>
                <span v-if="display.age"> • Age: {{ display.age }}</span>
                <span v-if="display.height"> • Height: {{ display.height }}</span>
                <span v-if="display.weight"> • Weight: {{ display.weight }}</span>
              </div>
              <div class="text-md text-gray-700" v-if="display.nationality">Nationality: {{ display.nationality }}</div>
              <div class="text-md text-gray-700" v-if="display.team">Team: {{ display.team }}</div>
              <div class="text-md text-gray-700" v-if="display.minutes">Minutes: {{ display.minutes }}’</div>
              <div class="text-md text-gray-700" v-if="display.cards">
                Cards: <span v-if="display.cards.yellow">🟨 {{ display.cards.yellow }}</span>
                <span v-if="display.cards.red"> • 🟥 {{ display.cards.red }}</span>
              </div>
              <div class="text-md text-gray-700" v-if="display.goals !== null">Goals: {{ display.goals }}</div>
              <div class="text-md text-gray-700" v-if="display.assists !== null">Assists: {{ display.assists }}</div>
              <div class="text-xs text-gray-500" v-if="display.league">League: {{ display.league }}</div>
              <div class="text-xs text-gray-500" v-if="display.season">Season: {{ display.season }}</div>
            </div>
          </div>

          <!-- raw dump (optional, dev-only) -->
          <!-- <pre class="mt-4 text-xs bg-gray-50 p-3 rounded-lg overflow-x-auto">{{ row }}</pre> -->
        </div>


        <!-- footer -->
        <div class="px-5 py-3 border-t flex justify-end">
          <button class="px-4 py-2 rounded-l text-white bg-emerald-600 hover:bg-emerald-700" @click="close">Close</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { computed, ref, watch, nextTick } from 'vue'



const props = defineProps({
  open: { type: Boolean, default: false },
  row:  { type: Object, default: null },
})

const emit = defineEmits(['close'])
const close = () => emit('close',false)

// focus on open for a11y
const dialogRef = ref(null)


watch(() => props.open, 
 (now) => {
  if (now) nextTick(() => dialogRef.value?.focus())
})

// normalize lots of possible fields from the SAME response you already have
const display = computed(() => {
  const r = props.row || {};
  return {
    id:        r?.player?.id ?? null,
    name:      r?.player?.name ?? '—',
    photo:     r?.player?.photo ?? '',
    age:       r?.player?.age ?? null,
    height:    r?.player?.height ?? null,
    weight:    r?.player?.weight ?? null,
    nationality: r?.player?.nationality ?? '—',
    position:  r?.statistics?.[0]?.games?.position ?? '—',
    minutes:   r?.statistics?.[0]?.games?.minutes ?? null,
    number:    r?.statistics?.[0]?.games?.number ?? null,
    team:      r?.statistics?.[0]?.team?.name ?? null,
    league:    r?.statistics?.[0]?.league?.name ?? null,
    season:    r?.statistics?.[0]?.league?.season ?? null,
    goals:     r?.statistics?.[0]?.goals?.total ?? null,
    assists:   r?.statistics?.[0]?.goals?.assists ?? null,
    cards: {
      yellow:  r?.statistics?.[0]?.cards?.yellow ?? null,
      red:     r?.statistics?.[0]?.cards?.red ?? null,
    },
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.pop-enter-active, .pop-leave-active { transition: transform .18s ease, opacity .18s ease; }
.pop-enter-from, .pop-leave-to { transform: translateY(8px) scale(.98); opacity: 0; }
</style>
