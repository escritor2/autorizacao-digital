<template>
  <div class="p-6 max-w-2xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex items-center gap-3">
      <router-link
        to="/aqv/dashboard"
        class="p-2 rounded-lg bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </router-link>
      <div>
        <h2 class="text-2xl font-bold text-slate-100">Nova Autorização</h2>
        <p class="text-slate-400 text-sm">Crie uma solicitação de entrada ou saída para um aluno.</p>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-6 shadow-xl space-y-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Student Select -->
        <div>
          <label for="student" class="block text-sm font-medium text-slate-300 mb-1.5">
            Selecione o Aluno
          </label>
          <select
            v-model="form.student_id"
            id="student"
            required
            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200"
          >
            <option value="" disabled>Escolha um aluno...</option>
            <option v-for="student in students" :key="student.id" :value="student.id">
              {{ student.name }} (RA: {{ student.registration }})
            </option>
          </select>
          <p v-if="students.length === 0 && !loadingStudents" class="text-xs text-amber-400 mt-1.5">
            Nenhum aluno ativo vinculado à sua conta.
          </p>
        </div>

        <!-- Movement Type -->
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-3">
            Tipo de Movimentação
          </label>
          <div class="grid grid-cols-2 gap-4">
            <button
              type="button"
              @click="form.movement_type = 'entry'"
              :class="[
                'py-3.5 px-4 rounded-xl font-semibold border flex items-center justify-center gap-2 transition-all active:scale-[0.98]',
                form.movement_type === 'entry'
                  ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500'
                  : 'bg-slate-900 text-slate-400 border-slate-700 hover:text-slate-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1" />
              </svg>
              Entrada
            </button>

            <button
              type="button"
              @click="form.movement_type = 'exit'"
              :class="[
                'py-3.5 px-4 rounded-xl font-semibold border flex items-center justify-center gap-2 transition-all active:scale-[0.98]',
                form.movement_type === 'exit'
                  ? 'bg-amber-500/10 text-amber-400 border-amber-500'
                  : 'bg-slate-900 text-slate-400 border-slate-700 hover:text-slate-300'
              ]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1" />
              </svg>
              Saída
            </button>
          </div>
        </div>

        <!-- Reason -->
        <div>
          <label for="reason" class="block text-sm font-medium text-slate-300 mb-1.5">
            Justificativa / Motivo
          </label>
          <textarea
            v-model="form.reason"
            id="reason"
            rows="4"
            required
            placeholder="Ex: Consulta médica agendada para as 14h, ou atraso devido a problemas no transporte."
            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-500"
          ></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Mínimo de 5 caracteres.</p>
        </div>

        <!-- Notifications Info -->
        <div class="p-4 rounded-xl bg-indigo-500/5 border border-indigo-500/10 flex gap-3 text-sm text-slate-300">
          <div class="text-indigo-400 mt-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <p>
            O professor responsável pela turma do aluno receberá um e-mail de notificação para aprovar ou rejeitar esta solicitação.
          </p>
        </div>

        <!-- Error Panel -->
        <div v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-sm">
          {{ error }}
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <router-link
            to="/aqv/dashboard"
            class="px-5 py-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 font-semibold hover:bg-slate-700/40 transition-colors text-sm"
          >
            Cancelar
          </router-link>
          <button
            type="submit"
            :disabled="loading"
            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50 text-sm"
          >
            {{ loading ? 'Enviando...' : 'Criar Solicitação' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const students = ref([])
const loadingStudents = ref(false)
const loading = ref(false)
const error = ref('')

const form = ref({
  student_id: '',
  movement_type: 'exit',
  reason: '',
})

const fetchMyStudents = async () => {
  loadingStudents.value = true
  try {
    const response = await authStore.api.get('/students-all')
    students.value = response.data
  } catch (error) {
    console.error('Erro ao buscar alunos', error)
  } finally {
    loadingStudents.value = false
  }
}

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  try {
    await authStore.api.post('/authorizations', form.value)
    router.push('/aqv/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao enviar solicitação'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchMyStudents()
})
</script>
