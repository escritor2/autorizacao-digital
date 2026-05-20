<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Painel do Administrador</h2>
        <p class="text-slate-400 text-sm">Gerencie usuários, alunos, turmas e monitore os logs do sistema.</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <router-link
          to="/admin/users"
          class="px-4 py-2 rounded-xl bg-slate-800 border border-slate-700/50 hover:bg-slate-700/50 hover:border-slate-600 text-slate-200 font-semibold text-xs transition-all active:scale-[0.98]"
        >
          Gerenciar Usuários
        </router-link>
        <router-link
          to="/admin/classes"
          class="px-4 py-2 rounded-xl bg-slate-800 border border-slate-700/50 hover:bg-slate-700/50 hover:border-slate-600 text-slate-200 font-semibold text-xs transition-all active:scale-[0.98]"
        >
          Gerenciar Turmas
        </router-link>
        <router-link
          to="/admin/students"
          class="px-4 py-2 rounded-xl bg-slate-800 border border-slate-700/50 hover:bg-slate-700/50 hover:border-slate-600 text-slate-200 font-semibold text-xs transition-all active:scale-[0.98]"
        >
          Gerenciar Alunos
        </router-link>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- We can fetch stats or render placeholders/simple metrics -->
      <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/5 border border-indigo-500/20 rounded-2xl p-6 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold">U</div>
        <div>
          <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Gestão do Sistema</h4>
          <p class="text-lg font-bold text-slate-200 mt-0.5">Usuários e Permissões</p>
          <router-link to="/admin/users" class="text-xs text-indigo-400 hover:underline mt-1 block">Acessar lista &rarr;</router-link>
        </div>
      </div>

      <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/5 border border-emerald-500/20 rounded-2xl p-6 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold">A</div>
        <div>
          <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Matrículas</h4>
          <p class="text-lg font-bold text-slate-200 mt-0.5">Alunos e Responsáveis</p>
          <router-link to="/admin/students" class="text-xs text-emerald-400 hover:underline mt-1 block">Acessar lista &rarr;</router-link>
        </div>
      </div>

      <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/5 border border-amber-500/20 rounded-2xl p-6 shadow-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400 font-bold">T</div>
        <div>
          <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pedagógico</h4>
          <p class="text-lg font-bold text-slate-200 mt-0.5">Turmas e Professores</p>
          <router-link to="/admin/classes" class="text-xs text-amber-400 hover:underline mt-1 block">Acessar lista &rarr;</router-link>
        </div>
      </div>
    </div>

    <!-- Logs Viewer Section -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl flex flex-col">
      <div class="p-6 border-b border-slate-700/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h3 class="font-bold text-slate-200">Logs de Auditoria do Sistema</h3>
          <p class="text-slate-400 text-xs mt-0.5">Acompanhe as ações realizadas no sistema pelos usuários em tempo real.</p>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-2">
          <!-- Search -->
          <input
            type="text"
            v-model="filters.search"
            placeholder="Buscar por mensagem/usuário..."
            @input="fetchLogs(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200 placeholder-slate-500"
          />

          <!-- Action -->
          <select
            v-model="filters.action"
            @change="fetchLogs(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200"
          >
            <option value="">Todas as Ações</option>
            <option value="auth.login">Login</option>
            <option value="auth.logout">Logout</option>
            <option value="authorization.create">Criar Solicitação</option>
            <option value="authorization.approve">Aprovação Professor</option>
            <option value="authorization.reject">Recusa Professor</option>
            <option value="movement.register">Registro de Portaria</option>
          </select>

          <!-- Level -->
          <select
            v-model="filters.level"
            @change="fetchLogs(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200"
          >
            <option value="">Todos os Níveis</option>
            <option value="info">INFO</option>
            <option value="warning">WARNING</option>
            <option value="error">ERROR</option>
          </select>
        </div>
      </div>

      <!-- Logs list/table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Data/Hora</th>
              <th class="px-6 py-4">Nível</th>
              <th class="px-6 py-4">Ação</th>
              <th class="px-6 py-4">Mensagem</th>
              <th class="px-6 py-4">Usuário</th>
              <th class="px-6 py-4">Detalhes</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-xs font-mono text-slate-300">
            <tr v-if="loadingLogs" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500">
                Carregando logs de auditoria...
              </td>
            </tr>
            <tr v-else-if="logs.length === 0" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500 font-sans">
                Nenhum log encontrado para os filtros selecionados.
              </td>
            </tr>
            <tr v-else v-for="log in logs" :key="log.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-slate-400 text-[11px]">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-0.5 rounded text-[10px] font-bold uppercase',
                    log.level === 'info' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '',
                    log.level === 'warning' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '',
                    log.level === 'error' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : ''
                  ]"
                >
                  {{ log.level }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-slate-200">
                {{ log.action }}
              </td>
              <td class="px-6 py-4 font-sans text-slate-300">
                {{ log.message }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap font-sans text-slate-400">
                {{ log.user ? log.user.name : 'Sistema/Visitante' }}
              </td>
              <td class="px-6 py-4 text-slate-400 max-w-[200px] truncate" :title="JSON.stringify(log.context)">
                {{ log.context ? JSON.stringify(log.context) : '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Logs -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-slate-700/50 flex items-center justify-between">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium hover:bg-slate-700/50 disabled:opacity-40 transition-colors"
        >
          Anterior
        </button>
        <span class="text-xs text-slate-400 font-sans">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium hover:bg-slate-700/50 disabled:opacity-40 transition-colors"
        >
          Próxima
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const logs = ref([])
const loadingLogs = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  search: '',
  action: '',
  level: '',
})

const fetchLogs = async (page = 1) => {
  loadingLogs.value = true
  try {
    let url = `/logs?page=${page}`
    if (filters.value.search) url += `&search=${encodeURIComponent(filters.value.search)}`
    if (filters.value.action) url += `&action=${encodeURIComponent(filters.value.action)}`
    if (filters.value.level) url += `&level=${encodeURIComponent(filters.value.level)}`

    const response = await authStore.api.get(url)
    logs.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar logs', error)
  } finally {
    loadingLogs.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchLogs(page)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

onMounted(() => {
  fetchLogs()
})
</script>
