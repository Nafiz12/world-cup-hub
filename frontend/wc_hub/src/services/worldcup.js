import axios from "axios";

// Hardcoded base URL
const API_BASE_URL = "http://127.0.0.1:8000";

export async function getWorldCupHistory() {
  try {
    const response = await axios.get(`${API_BASE_URL}/api/world-cup-history`);
    return Array.isArray(response.data) ? response.data : (response.data?.data ?? []);
  } catch (error) {
    console.error("Error fetching World Cup history:", error);
    throw error;
  }
}
