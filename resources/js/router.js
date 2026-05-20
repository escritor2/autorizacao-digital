import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/auth'

// Lazy loaded views
import LoginForm from './components/LoginForm.vue'
import LandingPage from './pages/LandingPage.vue'
import Dashboard from './pages/Dashboard.vue'
import AqvDashboard from './pages/aqv/Dashboard.vue'
import NewRequest from './pages/aqv/NewRequest.vue'
import ProfessorDashboard from './pages/professor/Dashboard.vue'
import PorteiroDashboard from './pages/porteiro/Dashboard.vue'
import AdminDashboard from './pages/admin/Dashboard.vue'
import UsersManager from './pages/admin/Users.vue'
import StudentsManager from './pages/admin/Students.vue'
import ClassesManager from './pages/admin/Classes.vue'

const routes = [
  {
    path: '/',
    name: 'landing',
    component: LandingPage,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginForm,
    meta: { guest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { auth: true },
  },
  // AQV / Responsável
  {
    path: '/aqv/dashboard',
    name: 'aqv.dashboard',
    component: AqvDashboard,
    meta: { auth: true, role: 'aqv' },
  },
  {
    path: '/aqv/new-request',
    name: 'aqv.new-request',
    component: NewRequest,
    meta: { auth: true, role: 'aqv' },
  },
  // Professor
  {
    path: '/professor/dashboard',
    name: 'professor.dashboard',
    component: ProfessorDashboard,
    meta: { auth: true, role: 'professor' },
  },
  // Porteiro
  {
    path: '/porteiro/dashboard',
    name: 'porteiro.dashboard',
    component: PorteiroDashboard,
    meta: { auth: true, role: 'porteiro' },
  },
  // Admin
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard',
    component: AdminDashboard,
    meta: { auth: true, role: 'admin' },
  },
  {
    path: '/admin/users',
    name: 'admin.users',
    component: UsersManager,
    meta: { auth: true, role: 'admin' },
  },
  {
    path: '/admin/students',
    name: 'admin.students',
    component: StudentsManager,
    meta: { auth: true, role: 'admin' },
  },
  {
    path: '/admin/classes',
    name: 'admin.classes',
    component: ClassesManager,
    meta: { auth: true, role: 'admin' },
  },
  // Fallback redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Route guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Se tem token local mas não carregou usuário
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }

  const isAuth = !!authStore.user

  if (to.meta.auth && !isAuth) {
    // Precisa de autenticação e não está logado
    return next('/login')
  }

  if (to.meta.guest && isAuth) {
    // Rota de visitante mas já está logado
    return next('/dashboard')
  }

  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    // Usuário não possui a role necessária para acessar
    return next('/dashboard')
  }

  next()
})

export default router
