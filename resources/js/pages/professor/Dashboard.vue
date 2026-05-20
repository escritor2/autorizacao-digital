<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Painel do Professor</h2>
        <p class="text-slate-400 text-sm">Gerencie as autorizações de entrada e saída dos alunos das suas turmas.</p>
      </div>
      <button @click="fetchAuthorizations" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium flex items-center gap-1.5 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m0 0l-3 3-3-3" />
        </svg>
        Atualizar Lista
      </button>
    </div>

    <!-- Main List Card -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50 flex items-center justify-between">
        <h3 class="font-bold text-slate-200">Solicitações Pendentes de Avaliação</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Aluno</th>
              <th class="px-6 py-4">Responsável</th>
              <th class="px-6 py-4">Movimentação</th>
              <th class="px-6 py-4">Motivo</th>
              <th class="px-6 py-4">Data Solicitação</th>
              <th class="px-6 py-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500">
                <div class="inline-block animate-spin w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mb-2"></div>
                <p>Carregando solicitações...</p>
              </td>
            </tr>
            <tr v-else-if="authorizations.length === 0" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500">
                Não há nenhuma solicitação pendente no momento.
              </td>
            </tr>
            <tr v-else v-for="auth in authorizations" :key="auth.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4">
                <div>
                  <p class="font-semibold text-slate-100">{{ auth.student?.name }}</p>
                  <p class="text-xs text-slate-400">Turma: {{ auth.class?.name || 'Não informada' }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div>
                  <p class="text-slate-200">{{ auth.guardian?.name }}</p>
                  <p class="text-xs text-slate-400">{{ auth.guardian?.phone }}</p>
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
                <p class="truncate text-slate-200 animate-pulse-slow" :title="auth.reason">{{ auth.reason }}</p>
              </td>
              <td class="px-6 py-4 text-xs text-slate-400">
                {{ formatDate(auth.created_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="approveRequest(auth.id)"
                    class="px-3 py-1.5 rounded-lg bg-emerald-600/10 hover:bg-emerald-600 text-emerald-400 hover:text-white font-medium text-xs border border-emerald-500/20 hover:border-transparent transition-all active:scale-95"
                  >
                    Aprovar
                  </button>
                  <button
                    @click="openRejectModal(auth)"
                    class="px-3 py-1.5 rounded-lg bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white font-medium text-xs border border-rose-500/20 hover:border-transparent transition-all active:scale-95"
                  >
                    Recusar
                  </button>
                </div>
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

    <!-- Reject Modal -->
    <div v-if="rejectModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold text-slate-100">Recusar Solicitação</h3>
          <button @click="closeRejectModal" class="text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div>
          <p class="text-sm text-slate-300">
            Aluno: <strong class="text-slate-100">{{ selectedAuth?.student?.name }}</strong>
          </p>
          <p class="text-sm text-slate-300">
            Movimento: <span class="font-semibold capitalize text-amber-400">{{ selectedAuth?.movement_type === 'entry' ? 'Entrada' : 'Saída' }}</span>
          </p>
        </div>

        <form @submit.prevent="submitRejection" class="space-y-4">
          <div>
            <label for="reject-notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Motivo da Recusa (Obrigatório)
            </label>
            <textarea
              id="reject-notes"
              v-model="rejectNotes"
              rows="3"
              required
              placeholder="Descreva o motivo pelo qual esta solicitação está sendo recusada..."
              class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent text-slate-200 placeholder-slate-500 text-sm"
            ></textarea>
            <p class="text-[10px] text-slate-400 mt-1">Mínimo de 5 caracteres.</p>
          </div>

          <div v-if="modalError" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-3 py-2 rounded-lg text-xs">
            {{ modalError }}
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeRejectModal"
              class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submittingRejection"
              class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs transition-all active:scale-[0.98] disabled:opacity-50"
            >
              {{ submittingRejection ? 'Recusando...' : 'Confirmar Recusa' }}
            </button>
          </div>
        </form>
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

// Modal Reject
const rejectModalOpen = ref(false)
const selectedAuth = ref(null)
const rejectNotes = ref('')
const submittingRejection = ref(false)
const modalError = ref('')

const fetchAuthorizations = async (page = 1) => {
  loading.value = true
  try {
    // A rota GET /authorizations filtra pela role do usuário logado (Professor vê as da sua turma, pendentes)
    const response = await authStore.api.get(`/authorizations?page=${page}`)
    // Filtrar apenas as pendentes de aprovação pelo professor (status === pending_teacher)
    authorizations.value = response.data.data.filter((auth) => auth.status === 'pending_teacher')
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

const approveRequest = async (id) => {
  if (!confirm('Deseja realmente aprovar esta solicitação?')) return
  try {
    await authStore.api.post(`/authorizations/${id}/approve`)
    fetchAuthorizations(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao aprovar solicitação')
  }
}

const openRejectModal = (auth) => {
  selectedAuth.value = auth
  rejectNotes.value = ''
  modalError.value = ''
  rejectModalOpen.value = true
}

const closeRejectModal = () => {
  rejectModalOpen.value = false
  selectedAuth.value = null
}

const submitRejection = async () => {
  if (rejectNotes.value.trim().length < 5) {
    modalError.value = 'O motivo deve conter no mínimo 5 caracteres.'
    return
  }

  submittingRejection.value = true
  modalError.value = ''
  try {
    await authStore.api.post(`/authorizations/${selectedAuth.value.id}/reject`, {
      notes: rejectNotes.value,
    })
    closeRejectModal()
    fetchAuthorizations(pagination.value.current_page)
  } catch (error) {
    modalError.value = error.response?.data?.message || 'Erro ao rejeitar solicitação'
  } finally {
    submittingRejection.value = false
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
  })
}

onMounted(() => {
  fetchAuthorizations()
})
</script>
