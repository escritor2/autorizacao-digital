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
          <h2 class="text-2xl font-bold text-slate-100">Gerenciar Usuários</h2>
          <p class="text-slate-400 text-sm">Cadastre, edite e ative ou desative contas de acesso ao sistema.</p>
        </div>
      </div>
      
      <button
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all text-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Novo Usuário
      </button>
    </div>

    <!-- Filters and Table Card -->
    <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl overflow-hidden shadow-xl">
      <div class="p-6 border-b border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-bold text-slate-200">Lista de Usuários</h3>

        <div class="flex items-center gap-2">
          <!-- Search -->
          <input
            type="text"
            v-model="filters.search"
            placeholder="Buscar por nome ou e-mail..."
            @input="fetchUsers(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200 placeholder-slate-500 w-56"
          />

          <!-- Role -->
          <select
            v-model="filters.role"
            @change="fetchUsers(1)"
            class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-1 focus:ring-indigo-500 text-xs text-slate-200"
          >
            <option value="">Todos os Perfis</option>
            <option value="admin">Administrador</option>
            <option value="aqv">Responsável (AQV)</option>
            <option value="professor">Professor</option>
            <option value="porteiro">Porteiro</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-800/60 border-b border-slate-700/50 text-xs font-bold text-slate-400 uppercase tracking-wider">
              <th class="px-6 py-4">Nome</th>
              <th class="px-6 py-4">E-mail</th>
              <th class="px-6 py-4">Perfil</th>
              <th class="px-6 py-4">Telefone</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/30 text-sm text-slate-300">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500">
                Carregando usuários...
              </td>
            </tr>
            <tr v-else-if="users.length === 0" class="text-center">
              <td colspan="6" class="px-6 py-12 text-slate-500">
                Nenhum usuário encontrado.
              </td>
            </tr>
            <tr v-else v-for="user in users" :key="user.id" class="hover:bg-slate-800/20 transition-colors">
              <td class="px-6 py-4 font-semibold text-slate-100">
                {{ user.name }}
              </td>
              <td class="px-6 py-4 text-slate-300">
                {{ user.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold tracking-wide uppercase bg-slate-900 border border-slate-700 text-slate-300">
                  {{ roleLabel(user.role) }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-400">
                {{ user.phone || '-' }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-2 py-0.5 rounded-full text-xs font-bold',
                    user.is_active
                      ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                      : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                  ]"
                >
                  {{ user.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="openEditModal(user)"
                    class="px-2.5 py-1 rounded bg-slate-750 hover:bg-slate-700 text-slate-300 text-xs border border-slate-600 transition-all"
                  >
                    Editar
                  </button>
                  <button
                    @click="toggleUserStatus(user)"
                    :disabled="authStore.user?.id === user.id"
                    :class="[
                      'px-2.5 py-1 rounded text-xs transition-all disabled:opacity-30',
                      user.is_active
                        ? 'bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/20 hover:border-transparent'
                        : 'bg-emerald-600/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20 hover:border-transparent'
                    ]"
                  >
                    {{ user.is_active ? 'Desativar' : 'Ativar' }}
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
            {{ isEditing ? 'Editar Usuário' : 'Novo Usuário' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveUser" class="space-y-4">
          <!-- Nome -->
          <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Nome Completo
            </label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Digite o nome do usuário"
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              E-mail
            </label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              required
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Ex: usuario@escola.com"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Perfil / Role -->
            <div>
              <label for="role" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                Perfil de Acesso
              </label>
              <select
                id="role"
                v-model="form.role"
                required
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-200 text-sm"
              >
                <option value="admin">Administrador</option>
                <option value="aqv">Responsável (AQV)</option>
                <option value="professor">Professor</option>
                <option value="porteiro">Porteiro</option>
              </select>
            </div>

            <!-- Telefone -->
            <div>
              <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                Telefone
              </label>
              <input
                type="text"
                id="phone"
                v-model="form.phone"
                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
                placeholder="Ex: (11) 99999-9999"
              />
            </div>
          </div>

          <!-- Senha -->
          <div>
            <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
              Senha {{ isEditing ? '(deixe em branco para não alterar)' : '' }}
            </label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              :required="!isEditing"
              class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-650 text-sm"
              placeholder="Mínimo de 6 caracteres"
            />
          </div>

          <!-- Active Toggle -->
          <div class="flex items-center gap-2 pt-2">
            <input
              type="checkbox"
              id="is_active"
              v-model="form.is_active"
              class="rounded text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-slate-700 w-4 h-4"
            />
            <label for="is_active" class="text-sm text-slate-350 select-none">Usuário Ativo (Pode acessar o sistema)</label>
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
              {{ submitting ? 'Salvando...' : 'Salvar Usuário' }}
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

const users = ref([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  search: '',
  role: '',
})

// Modal controls
const modalOpen = ref(false)
const isEditing = ref(false)
const selectedUser = ref(null)
const submitting = ref(false)
const modalError = ref('')

const form = ref({
  name: '',
  email: '',
  role: 'aqv',
  phone: '',
  password: '',
  is_active: true,
})

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    let url = `/users?page=${page}`
    if (filters.value.search) url += `&search=${encodeURIComponent(filters.value.search)}`
    if (filters.value.role) url += `&role=${encodeURIComponent(filters.value.role)}`

    const response = await authStore.api.get(url)
    users.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
    }
  } catch (error) {
    console.error('Erro ao buscar usuários', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  selectedUser.value = null
  modalError.value = ''
  form.value = {
    name: '',
    email: '',
    role: 'aqv',
    phone: '',
    password: '',
    is_active: true,
  }
  modalOpen.value = true
}

const openEditModal = (user) => {
  isEditing.value = true
  selectedUser.value = user
  modalError.value = ''
  form.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    phone: user.phone || '',
    password: '',
    is_active: !!user.is_active,
  }
  modalOpen.value = true
}

const closeModal = () => {
  modalOpen.value = false
  selectedUser.value = null
}

const saveUser = async () => {
  submitting.value = true
  modalError.value = ''
  try {
    if (isEditing.value) {
      await authStore.api.put(`/users/${selectedUser.value.id}`, form.value)
    } else {
      await authStore.api.post('/users', form.value)
    }
    closeModal()
    fetchUsers(pagination.value.current_page)
  } catch (error) {
    modalError.value = error.response?.data?.message || 'Erro ao salvar dados do usuário'
  } finally {
    submitting.value = false
  }
}

const toggleUserStatus = async (user) => {
  const action = user.is_active ? 'desativar' : 'ativar'
  if (!confirm(`Deseja realmente ${action} este usuário?`)) return
  try {
    await authStore.api.delete(`/users/${user.id}`)
    fetchUsers(pagination.value.current_page)
  } catch (error) {
    alert(error.response?.data?.message || 'Erro ao alterar status do usuário')
  }
}

const roleLabel = (role) => {
  const labels = {
    admin: 'Admin',
    aqv: 'Responsável',
    professor: 'Professor',
    porteiro: 'Porteiro',
  }
  return labels[role] || role
}

onMounted(() => {
  fetchUsers()
})
</script>
