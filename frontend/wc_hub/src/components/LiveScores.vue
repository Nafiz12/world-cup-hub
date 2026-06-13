<template>
  <section class="rounded-3xl bg-white/10 ring-1 ring-white/15 shadow-xl p-5 sm:p-6">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-[11px] uppercase tracking-[0.35em] text-emerald-100/80">Live</p>
        <h2 class="text-xl sm:text-2xl font-semibold mt-1">{{ title }}</h2>
        <p class="text-sm text-white/70 mt-1">{{ subtitle }}</p>
      </div>
      <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-100">Updated live</span>
    </div>

    <div v-if="loading" class="mt-6 text-center text-white/80">
      <p class="text-sm">Loading live matches…</p>
    </div>

    <div v-else-if="error" class="mt-6 rounded-2xl bg-red-500/10 ring-1 ring-red-400/30 p-4 text-sm text-red-100">
      {{ error }}
    </div>

    <div v-else-if="matches.length" class="mt-6 space-y-3">
      <article
        v-for="match in matches"
        :key="match.id || `${match.home_team?.name}-${match.away_team?.name}-${match.datetime}`"
        class="rounded-2xl bg-black/20 ring-1 ring-white/10 p-4"
      >
        <div class="flex items-center justify-between text-[11px] uppercase tracking-[0.25em] text-white/60">
          <span>{{ match.stage_name || 'Match' }}</span>
          <span>{{ match.group_name ? `Group: ${match.group_name}` : 'World Cup' }}</span>
        </div>
        <p class="mt-1 text-[11px] uppercase tracking-[0.25em] text-white/45">{{ formatDate(match.datetime) }}</p>

        <div class="mt-3 flex items-center justify-between gap-3">
          <div class="min-w-0 flex-1 text-left">
            <p class="text-sm font-semibold text-white">{{ match.home_team?.name || 'Home' }}</p>
         
          </div>
          <div class="rounded-xl bg-white/10 px-3 py-2 text-sm font-black text-white">{{ match.home_score ?? 0 }} - {{ match.away_score ?? 0 }}</div>
          <div class="min-w-0 flex-1 text-right">
            <p class="text-sm font-semibold text-white">{{ match.away_team?.name || 'Away' }}</p>
           
          </div>
        </div>

        <p class="mt-3 text-xs text-white/70">{{ match.status? match.status : 'Fixture update pending' }}</p>
      </article>
    </div>

    <div v-else class="mt-6 rounded-2xl bg-white/8 p-4 text-sm text-white/75">
      No live matches are available right now. Check back soon for the latest fixture updates.
    </div>
  </section>
</template>

<script setup>
import axios from 'axios'
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
  endpoint: { type: String, default: '/api/live-scores' },
  limit: { type: Number, default: 3 },
  useLimit: { type: Boolean, default: true },
  title: { type: String, default: 'Live Scores' },
  subtitle: {
    type: String,
    default: 'Today’s FIFA World Cup fixtures — showing up to 3 current-day matches.',
  },
})

const API_BASE = (import.meta.env.VITE_API_BASE || '').replace(/\/+$/, '')

function joinURL(base, path) {
  return `${base.replace(/\/+$/, '')}/${String(path || '').replace(/^\/+/, '')}`
}

const matches = ref([])
const loading = ref(true)
const error = ref('')

let intervalId = null

function formatDate(value) {
  if (!value) return 'TBD'
  const date = new Date(value)
  return Number.isNaN(date.getTime()) ? 'TBD' : date.toLocaleString([], {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

// function statusLabel(match) {
//   const code = Number(match.status_code ?? match.statusCode ?? 0)
//   if (code === 0) return 'Just finished'
//   if (code === 1) return 'About to start'
//   if (code === 2 || code === 3) return 'Live now'
//   return match.status_label || match.status || 'Fixture update pending'
// }

function normalizeMatch(match) {
  const homeTeam = match.home_team || match.homeTeam || {}
  const awayTeam = match.away_team || match.awayTeam || {}
  const homeScore = Number.isFinite(Number(match.home_score))
    ? Number(match.home_score)
    : Number(homeTeam.goals ?? match.home_goals ?? 0)
  const awayScore = Number.isFinite(Number(match.away_score))
    ? Number(match.away_score)
    : Number(awayTeam.goals ?? match.away_goals ?? 0)

  return {
    ...match,
    home_team: homeTeam,
    away_team: awayTeam,
    home_score: homeScore,
    away_score: awayScore,
    stage_name: match.stage_name || match.stage || 'Match',
    datetime: match.datetime || match.date || match.kickoff_time,
  }
}

async function loadMatches() {
  try {
    loading.value = true
    error.value = ''

    const today = new Date().toISOString().slice(0, 10)
    const endpoint = props.endpoint || '/api/live-scores'
    const query = new URLSearchParams({ date: today })

    if (endpoint === '/api/live-scores') {
      query.set('limit', String(Number.isFinite(props.limit) && props.limit > 0 ? props.limit : 3))
    }

    const url = joinURL(API_BASE, `${endpoint}${endpoint.includes('?') ? '&' : '?'}${query.toString()}`)
    const { data } = await axios.get(url, {
      timeout: 10000,
    })

    const list = Array.isArray(data) ? data : Array.isArray(data?.matches) ? data.matches : []
    const sorted = [...list].sort((a, b) => (Number(a.match_number || 0) - Number(b.match_number || 0)) || (String(a.datetime || '').localeCompare(String(b.datetime || ''))))

    matches.value = (props.useLimit
      ? sorted.slice(0, Number.isFinite(props.limit) && props.limit > 0 ? props.limit : 3)
      : sorted
    ).map(normalizeMatch)
  } catch (e) {
    console.error('Live score fetch failed:', e)
    error.value = 'Could not load live scores right now. Please try again shortly.'
    matches.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadMatches()
  intervalId = setInterval(loadMatches, 60000)
})

onBeforeUnmount(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>
