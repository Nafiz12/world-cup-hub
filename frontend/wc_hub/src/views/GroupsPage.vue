<template>
  <div class="groups-page min-h-screen py-8 px-4">
    <div class="fixed inset-0 -z-10 bg-black/55 backdrop-blur-sm"></div>

    <div class="w-full max-w-full px-4 sm:px-6 text-white">
      <div class="mb-8">
        <h1 class="text-4xl font-bold tracking-tight drop-shadow mb-2">FIFA World Cup 2026</h1>
        <!-- <p class="max-w-3xl text-sm text-white/80 leading-6">
          Explore every qualified team and the group they belong to. Click a team to mark it selected.
        </p> -->
        <br></br>
        <!-- <div v-if="selectedTeam" class="mt-4 rounded-3xl border border-cyan-400/20 bg-cyan-500/10 p-4 text-sm text-cyan-100">
          Selected team: <span class="font-semibold">{{ selectedTeam }}</span>
        </div> -->
      </div>

      <div class="mb-6 flex flex-wrap gap-3 rounded-3xl border border-white/10 bg-white/5 p-2 shadow-lg backdrop-blur">
        <button
          type="button"
          class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
          :class="activeTab === 'groups' ? 'bg-cyan-400/20 text-cyan-100 ring-1 ring-cyan-300/30' : 'text-white/70 hover:bg-white/8 hover:text-white'"
          @click="activeTab = 'groups'"
        >
          Groups
        </button>
        <button
          type="button"
          class="rounded-2xl px-4 py-2 text-sm font-semibold transition"
          :class="activeTab === 'fixtures' ? 'bg-emerald-400/20 text-emerald-100 ring-1 ring-emerald-300/30' : 'text-white/70 hover:bg-white/8 hover:text-white'"
          @click="activeTab = 'fixtures'"
        >
          Fixtures
        </button>
      </div>

      <div>

      </div>

      <div v-if="activeTab === 'groups'" class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <article
          v-for="group in GROUPS"
          :key="group.name"
          class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-lg backdrop-blur"
        >
          <div class="flex items-center justify-between mb-4">
            <span class="inline-flex items-center gap-2 rounded-full bg-blue-600/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-blue-100">
              Group {{ group.name }}
            </span>
            <!-- <span class="text-sm text-white/70">{{ group.teams.length }} teams</span> -->
          </div>

          <ul class="space-y-3">
            <li
              v-for="team in group.teams"
              :key="team.name"
              class="rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 transition hover:border-cyan-400/40 hover:bg-slate-900/90"
            >
              <button
                type="button"
                @click="openTeam(team)"
                class="w-full text-left text-cyan-200 hover:text-cyan-100"
              >
                <div class="flex items-center gap-3 font-medium truncate">
                  <img
                    v-if="team.code"
                    :src="flagUrl(team.code)"
                    :alt="team.name + ' flag'"
                    class="h-7 w-10 rounded-sm object-cover bg-white/10"
                    loading="lazy"
                  />
                  <span>{{ team.name }}</span>
                </div>
              </button>
            </li>
          </ul>
        </article>
      </div>

      <section v-else class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-lg backdrop-blur">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-[11px] uppercase tracking-[0.35em] text-emerald-100/80">Fixtures</p>
            <h2 class="text-xl font-semibold text-white">All FIFA World Cup 2026 matches</h2>
            <p class="text-sm text-white/70">This uses the same live-score API and shows the full match list with results.</p>
          </div>
        </div>

        <LiveScores
          endpoint="/api/fixtures"
          :limit="52"
          :use-limit="false"
          title="Fixtures"
          subtitle="This uses the same live-score API and shows the full match list with results."
        />
      </section>

      <footer class="mt-12 text-center text-white/70">
        © {{ new Date().getFullYear() }} World Cup Hub created by <a href="https://mdnafizalifat.vercel.app/" target="_blank" class="underline hover:text-white">Md Nafiz Al ifat</a>.
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import LiveScores from '@/components/LiveScores.vue'

const activeTab = ref('groups')
const selectedTeam = ref('')

const GROUPS = [
  {
    name: 'A',
    teams: [
      { name: 'Mexico', code: 'MX', subtitle: 'Qualified team' },
      { name: 'South Africa', code: 'ZA', subtitle: 'Qualified team' },
      { name: 'South Korea', code: 'KR', subtitle: 'Qualified team' },
      { name: 'Czechia', code: 'CZ', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'B',
    teams: [
      { name: 'Canada', code: 'CA', subtitle: 'Qualified team' },
      { name: 'Bosnia', code: 'BA', subtitle: 'Qualified team' },
      { name: 'Qatar', code: 'QA', subtitle: 'Qualified team' },
      { name: 'Switzerland', code: 'CH', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'C',
    teams: [
      { name: 'Brazil', code: 'BR', subtitle: 'Qualified team' },
      { name: 'Morocco', code: 'MA', subtitle: 'Qualified team' },
      { name: 'Haiti', code: 'HT', subtitle: 'Qualified team' },
      { name: 'Scotland', code: 'GB', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'D',
    teams: [
      { name: 'United States', code: 'US', subtitle: 'Qualified team' },
      { name: 'Paraguay', code: 'PY', subtitle: 'Qualified team' },
      { name: 'Australia', code: 'AU', subtitle: 'Qualified team' },
      { name: 'Türkiye', code: 'TR', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'E',
    teams: [
      { name: 'Germany', code: 'DE', subtitle: 'Qualified team' },
      { name: 'Curaçao', code: 'CW', subtitle: 'Qualified team' },
      { name: 'Ivory Coast', code: 'CI', subtitle: 'Qualified team' },
      { name: 'Ecuador', code: 'EC', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'F',
    teams: [
      { name: 'Netherlands', code: 'NL', subtitle: 'Qualified team' },
      { name: 'Japan', code: 'JP', subtitle: 'Qualified team' },
      { name: 'Sweden', code: 'SE', subtitle: 'Qualified team' },
      { name: 'Tunisia', code: 'TN', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'G',
    teams: [
      { name: 'Belgium', code: 'BE', subtitle: 'Qualified team' },
      { name: 'Egypt', code: 'EG', subtitle: 'Qualified team' },
      { name: 'Iran', code: 'IR', subtitle: 'Qualified team' },
      { name: 'New Zealand', code: 'NZ', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'H',
    teams: [
      { name: 'Spain', code: 'ES', subtitle: 'Qualified team' },
      { name: 'Cabo Verde', code: 'CV', subtitle: 'Qualified team' },
      { name: 'Saudi Arabia', code: 'SA', subtitle: 'Qualified team' },
      { name: 'Uruguay', code: 'UY', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'I',
    teams: [
      { name: 'France', code: 'FR', subtitle: 'Qualified team' },
      { name: 'Senegal', code: 'SN', subtitle: 'Qualified team' },
      { name: 'Iraq', code: 'IQ', subtitle: 'Qualified team' },
      { name: 'Norway', code: 'NO', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'J',
    teams: [
      { name: 'Argentina', code: 'AR', subtitle: 'Qualified team' },
      { name: 'Algeria', code: 'DZ', subtitle: 'Qualified team' },
      { name: 'Austria', code: 'AT', subtitle: 'Qualified team' },
      { name: 'Jordan', code: 'JO', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'K',
    teams: [
      { name: 'Portugal', code: 'PT', subtitle: 'Qualified team' },
      { name: 'DR Congo', code: 'CD', subtitle: 'Qualified team' },
      { name: 'Uzbekistan', code: 'UZ', subtitle: 'Qualified team' },
      { name: 'Colombia', code: 'CO', subtitle: 'Qualified team' },
    ],
  },
  {
    name: 'L',
    teams: [
      { name: 'England', code: 'GB', subtitle: 'Qualified team' },
      { name: 'Croatia', code: 'HR', subtitle: 'Qualified team' },
      { name: 'Ghana', code: 'GH', subtitle: 'Qualified team' },
      { name: 'Panama', code: 'PA', subtitle: 'Qualified team' },
    ],
  },
]

function flagUrl(code) {
  return `https://flagcdn.com/w40/${String(code).toLowerCase()}.png`
}

function openTeam(team) {
  selectedTeam.value = `${team.code ?? ''} ${team.name}`.trim()
}
</script>
