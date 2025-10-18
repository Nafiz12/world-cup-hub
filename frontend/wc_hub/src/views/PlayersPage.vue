
<template>
     <div v-if="loadingTeams"
         class="absolute inset-0 grid place-items-center bg-black/40 backdrop-blur-sm rounded-xl">
      <div class="flex flex-col items-center gap-3 text-white">
        <svg class="animate-spin h-10 w-10" viewBox="0 0 24 24" role="status" aria-label="Loading">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
        <div class="text-sm">Loading teams…</div>
      </div>
    </div>
  <div v-else class="max-w-6xl mx-auto px-4 pt-0 pb-6">
    <div class="fixed inset-0 -z-10 bg-black/55 backdrop-blur-sm"></div>
    <h1 class="mt-0 mb-3 text-2xl font-semibold text-white">Players</h1>

    <!-- Controls -->
    <div class="bg-white/95 rounded-xl shadow p-3 md:p-4 mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
      <label class="flex flex-col gap-1 md:col-span-3">
        <span class="text-xs text-gray-600">Team</span>
        <select v-model="teamId" class="border rounded-lg px-3 py-2">
          <option v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
      </label>

      <label class="flex flex-col gap-1 md:col-span-2">
        <span class="text-xs text-gray-600">Season</span>
        <input v-model.number="season" 
        :min="2021"
        :max="2023"
        type="number" class="border rounded-lg px-3 py-2" />
      </label>

      <div class="md:col-span-1 flex items-end">
        <button @click="loadPlayers" :disabled="loading || !teamId"
          class="px-4 py-2 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 w-full">
          {{ loading ? 'Loading…' : 'Load' }}
        </button>
      </div>
    </div>
    <br></br>

    <!-- Messages -->
    <p v-if="errorMsg" class="text-red-200 mb-3">{{ errorMsg }}</p>
    <p v-else-if="!loading && players.length === 0" class="text-white/80">No players loaded yet.</p>

    <!-- ✅ Render mapped players -->
    <ul v-else class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <li v-for="p in players" :key="p.id" class="bg-white/95 rounded-xl shadow p-3 flex items-center gap-3">
        <img :src="p.photo" alt="" class="w-12 h-12 rounded-full object-cover bg-gray-200" />
        <div class="min-w-0">
          <div class="font-medium truncate">{{ p.name }}</div>
          <div class="text-xs text-gray-600">
            {{ p.position }} • {{ p.nationality }} <span v-if="p.minutes">• {{ p.minutes }}’</span>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { fetchTeams, fetchPlayers } from "@/services/worldcup";

const season = ref(2022);
const league = ref(1);
const teams  = ref([]);
const teamId = ref(null);
const loadingTeams = ref(true);
const loadingPlayers = ref(false);

const rows = ref([]);          // raw API response rows
const players = computed(() => // ✅ map to a clean shape for the view
  (rows.value || []).map(r => ({
    id:          r?.player?.id ?? null,
    name:        r?.player?.name ?? "—",
    age:         r?.player?.age ?? null,
    nationality: r?.player?.nationality ?? "—",
    photo:       r?.player?.photo ?? "",
    position:    r?.statistics?.[0]?.games?.position ?? "—",
    minutes:     r?.statistics?.[0]?.games?.minutes ?? null,
  }))
);

const loading = ref(false);
const errorMsg = ref("");

async function loadTeams() {
      loadingTeams.value = true;
        errorMsg.value = "";
  try {
    teams.value = await fetchTeams(league.value, season.value);
    if (!teamId.value && teams.value.length) teamId.value = teams.value[0].id;
  } catch (e) {
    errorMsg.value = e.message || "Failed to load teams.";
  }finally {
    loadingTeams.value = false;
  }
}

function clampSeason(v) {
  const n = Number(v || 1930);
  return Math.min(2023, Math.max(2021, n));
}

async function loadPlayers() {
  season.value = clampSeason(season.value); // ✅ enforce before calling API
  if (!teamId.value) return;
  loading.value = true; errorMsg.value = "";
  try {
    rows.value = await fetchPlayers(Number(teamId.value), Number(season.value));
  } catch (e) {
    rows.value = [];
    errorMsg.value = e.message || "Failed to load players.";
  } finally {
    loading.value = false;
  }
}

onMounted(loadTeams);
</script>


