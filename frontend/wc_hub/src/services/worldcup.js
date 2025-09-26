import axios from "axios";

// Hardcoded base URL
const API_BASE_URL = import.meta.env.VITE_API_BASE 

export async function getWorldCupHistory() {
  try {
    const response = await axios.get(`${API_BASE_URL}/api/world-cup-history`);
    return Array.isArray(response.data) ? response.data : (response.data?.data ?? []);
  } catch (error) {
    console.error("Error fetching World Cup history:", error);
    throw error;
  }
}
