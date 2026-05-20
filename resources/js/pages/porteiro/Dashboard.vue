<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Controle da Portaria</h2>
        <p class="text-slate-400 text-sm">Registre a entrada e saída dos alunos autorizados e acompanhe o histórico.</p>
      </div>
      <button @click="refreshAll" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium flex items-center gap-1.5 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m0 0l-3 3-3-3" />
        </svg>
        Atualizar Painel
      </button>
    </div>

    <!-- Quick Search / Pending List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Pending Authorizations Panel (Left/Mid) -->
      <div class="lg:col-span-2 bg-slate-800/30 border border-slate-700/40 rounded-2xl p-6 shadow-xl flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/50 pb-4">
          <h3 class="font-bold text-slate-200 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-ping"></span>
            Liberações Prontas
          </h3>
          <!-- Search input -->
          <div class="relative w-full sm:w-64">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Buscar aluno ou RA..."
              class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-500 text-xs"
            />
            <div class="absolute left-3 top-2.5 text-slate-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Pending Items List -->
        <div class="divide-y divide-slate-700/30 overflow-y-auto max-h-[400px] pr-1">
          <div v-if="loadingPending" class="py-12 text-center text-slate-500">
            <div class="inline-block animate-spin w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mb-2"></div>
            <p>Carregando liberações...</p>
          </div>
          <div v-else-if="filteredPending.length === 0" class="py-12 text-center text-slate-500">
            Nenhuma liberação pendente encontrada.
          </div>
          <div
            v-else
            v-for="auth in filteredPending"
            :key="auth.id"
            class="py-4 flex items-center justify-between gap-4 hover:bg-slate-800/10 transition-colors px-2 rounded-xl"
          >
            <div>
              <p class="font-bold text-slate-100">{{ auth.student?.name }}</p>
              <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                <span>RA: {{ auth.student?.registration }}</span>
                <span>•</span>
                <span>Turma: {{ auth.class?.name }}</span>
              </div>
              <p class="text-xs text-slate-300 mt-1 italic">
                Motivo: "{{ auth.reason }}"
              </p>
            </div>

            <div class="flex items-center gap-3">
              <span
                :class="[
                  'px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider',
                  auth.movement_type === 'entry'
                    ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                    : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                ]"
              >
                {{ auth.movement_type === 'entry' ? 'Entrada' : 'Saída' }}
              </span>
              <button
                @click="openMovementModal(auth)"
                class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/20 transition-all active:scale-[0.97]"
              >
                Validar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats / Actions (Right) -->
      <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-6 shadow-xl space-y-6">
        <h3 class="font-bold text-slate-200 border-b border-slate-700/50 pb-4">
          Resumo do Turno
        </h3>
        
        <div class="space-y-4">
          <div class="p-4 bg-slate-900 border border-slate-800 rounded-xl flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pendentes de Entrada/Saída</p>
              <h4 class="text-3xl font-extrabold text-indigo-400 mt-1">{{ pendingAuthorizations.length }}</h4>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="p-4 bg-slate-900 border border-slate-800 rounded-xl flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Movimentações do Dia</p>
              <h4 class="text-3xl font-extrabold text-emerald-400 mt-1">{{ countMovementsToday() }}</h4>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
              </svg>
            </div>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-900 border border-slate-700/50 text-xs text-slate-400 space-y-2">
          <p class="font-semibold text-slate-300">Instruções de Operação:</p>
          <ul class="list-disc pl-4 space-y-1">
            <li>Identifique o aluno e valide o documento (RA).</li>
            <li>Localize a liberação na lista à esquerda.</li>
            <li>Clique em "Validar", insira notas se necessário (ex: com quem saiu), e confirme.</li>
            <li>O responsável será notificado por e-mail automaticamente.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- History list -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50">
        <h3 class="font-bold text-slate-200">Histórico Recente de Movimentações</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Aluno</th>
              <th class="px-6 py-4">Tipo Movimentação</th>
              <th class="px-6 py-4">Observações</th>
              <th class="px-6 py-4">Horário de Registro</th>
              <th class="px-6 py-4">Registrado por</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loadingHistory" class="text-center">
              <td colspan="5" class="px-6 py-8 text-slate-500">
                Carregando histórico...
              </td>
            </tr>
            <tr v-else-if="movements.length === 0" class="text-center">
              <td colspan="5" class="px-6 py-8 text-slate-500">
                Nenhum movimento registrado por você hoje.
              </td>
            </tr>
            <tr v-else v-for="move in movements" :key="move.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4">
                <div>
                  <p class="font-semibold text-slate-100">{{ move.student?.name }}</p>
                  <p class="text-xs text-slate-400">RA: {{ move.student?.registration }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2.5 py-1 rounded-lg text-xs font-semibold uppercase tracking-wider',
                    move.type === 'entry'
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                  ]"
                >
                  {{ move.type === 'entry' ? 'Entrada' : 'Saída' }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-300">
                {{ move.notes || 'Sem observações' }}
              </td>
              <td class="px-6 py-4 text-xs text-slate-400">
                {{ formatDate(move.registered_at || move.created_at) }}
              </td>
              <td class="px-6 py-4 text-xs text-slate-400">
                {{ move.porteiro?.name }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination History -->
      <div v-if="paginationHistory.last_page > 1" class="p-4 border-t border-slate-700/50 flex items-center justify-between">
        <button
          @click="changeHistoryPage(paginationHistory.current_page - 1)"
          :disabled="paginationHistory.current_page === 1"
          class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium hover:bg-slate-700/50 disabled:opacity-40 transition-colors"
        >
          Anterior
        </button>
        <span class="text-xs text-slate-400">Página {{ paginationHistory.current_page }} de {{ paginationHistory.last_page }}</span>
        <button
          @click="changeHistoryPage(paginationHistory.current_page + 1)"
          :disabled="paginationHistory.current_page === paginationHistory.last_page"
          class="px-3 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium hover:bg-slate-700/50 disabled:opacity-40 transition-colors"
        >
          Próxima
        </button>
      </div>
    </div>

    <!-- Validate Movement Modal -->
    <div v-if="movementModalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold text-slate-100">Registrar Movimentação</h3>
          <button @click="closeMovementModal" class="text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-4 bg-slate-900/60 rounded-xl space-y-2 border border-slate-700/30 text-sm">
          <p class="text-slate-300">Aluno: <strong class="text-slate-100">{{ selectedAuth?.student?.name }}</strong></p>
          <p class="text-slate-300">RA: <span class="font-mono text-slate-200">{{ selectedAuth?.student?.registration }}</span></p>
          <p class="text-slate-300">
            Ação:
            <span
              :class="[
                'ml-1 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider',
                selectedAuth?.movement_type === 'entry' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'
              ]"
            >
              {{ selectedAuth?.movement_type === 'entry' ? 'Entrada' : 'Saída' }}
            </span>
          </p>
          <p class="text-slate-300">Motivo: <span class="italic text-slate-400">"{{ selectedAuth?.reason }}"</span></p>
        </div>

        <form @submit.prevent="submitMovement" class="space-y-4">
          <div>
            <label for="move-notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Observações / Notas da Portaria
            </label>
            <textarea
              id="move-notes"
              v-model="movementNotes"
              rows="3"
              placeholder="Ex: Saiu acompanhado do padrinho, ou entregou documento justificando."
              class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-500 text-sm"
            ></textarea>
          </div>

          <div v-if="modalError" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-3 py-2 rounded-lg text-xs">
            {{ modalError }}
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeMovementModal"
              class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submittingMovement"
              class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all active:scale-[0.98] disabled:opacity-50"
            >
              {{ submittingMovement ? 'Registrando...' : 'Confirmar e Registrar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

// Pending List
const pendingAuthorizations = ref([])
const loadingPending = ref(false)
const searchQuery = ref('')

// History List
const movements = ref([])
const loadingHistory = ref(false)
const paginationHistory = ref({
  current_page: 1,
  last_page: 1,
})

// Modal Validate
const movementModalOpen = ref(false)
const selectedAuth = ref(null)
const movementNotes = ref('')
const submittingMovement = ref(false)
const modalError = ref('')

const fetchPending = async () => {
  loadingPending.value = true
  try {
    // Porteiros em GET /authorizations recebem apenas as com status ready_porteiro
    const response = await authStore.api.get('/authorizations')
    pendingAuthorizations.value = response.data.data
  } catch (error) {
    console.error('Erro ao buscar liberações pendentes', error)
  } finally {
    loadingPending.value = false
  }
}

const fetchHistory = async (page = 1) => {
  loadingHistory.value = true
  try {
    const response = await authStore.api.get(`/movements?page=${page}`)
    movements.value = response.data.data
    paginationHistory.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar histórico de movimentos', error)
  } finally {
    loadingHistory.value = false
  }
}

const refreshAll = () => {
  fetchPending()
  fetchHistory(1)
}

const filteredPending = computed(() => {
  if (!searchQuery.value.trim()) return pendingAuthorizations.value
  const q = searchQuery.value.toLowerCase()
  return pendingAuthorizations.value.filter((auth) => {
    return (
      auth.student?.name.toLowerCase().includes(q) ||
      auth.student?.registration.toLowerCase().includes(q)
    );
  })
})

const changeHistoryPage = (page) => {
  if (page >= 1 && page <= paginationHistory.value.last_page) {
    fetchHistory(page)
  }
}

const countMovementsToday = () => {
  const today = new Date().toDateString()
  return movements.value.filter((move) => {
    const date = new Date(move.registered_at || move.created_at)
    return date.toDateString() === today
  }).length
}

const openMovementModal = (auth) => {
  selectedAuth.value = auth
  movementNotes.value = ''
  modalError.value = ''
  movementModalOpen.value = true
}

const closeMovementModal = () => {
  movementModalOpen.value = false
  selectedAuth.value = null
}

const submitMovement = async () => {
  submittingMovement.value = true
  modalError.value = ''
  try {
    await authStore.api.post(`/authorizations/${selectedAuth.value.id}/register-movement`, {
      notes: movementNotes.value,
    })
    closeMovementModal()
    refreshAll()
  } catch (error) {
    modalError.value = error.response?.data?.message || 'Erro ao registrar movimento'
  } finally {
    submittingMovement.value = false
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
  refreshAll()
})
</script>
