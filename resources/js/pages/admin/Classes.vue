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
          <h2 class="text-2xl font-bold text-slate-100">Gerenciar Turmas</h2>
          <p class="text-slate-400 text-sm">Cadastre turmas escolares e associe-as aos seus professores regentes.</p>
        </div>
      </div>

      <button
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all text-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Turma
      </button>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-bold text-slate-200">Lista de Turmas</h3>

        <div class="flex items-center gap-2">
          <!-- Search -->
          <input
            type="text"
            v-model="filters.search"
            placeholder="Buscar por nome ou série..."
            @input="fetchClasses(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200 placeholder-slate-500 w-56"
          />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Nome da Turma</th>
              <th class="px-6 py-4">Série/Ano</th>
              <th class="px-6 py-4">Professor Regente</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loading" class="text-center">
              <td colspan="5" class="px-6 py-12 text-slate-500">
                Carregando turmas...
              </td>
            </tr>
            <tr v-else-if="classes.length === 0" class="text-center">
              <td colspan="5" class="px-6 py-12 text-slate-500">
                Nenhuma turma encontrada.
              </td>
            </tr>
            <tr v-else v-for="cls in classes" :key="cls.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4 font-semibold text-slate-100">
                {{ cls.name }}
              </td>
              <td class="px-6 py-4 text-slate-300">
                {{ cls.series || '-' }}
              </td>
              <td class="px-6 py-4">
                <div v-if="cls.teacher">
                  <p class="text-slate-200 font-semibold">{{ cls.teacher.name }}</p>
                  <p class="text-xs text-slate-450">{{ cls.teacher.email }}</p>
                </div>
                <span v-else class="text-xs text-amber-400 bg-amber-500/5 px-2.5 py-1 border border-amber-500/10 rounded-lg font-medium">Sem regente associado</span>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2 py-0.5 rounded-full text-xs font-bold',
                    cls.is_active
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                  ]"
                >
                  {{ cls.is_active ? 'Ativa' : 'Inativa' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="openEditModal(cls)"
                    class="px-2.5 py-1 rounded bg-slate-750 hover:bg-slate-700 text-slate-300 text-xs border border-slate-600 transition-all"
                  >
                    Editar
                  </button>
                  <button
                    @click="toggleClassStatus(cls)"
                    :class="[
                      'px-2.5 py-1 rounded text-xs transition-all',
                      cls.is_active
                        ? 'bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/20'
                        : 'bg-emerald-600/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20'
                    ]"
                  >
                    {{ cls.is_active ? 'Desativar' : 'Ativar' }}
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
            {{ isEditing ? 'Editar Turma' : 'Nova Turma' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveClass" class="space-y-4">
          <!-- Nome -->
          <div>
            <label for="class-name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Identificação/Nome da Turma
            </label>
            <input
              type="text"
              id="class-name"
              v-model="form.name"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Ex: 3º Ano Ensino Médio A"
            />
          </div>

          <!-- Série / Ano -->
          <div>
            <label for="series" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Série ou Ano Escolar
            </label>
            <input
              type="text"
              id="series"
              v-model="form.series"
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Ex: 3º Ano, 8ª Série"
            />
          </div>

          <!-- Professor Regente -->
          <div>
            <label for="teacher_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Professor Regente
            </label>
            <select
              id="teacher_id"
              v-model="form.teacher_id"
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-200 text-sm"
            >
              <option value="">Nenhum / Selecione depois...</option>
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                {{ teacher.name }} (Email: {{ teacher.email }})
              </option>
            </select>
          </div>

          <!-- Active Toggle -->
          <div class="flex items-center gap-2 pt-2">
            <input
              type="checkbox"
              id="class_is_active"
              v-model="form.is_active"
              class="rounded text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-slate-700 w-4 h-4"
            />
            <label for="class_is_active" class="text-sm text-slate-350 select-none">Turma Ativa</label>
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
              {{ submitting ? 'Salvando...' : 'Salvar Turma' }}
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

const classes = ref([])
const teachers = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  search: '',
})

// Modal states
const modalOpen = ref(false)
const isEditing = ref(false)
const selectedClass = ref(null)
const submitting = ref(false)
const modalError = ref('')

const form = ref({
  name: '',
  series: '',
  teacher_id: '',
  is_active: true,
})

const fetchClasses = async (page = 1) => {
  loading.value = true
  try {
    let url = `/classes?page=${page}`
    if (filters.value.search) url += `&search=${encodeURIComponent(filters.value.search)}`

    const response = await authStore.api.get(url)
    classes.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar turmas', error)
  } finally {
    loading.value = false
  }
}

const fetchTeachers = async () => {
  try {
    const response = await authStore.api.get('/teachers')
    teachers.value = response.data
  } catch (error) {
    console.error('Erro ao buscar professores', error)
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchClasses(page)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  selectedClass.value = null
  modalError.value = ''
  form.value = {
    name: '',
    series: '',
    teacher_id: '',
    is_active: true,
  }
  modalOpen.value = true
}

const openEditModal = (cls) => {
  isEditing.value = true
  selectedClass.value = cls
  modalError.value = ''
  form.value = {
    name: cls.name,
    series: cls.series || '',
    teacher_id: cls.teacher_id || '',
    is_active: !!cls.is_active,
  }
  modalOpen.value = true
}

const closeModal = () => {
  modalOpen.value = false
  selectedClass.value = null
}

const saveClass = async () => {
  submitting.value = true
  modalError.value = ''
  try {
    if (isEditing.value) {
      await authStore.api.put(`/classes/${selectedClass.value.id}`, form.value)
    } else {
      await authStore.api.post('/classes', form.value)
    }
    closeModal()
    fetchClasses(pagination.value.current_page)
  } catch (error) {
    modalError.value = error.response?.data?.message || 'Erro ao salvar turma'
  } finally {
    submitting.value = false
  }
}

const toggleClassStatus = async (cls) => {
  const action = cls.is_active ? 'desativar' : 'ativar'
  if (!confirm(`Deseja realmente ${action} a turma ${cls.name}?`)) return
  try {
    await authStore.api.delete(`/classes/${cls.id}`)
    fetchClasses(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao alterar status da turma')
  }
}

onMounted(() => {
  fetchClasses()
  fetchTeachers()
})
</script>
