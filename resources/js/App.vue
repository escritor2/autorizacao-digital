<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans antialiased">
    <!-- Top Navbar (Only visible if authenticated) -->
    <header v-if="authStore.isAuthenticated" class="bg-slate-800/80 backdrop-blur-md border-b border-slate-700/50 sticky top-0 z-50 px-6 py-4 flex items-center justify-between shadow-lg">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white shadow-md shadow-indigo-500/20">
          S
        </div>
        <div>
          <h1 class="text-lg font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">SAFE</h1>
          <p class="text-xs text-slate-400">Fluxo & Autorização Escolar</p>
        </div>
      </div>

      <div class="flex items-center gap-6">
        <!-- User Badge -->
        <div class="flex items-center gap-3 px-3 py-1.5 rounded-xl bg-slate-700/30 border border-slate-700/50">
          <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
          <div class="text-left">
            <p class="text-sm font-semibold text-slate-200">{{ authStore.user?.name }}</p>
            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">{{ roleName(authStore.user?.role) }}</p>
          </div>
        </div>

        <!-- Navigation Links for Desktop -->
        <nav class="hidden md:flex items-center gap-2">
          <router-link to="/dashboard" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/40 transition-all">
            Início
          </router-link>
          
          <button @click="handleLogout" class="px-3 py-2 rounded-lg text-sm font-medium text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all">
            Sair
          </button>
        </nav>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-700/40">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
      </div>
    </header>

    <!-- Mobile Menu -->
    <div v-if="authStore.isAuthenticated && mobileMenuOpen" class="md:hidden bg-slate-800 border-b border-slate-700 px-6 py-4 flex flex-col gap-2">
      <router-link @click="mobileMenuOpen = false" to="/dashboard" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/40 transition-all">
        Painel Geral
      </router-link>
      <button @click="handleLogout(); mobileMenuOpen = false" class="text-left px-3 py-2 rounded-lg text-sm font-medium text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all">
        Sair do Sistema
      </button>
    </div>

    <!-- Main Container -->
    <main class="flex-grow flex flex-col">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 py-4 px-6 border-t border-slate-800/50 text-center text-xs text-slate-500">
      <p>&copy; {{ new Date().getFullYear() }} SAFE. Desenvolvido para máxima segurança e rastreabilidade escolar.</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const roleName = (role) => {
  const roles = {
    admin: 'Administrador',
    aqv: 'Responsável',
    professor: 'Professor',
    porteiro: 'Porteiro',
  }
  return roles[role] || role
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(async () => {
  if (authStore.token) {
    await authStore.fetchUser()
  }
})
</script>

<style>
/* Custom transitions and scrollbar */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #0f172a;
}
::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #475569;
}
</style>
