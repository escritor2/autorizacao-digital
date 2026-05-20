<template>
  <div class="min-h-[70vh] flex items-center justify-center bg-slate-900">
    <div class="text-center space-y-4">
      <div class="inline-block animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
      <p class="text-slate-400 text-sm">Carregando painel personalizado...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  const role = authStore.user?.role
  if (role === 'admin') {
    router.replace('/admin/dashboard')
  } else if (role === 'aqv') {
    router.replace('/aqv/dashboard')
  } else if (role === 'professor') {
    router.replace('/professor/dashboard')
  } else if (role === 'porteiro') {
    router.replace('/porteiro/dashboard')
  } else {
    router.replace('/login')
  }
})
</script>
