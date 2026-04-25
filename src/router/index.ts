import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import MainLayout from '@/components/layout/MainLayout.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'

import HomePage from '@/pages/HomePage.vue'
import TeamPage from '@/pages/TeamPage.vue'
import CompetitionsPage from '@/pages/CompetitionsPage.vue'
import ResearchPage from '@/pages/ResearchPage.vue'
import AdvisorsPage from '@/pages/AdvisorsPage.vue'
import ArticleList from '@/pages/ArticleList.vue'
import ArticleDetail from '@/pages/ArticleDetail.vue'

import AdminLogin from '@/pages/admin/AdminLogin.vue'
import AdminDashboard from '@/pages/admin/AdminDashboard.vue'
import AdminArticles from '@/pages/admin/AdminArticles.vue'
import AdminMembers from '@/pages/admin/AdminMembers.vue'
import AdminCompetitions from '@/pages/admin/AdminCompetitions.vue'
import AdminProjects from '@/pages/admin/AdminProjects.vue'
import AdminTimeline from '@/pages/admin/AdminTimeline.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', name: 'home', component: HomePage },
      { path: 'team', name: 'team', component: TeamPage },
      { path: 'competitions', name: 'competitions', component: CompetitionsPage },
      { path: 'research', name: 'research', component: ResearchPage },
      { path: 'advisors', name: 'advisors', component: AdvisorsPage },
      { path: 'articles', name: 'articles', component: ArticleList },
      { path: 'article/:id', name: 'article', component: ArticleDetail }
    ]
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: AdminLogin
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      { path: 'dashboard', name: 'admin-dashboard', component: AdminDashboard },
      { path: 'articles', name: 'admin-articles', component: AdminArticles },
      { path: 'members', name: 'admin-members', component: AdminMembers },
      { path: 'competitions', name: 'admin-competitions', component: AdminCompetitions },
      { path: 'projects', name: 'admin-projects', component: AdminProjects },
      { path: 'timeline', name: 'admin-timeline', component: AdminTimeline },
      { path: '', redirect: '/admin/dashboard' }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('admin_token')

  if (to.meta.requiresAuth && !token) {
    next('/admin/login')
    return
  }

  next()
})

export default router
