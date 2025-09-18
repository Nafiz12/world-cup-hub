<template>
  <div
    class="w-full md:max-w-sm rounded-2xl bg-white/10 ring-1 ring-white/15 shadow-xl p-4 sm:p-5"
  >
    <h2 id="countdown-heading" class="text-lg sm:text-xl font-semibold mb-2 text-white/90">
      Road to <span class="font-extrabold">2026</span>
    </h2>

    <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center select-none">
      <div>
        <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold">{{ time.days }}</div>
        <div class="text-[10px] sm:text-xs md:text-sm text-white/70">Days</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold">{{ time.hours }}</div>
        <div class="text-[10px] sm:text-xs md:text-sm text-white/70">Hours</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold">{{ time.minutes }}</div>
        <div class="text-[10px] sm:text-xs md:text-sm text-white/70">Minutes</div>
      </div>
      <div>
        <div class="text-2xl sm:text-3xl md:text-4xl font-extrabold">{{ time.seconds }}</div>
        <div class="text-[10px] sm:text-xs md:text-sm text-white/70">Seconds</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

// Accept a prop so the target date can be reused
const props = defineProps({
  target: {
    type: String,
    required: true,
  },
})

const time = ref({ days: '0', hours: '00', minutes: '00', seconds: '00' })

function updateCountdown() {
  const targetTime = new Date(props.target).getTime()
  const diff = targetTime - Date.now()

  if (diff <= 0) {
    time.value = { days: '0', hours: '00', minutes: '00', seconds: '00' }
    return
  }

  const s = Math.floor(diff / 1000)
  const days = Math.floor(s / 86400)
  const hours = Math.floor((s % 86400) / 3600)
  const mins = Math.floor((s % 3600) / 60)
  const secs = s % 60

  const pad = (n) => String(n).padStart(2, '0')
  time.value = { days: String(days), hours: pad(hours), minutes: pad(mins), seconds: pad(secs) }
}

let intervalId = null
onMounted(() => {
  updateCountdown()
  intervalId = setInterval(updateCountdown, 1000)
})
onBeforeUnmount(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>
