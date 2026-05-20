<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6 w-full">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <router-link
          to="/admin/dashboard"
          class="p-2 rounded-lg bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </router-link>
        <div>
          <h2 class="text-2xl font-bold text-slate-100">Gerenciar Alunos</h2>
          <p class="text-slate-400 text-sm">Cadastre alunos, vincule-os a responsáveis e associe-os a turmas.</p>
        </div>
      </div>

      <button
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all text-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Novo Aluno
      </button>
    </div>

    <!-- Filters & Table -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-bold text-slate-200">Lista de Alunos</h3>

        <div class="flex items-center gap-2">
          <!-- Search -->
          <input
            type="text"
            v-model="filters.search"
            placeholder="Buscar por nome ou RA..."
            @input="fetchStudents(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200 placeholder-slate-500 w-56"
          />

          <!-- Class Filter -->
          <select
            v-model="filters.class_id"
            @change="fetchStudents(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200"
          >
            <option value="">Todas as Turmas</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
              {{ cls.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Nome</th>
              <th class="px-6 py-4">Matrícula (RA)</th>
              <th class="px-6 py-4">Turma</th>
              <th class="px-6 py-4">Responsável</th>
              <th class="px-6 py-4">Nascimento</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="px-6 py-12 text-slate-500">
                Carregando alunos...
              </td>
            </tr>
            <tr v-else-if="students.length === 0" class="text-center">
              <td colspan="7" class="px-6 py-12 text-slate-500">
                Nenhum aluno encontrado.
              </td>
            </tr>
            <tr v-else v-for="student in students" :key="student.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4 font-semibold text-slate-100">
                {{ student.name }}
              </td>
              <td class="px-6 py-4 font-mono text-slate-350">
                {{ student.registration }}
              </td>
              <td class="px-6 py-4 text-slate-300">
                {{ student.class ? student.class.name : 'Não vinculada' }}
              </td>
              <td class="px-6 py-4">
                <div>
                  <p class="text-slate-200 font-semibold">{{ student.guardian ? student.guardian.name : 'Não vinculado' }}</p>
                  <p class="text-xs text-slate-400">{{ student.guardian?.phone }}</p>
                </div>
              </td>
              <td class="px-6 py-4 text-slate-400 text-xs">
                {{ formatDate(student.date_of_birth) }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2 py-0.5 rounded-full text-xs font-bold',
                    student.is_active
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                  ]"
                >
                  {{ student.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="openEditModal(student)"
                    class="px-2.5 py-1 rounded bg-slate-750 hover:bg-slate-700 text-slate-300 text-xs border border-slate-600 transition-all"
                  >
                    Editar
                  </button>
                  <button
                    @click="toggleStudentStatus(student)"
                    :class="[
                      'px-2.5 py-1 rounded text-xs transition-all',
                      student.is_active
                        ? 'bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/20'
                        : 'bg-emerald-600/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20'
                    ]"
                  >
                    {{ student.is_active ? 'Desativar' : 'Ativar' }}
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

    <!-- Modal Form (Create / Edit) -->
    <div v-if="modalOpen" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-lg shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold text-slate-100">
            {{ isEditing ? 'Editar Aluno' : 'Novo Aluno' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveStudent" class="space-y-4">
          <!-- Nome -->
          <div>
            <label for="student-name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Nome Completo do Aluno
            </label>
            <input
              type="text"
              id="student-name"
              v-model="form.name"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Digite o nome do aluno"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- RA / Registration -->
            <div>
              <label for="registration" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                Matrícula (RA)
              </label>
              <input
                type="text"
                id="registration"
                v-model="form.registration"
                required
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
                placeholder="Ex: RA123456"
              />
            </div>

            <!-- Date of Birth -->
            <div>
              <label for="dob" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                Data de Nascimento
              </label>
              <input
                type="date"
                id="dob"
                v-model="form.date_of_birth"
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-200 text-sm"
              />
            </div>
          </div>

          <!-- Turma -->
          <div>
            <label for="class_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Turma Vinculada
            </label>
            <select
              id="class_id"
              v-model="form.class_id"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-200 text-sm"
            >
              <option value="" disabled>Selecione uma turma...</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                {{ cls.name }}
              </option>
            </select>
          </div>

          <!-- Responsável (Guardian) -->
          <div>
            <label for="guardian_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Responsável Vinculado (Responsável/AQV)
            </label>
            <select
              id="guardian_id"
              v-model="form.guardian_id"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-200 text-sm"
            >
              <option value="" disabled>Selecione um responsável...</option>
              <option v-for="guard in guardians" :key="guard.id" :value="guard.id">
                {{ guard.name }} (Email: {{ guard.email }})
              </option>
            </select>
          </div>

          <!-- Active Toggle -->
          <div class="flex items-center gap-2 pt-2">
            <input
              type="checkbox"
              id="student_is_active"
              v-model="form.is_active"
              class="rounded text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-slate-700 w-4 h-4"
            />
            <label for="student_is_active" class="text-sm text-slate-350 select-none">Aluno Ativo</label>
          </div>

          <div v-if="modalError" class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-3 py-2 rounded-lg text-xs">
            {{ modalError }}
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-750">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-xs transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all active:scale-[0.98] disabled:opacity-50"
            >
              {{ submitting ? 'Salvando...' : 'Salvar Aluno' }}
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

const students = ref([])
const classes = ref([])
const guardians = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  search: '',
  class_id: '',
})

// Modal states
const modalOpen = ref(false)
const isEditing = ref(false)
const selectedStudent = ref(null)
const submitting = ref(false)
const modalError = ref('')

const form = ref({
  name: '',
  registration: '',
  class_id: '',
  guardian_id: '',
  date_of_birth: '',
  is_active: true,
})

const fetchStudents = async (page = 1) => {
  loading.value = true
  try {
    let url = `/students?page=${page}`
    if (filters.value.search) url += `&search=${encodeURIComponent(filters.value.search)}`
    if (filters.value.class_id) url += `&class_id=${encodeURIComponent(filters.value.class_id)}`

    const response = await authStore.api.get(url)
    students.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar alunos', error)
  } finally {
    loading.value = false
  }
}

const fetchClassesAndGuardians = async () => {
  try {
    const classRes = await authStore.api.get('/classes-all')
    classes.value = classRes.data

    const guardRes = await authStore.api.get('/guardians')
    guardians.value = guardRes.data
  } catch (error) {
    console.error('Erro ao buscar dados auxiliares', error)
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchStudents(page)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  selectedStudent.value = null
  modalError.value = ''
  form.value = {
    name: '',
    registration: '',
    class_id: classes.value[0]?.id || '',
    guardian_id: guardians.value[0]?.id || '',
    date_of_birth: '',
    is_active: true,
  }
  modalOpen.value = true
}

const openEditModal = (student) => {
  isEditing.value = true
  selectedStudent.value = student
  modalError.value = ''
  form.value = {
    name: student.name,
    registration: student.registration,
    class_id: student.class_id,
    guardian_id: student.guardian_id,
    date_of_birth: student.date_of_birth || '',
    is_active: !!student.is_active,
  }
  modalOpen.value = true
}

const closeModal = () => {
  modalOpen.value = false
  selectedStudent.value = null
}

const saveStudent = async () => {
  submitting.value = true
  modalError.value = ''
  try {
    if (isEditing.value) {
      await authStore.api.put(`/students/${selectedStudent.value.id}`, form.value)
    } else {
      await authStore.api.post('/students', form.value)
    }
    closeModal()
    fetchStudents(pagination.value.current_page)
  } catch (error) {
    modalError.value = error.response?.data?.message || 'Erro ao salvar aluno'
  } finally {
    submitting.value = false
  }
}

const toggleStudentStatus = async (student) => {
  const action = student.is_active ? 'desativar' : 'ativar'
  if (!confirm(`Deseja realmente ${action} o aluno ${student.name}?`)) return
  try {
    await authStore.api.delete(`/students/${student.id}`)
    fetchStudents(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao alterar status do aluno')
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR')
}

onMounted(() => {
  fetchStudents()
  fetchClassesAndGuardians()
})
</script>
