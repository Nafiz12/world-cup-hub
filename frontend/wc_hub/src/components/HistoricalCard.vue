<!-- src/components/HistoricalCard.vue -->
<template>
  <article
    class="historical-card bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-[1.02] focus-within:scale-[1.02] outline-none"
    tabindex="0"
  >
    <div class="p-6">
      <div class="flex items-center gap-4 mb-4">
                <img
          :src="flagSrc"
          :alt="`${cup.winner} flag`"
          class="w-12 h-12 rounded-full border border-gray-200 object-cover"
          loading="lazy" decoding="async"
        />  
        <h3 class="text-2xl font-bold text-gray-900">{{ cup.winner }}</h3>
      </div>

      <dl class="text-gray-700">
        <div class="mt-1">
          <dt class="font-semibold text-gray-800 inline">Year:</dt>
          <dd class="inline"> {{ cup.year }}</dd>
        </div>
        <div class="mt-1">
          <dt class="font-semibold text-gray-800 inline">Host:</dt>
          <dd class="inline"> {{ cup.host }}</dd>
        </div>
        <div v-if="cup.runner_up" class="mt-1">
          <dt class="font-semibold text-gray-800 inline">Runner-up:</dt>
          <dd class="inline"> {{ cup.runner_up }}</dd>
        </div>
        <div v-if="cup.score" class="mt-1">
          <dt class="font-semibold text-gray-800 inline">Final Score:</dt>
          <dd class="inline"> {{ cup.score }}</dd>
        </div>
      </dl>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  cup: { type: Object, required: true }
});

// Normalize country names to lookups (covers “West Germany”, etc.)
const FLAG_MAP = {
  uruguay: 'https://flagcdn.com/uy.svg',
  italy: 'https://flagcdn.com/it.svg',
  'west germany': 'https://flagcdn.com/de.svg',
  germany: 'https://flagcdn.com/de.svg',
  brazil: 'https://flagcdn.com/br.svg',
  england: 'https://flagcdn.com/gb-eng.svg',  // national flag for England
  argentina: 'https://flagcdn.com/ar.svg',
  spain: 'https://flagcdn.com/es.svg',
  france: 'https://flagcdn.com/fr.svg',
};

const flagSrc = computed(() => {
  const key = String(props.cup?.winner || '').trim().toLowerCase();
  return FLAG_MAP[key] || 'https://flagcdn.com/unknown.svg';
});
</script>

<style scoped>
.historical-card {
  box-shadow: 0 4px 10px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
  will-change: transform;
  transition: transform 140ms ease, box-shadow 140ms ease;
}
.historical-card:focus-within {
  box-shadow: 0 6px 16px rgba(0,0,0,0.18), 0 3px 8px rgba(0,0,0,0.10);
}
</style>
