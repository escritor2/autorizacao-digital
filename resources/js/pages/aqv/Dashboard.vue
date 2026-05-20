<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Painel do Responsável</h2>
        <p class="text-slate-400 text-sm">Acompanhe as autorizações de seus alunos vinculados.</p>
      </div>
      <router-link
        to="/aqv/new-request"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Solicitação
      </router-link>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Aguardando Professor</p>
          <h3 class="text-2xl font-bold text-slate-100 mt-1">{{ countByStatus('pending_teacher') }}</h3>
        </div>
      </div>

      <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Pronto na Portaria</p>
          <h3 class="text-2xl font-bold text-slate-100 mt-1">{{ countByStatus('ready_porteiro') }}</h3>
        </div>
      </div>

      <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Finalizados</p>
          <h3 class="text-2xl font-bold text-slate-100 mt-1">{{ countByStatus('completed') }}</h3>
        </div>
      </div>

      <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Recusados</p>
          <h3 class="text-2xl font-bold text-slate-100 mt-1">{{ countByStatus('rejected_teacher') }}</h3>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
        <h3 class="font-bold text-slate-200">Histórico de Solicitações</h3>
        <button @click="fetchAuthorizations" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium flex items-center gap-1.5 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m0 0l-3 3-3-3" />
          </svg>
          Atualizar
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Aluno</th>
              <th class="px-6 py-4">Tipo</th>
              <th class="px-6 py-4">Motivo / Detalhes</th>
              <th class="px-6 py-4">Criado em</th>
              <th class="px-6 py-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loading" class="text-center">
              <td colspan="5" class="px-6 py-12 text-slate-500">
                <div class="inline-block animate-spin w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mb-2"></div>
                <p>Carregando solicitações...</p>
              </td>
            </tr>
            <tr v-else-if="authorizations.length === 0" class="text-center">
              <td colspan="5" class="px-6 py-12 text-slate-500">
                Nenhuma solicitação encontrada.
              </td>
            </tr>
            <tr v-else v-for="auth in authorizations" :key="auth.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4">
                <div>
                  <p class="font-semibold text-slate-100">{{ auth.student?.name }}</p>
                  <p class="text-xs text-slate-400">Reg: {{ auth.student?.registration }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2.5 py-1 rounded-lg text-xs font-semibold uppercase tracking-wider',
                    auth.movement_type === 'entry'
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                  ]"
                >
                  {{ auth.movement_type === 'entry' ? 'Entrada' : 'Saída' }}
                </span>
              </td>
              <td class="px-6 py-4 max-w-xs">
                <div>
                  <p class="truncate text-slate-200" :title="auth.reason">{{ auth.reason }}</p>
                  <p v-if="auth.status === 'rejected_teacher'" class="text-xs text-rose-400 mt-1 font-medium">
                    Motivo Recusa: "{{ auth.teacher_notes }}"
                  </p>
                  <p v-if="auth.status === 'completed'" class="text-xs text-slate-400 mt-1">
                    Registrado na portaria por: {{ auth.porteiro?.name }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-4 text-xs text-slate-400">
                {{ formatDate(auth.created_at) }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-bold tracking-wide border',
                    statusStyles(auth.status)
                  ]"
                >
                  {{ statusLabel(auth.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-slate-700/50 flex items-center justify-between">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium hover:bg-slate-700/50 disabled:opacity-40 transition-colors"
        >
          Anterior
        </button>
        <span class="text-xs text-slate-400">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
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
const authorizations = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
})

const fetchAuthorizations = async (page = 1) => {
  loading.value = true
  try {
    const response = await authStore.api.get(`/authorizations?page=${page}`)
    authorizations.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar autorizações', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchAuthorizations(page)
  }
}

const countByStatus = (status) => {
  return authorizations.value.filter((auth) => auth.status === status).length
}

const statusLabel = (status) => {
  const labels = {
    pending_teacher: 'Pendente Professor',
    ready_porteiro: 'Liberado Portaria',
    rejected_teacher: 'Recusado',
    completed: 'Finalizado',
  }
  return labels[status] || status
}

const statusStyles = (status) => {
  const styles = {
    pending_teacher: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    ready_porteiro: 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
    rejected_teacher: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
    completed: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
  }
  return styles[status] || ''
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
  })
}

onMounted(() => {
  fetchAuthorizations()
})
</script>
