<template>
  <div v-if="loadingTeams">
    <FullLoadingScreen />
  </div>

  <div v-else class="players-page min-h-screen flex justify-center items-start bg-gradient-to-br py-8 px-4">
    <div class="w-full max-w-7xl">
      <!-- keep your dark overlay -->
      <div class="fixed inset-0 -z-10 bg-black/55 backdrop-blur-sm"></div>

      <h1 class="text-3xl font-semibold text-white mb-4">Players</h1>
        <br></br>
      <!-- Controls -->
      <div class="flex justify-center my-6">
        <div class="bg-white/95 rounded-2xl shadow-lg p-4 w-full max-w-7xl">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- Team -->
            <label class="flex flex-col gap-1 md:col-span-4">
              <span class="text-xs text-gray-600 leading-tight">Team</span>
              <select v-model="teamId" class="border rounded-lg px-3 h-12">
                <option v-for="t in teams" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
            </label>

            <!-- Position -->
            <label class="flex flex-col gap-1 md:col-span-4">
              <span class="text-xs text-gray-600 leading-tight">Position</span>
              <select v-model="positionId" class="border rounded-lg px-3 h-12">
                <option v-for="pos in positionList" :key="pos.id ?? 'all'" :value="pos.id">{{ pos.value }}</option>
              </select>
            </label>

            <!-- Season -->
            <label class="flex flex-col gap-1 md:col-span-2">
              <span class="text-xs text-gray-600 leading-tight">Season</span>
              <input
                v-model.number="season"
                :min="2021"
                :max="2023"
                type="number"
                class="border rounded-lg px-3 h-12"
              />
            </label>

            <!-- Load -->
            <div class="md:col-span-2">
              <button
                @click="loadPlayers"
                :disabled="loading || !teamId"
                class="px-4 py-2 rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 w-full h-12"
              >
                {{ loading ? "Loading…" : "Load" }}
              </button>
            </div>
          </div>
        </div>
      </div>
<br></br>
      <!-- Messages -->
      <p v-if="errorMsg" class="text-red-200 mb-3">{{ errorMsg }}</p>
      <p v-else-if="!loading && filteredPlayers.length === 0" class="text-white/80 flex justify-center">
        No players loaded yet.
      </p>

      <!-- Players Grid -->
      <ul v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <li
          v-for="p in filteredPlayers"
          :key="p.id ?? p._idx"
          class="bg-white/95 rounded-xl shadow p-3 flex items-center gap-3 cursor-pointer hover:shadow-lg transition"
          @click="openModal(p._row)"
        >
          <img :src="p.photo" alt="" class="w-12 h-12 rounded-full object-cover bg-gray-200" />
          <div class="min-w-0">
            <div class="font-medium truncate">{{ p.name }}</div>
            <div class="text-xs text-gray-600">
              {{ p.position }} • {{ p.nationality }}
              <span v-if="p.minutes">• {{ p.minutes }}’</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <!-- Modal (uses the SAME row data) -->
  <PlayerDetailsModal :open="modalOpen" :row="activeRow" @close="(modalvalu)=>{modalOpen=modalvalu}" />
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { fetchTeams, fetchPlayers } from "@/services/worldcup";
import FullLoadingScreen from "@/components/FullLoadingScreen.vue";
import PlayerDetailsModal from "@/components/PlayerDetailsModal.vue";

const season = ref(2022);
const league = ref(1);

const teams  = ref([]);
const teamId = ref(null);

// IMPORTANT: your list had trailing spaces — fixed here
const positionList = ref([
  { id: null,         value: "All" },
  { id: "Attacker",   value: "Attacker" },
  { id: "Midfielder", value: "Midfielder" },
  { id: "Defender",   value: "Defender" },
  { id: "Goalkeeper", value: "Goalkeeper" },
])

const positionId = ref(null);

const loadingTeams = ref(true);
const loading      = ref(false);
const errorMsg     = ref("");
const modalOpen = ref(false)
const activeRow = ref(null)

const rows = ref([]); // raw API response rows

// Map raw rows to a simple card model but keep the original row for modal
const mapped = computed(() =>
  (rows.value || []).map((r, i) => ({
    _idx: i,
    _row: r, // keep the original for modal
    id:          r?.player?.id ?? null,
    name:        r?.player?.name ?? "—",
    age:         r?.player?.age ?? null,
    nationality: r?.player?.nationality ?? "—",
    photo:       r?.player?.photo ?? "",
    position:    (r?.statistics?.[0]?.games?.position ?? "—").trim(),
    minutes:     r?.statistics?.[0]?.games?.minutes ?? null,
  }))
);

// Apply position filter without mutating original rows
const filteredPlayers = computed(() => {
    if(!positionId.value){ 
    return mapped.value
    }
    const want = String(positionId.value).trim().toLowerCase();
    return mapped.value.filter(p=> p.position?.toLowerCase() === want);
    
})

function clampSeason(v) {
  const n = Number(v || 2021);
  return Math.min(2023, Math.max(2021, n));
}

async function loadTeams() {
  loadingTeams.value = true;
  errorMsg.value = "";
  try {
    teams.value = await fetchTeams(league.value, season.value);

    teams.value.sort((a,b)=> a.name.toLowerCase().localeCompare(b.name.toLowerCase()));

// If no team is selected yet, set default to the first one
if (!teamId.value && teams.value.length) {
  teamId.value = teams.value[0].id;
}


  } catch (e) {
    errorMsg.value = e.message || "Failed to load teams.";
  } finally {
    loadingTeams.value = false;
  }
}

async function loadPlayers() {
  season.value = clampSeason(season.value);
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

// modal state

function openModal(row) {
  activeRow.value = row
  modalOpen.value = true
}

onMounted(loadTeams);
</script>
<style>
  .players-page {
  color: #1f2937; /* dark gray, readable on light cards */
}
</style>