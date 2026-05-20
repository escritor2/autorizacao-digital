<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-900/30 via-slate-950 to-slate-950">
    <div class="bg-slate-900/60 backdrop-blur-md border border-slate-800/80 rounded-2xl shadow-2xl p-8 w-full max-w-md space-y-6">
      <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/20">
          <span class="text-white text-2xl font-bold">S</span>
        </div>
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">SAFE</h1>
        <p class="text-slate-400 text-xs mt-2 uppercase tracking-widest font-semibold">Sistema de Autorização e Fluxo Escolar</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-5">
        <div>
          <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
            E-mail
          </label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            class="w-full px-4 py-3 bg-slate-950/50 border border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-600 text-sm transition-all"
            placeholder="seu@email.com"
            required
          />
        </div>

        <div>
          <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
            Senha
          </label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            class="w-full px-4 py-3 bg-slate-950/50 border border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-slate-200 placeholder-slate-600 text-sm transition-all"
            placeholder="••••••••"
            required
          />
        </div>

        <div v-if="error" class="bg-rose-500/10 border border-rose-500/20 text-rose-450 px-4 py-3 rounded-xl text-sm">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3 rounded-xl shadow-lg shadow-indigo-550/15 hover:shadow-indigo-550/25 transition-all active:scale-[0.98] disabled:opacity-50 text-sm"
        >
          {{ loading ? 'Entrando...' : 'Entrar no Sistema' }}
        </button>
      </form>

      <!-- Credentials tip box -->
      <div class="p-4 bg-slate-950/40 border border-slate-800/80 rounded-xl space-y-2">
        <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Acesso de Teste:</p>
        <ul class="text-[11px] text-slate-400 space-y-1 font-mono">
          <li><span class="text-slate-300">Admin:</span> admin@safe-sistema.local</li>
          <li><span class="text-slate-300">AQV:</span> maria@safe-sistema.local</li>
          <li><span class="text-slate-300">Prof:</span> carlos@safe-sistema.local</li>
          <li><span class="text-slate-300">Porteiro:</span> joao@safe-sistema.local</li>
          <li><span class="text-slate-300">Senha:</span> password</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: '',
})

const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    await authStore.login(form.value.email, form.value.password)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.message || 'Erro ao fazer login'
  } finally {
    loading.value = false
  }
}
</script>
