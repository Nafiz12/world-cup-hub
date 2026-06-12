import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import PlayersPage from '@/views/PlayersPage.vue'
import GroupsPage from '@/views/GroupsPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/FW26',
      name: 'fw26',
      component: GroupsPage,
    },
    {
      path: '/players',
      name: 'players',
      component: PlayersPage,
    },
  ],
  scrollBehavior() {
    return { top: 0 }
  },
})

export default router
