import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import PlayersPage from '@/views/PlayersPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path :'/players',
      name: "players",
      component: PlayersPage
    }
  ],
    scrollBehavior() {
    return { top: 0 }
  },
})

export default router
