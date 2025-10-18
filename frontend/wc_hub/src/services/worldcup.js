import axios from "axios";

// Remove trailing slash if it exists
const API_BASE_URL = import.meta.env.VITE_API_BASE?.replace(/\/$/, "");

/**
 * Fetch World Cup history
 */
export async function getWorldCupHistory() {
  try {
    const history_url = API_BASE_URL + "/api/world-cup-history";
    const response = await axios.get(history_url);
    return Array.isArray(response.data)
      ? response.data
      : (response.data?.data ?? []);
  } catch (error) {
    console.error("Error fetching World Cup history:", error);
    throw error;
  }
}

/**
 * Fetch players list from Laravel test route
 * @param {number} teamId
 * @param {number} season
 */
export async function fetchPlayers(teamId, season) {
  try {
    const players_url = API_BASE_URL + "/api/test/worldcup/players";
    const { data } = await axios.get(players_url, {
      params: { team: teamId, season },
    });

    return Array.isArray(data?.response) ? data.response : [];
  } catch (error) {
    console.error("Error fetching player list:", error);
    throw error;
  }
  
}


export async function fetchTeams(league, season) {
  try {
    const url = `${API_BASE_URL}/api/worldcup/teams`;
    const { data } = await axios.get(url, { params: { league, season }, timeout: 10000 });
    return Array.isArray(data?.teams) ? data.teams : [];
  } catch (err) {
    console.error("fetchTeams failed:", err?.message || err);
    throw new Error("Couldn’t load teams.");
  }
}
