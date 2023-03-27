import { createRouter, createWebHistory } from 'vue-router'
import RegisterView from '../views/RegisterView.vue'
import MembersView from '../views/MembersView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/members',
      name: 'members',
      component: MembersView
    },
    { path: '/:pathMatch(.*)', 
      redirect: '/register' 
    }
  ]
})

export default router
