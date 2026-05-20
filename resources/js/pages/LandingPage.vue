<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col selection:bg-indigo-500 selection:text-white overflow-hidden relative">
    
    <!-- Background glowing ambient lights -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10 relative">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/20">
          S
        </div>
        <div>
          <h1 class="text-xl font-black bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent tracking-wider">SAFE</h1>
          <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Segurança & Fluxo Escolar</p>
        </div>
      </div>

      <div>
        <router-link
          :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2"
        >
          {{ authStore.isAuthenticated ? 'Entrar no Painel' : 'Acessar o Sistema' }}
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </router-link>
      </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow flex flex-col justify-center items-center px-6 py-16 md:py-24 max-w-5xl mx-auto text-center z-10 relative">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/25 text-indigo-350 text-xs font-semibold uppercase tracking-wider mb-6 animate-fade-in">
        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
        Plataforma Escolar Inteligente
      </div>

      <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white leading-tight md:leading-none max-w-4xl">
        Controle de Fluxo e <br class="hidden md:inline">
        <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
          Segurança Escolar Integrada
        </span>
      </h1>

      <p class="mt-6 text-base md:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
        O SAFE conecta responsáveis, professores e a portaria escolar em tempo real, garantindo máxima rastreabilidade, autorizações rápidas e total segurança nas saídas e entradas de alunos.
      </p>

      <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center w-full max-w-md mx-auto">
        <router-link
          :to="authStore.isAuthenticated ? '/dashboard' : '/login'"
          class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-bold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-xl shadow-indigo-650/20 transition-all hover:scale-[1.03] active:scale-[0.98] flex items-center justify-center gap-2"
        >
          Começar Agora
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </router-link>
        
        <a
          href="#funcionalidades"
          class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-semibold bg-slate-900/60 backdrop-blur-md border border-slate-800 hover:bg-slate-800/80 text-slate-300 transition-all hover:scale-[1.02] flex items-center justify-center gap-2"
        >
          Conhecer Recursos
        </a>
      </div>

      <!-- Features Pillars Grid -->
      <section id="funcionalidades" class="mt-24 md:mt-32 w-full text-left scroll-mt-24">
        <div class="text-center mb-12">
          <h2 class="text-2xl md:text-3xl font-bold text-white">Fluxos Inteligentes de Segurança</h2>
          <p class="text-slate-500 text-sm mt-2">Uma resposta sob medida para cada ator do ambiente escolar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          
          <!-- AQV (Responsável) -->
          <div class="group bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 transition-all hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-950/20 hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/25 flex items-center justify-center text-indigo-400 mb-4 group-hover:bg-indigo-650 group-hover:text-white transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Responsáveis</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              Solicite e aprove autorizações de saída para seus filhos diretamente pelo celular, com notificação em tempo real.
            </p>
          </div>

          <!-- Professor -->
          <div class="group bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 transition-all hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-950/20 hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/25 flex items-center justify-center text-purple-400 mb-4 group-hover:bg-purple-650 group-hover:text-white transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Professores</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              Monitore a liberação dos alunos da sua sala de aula em tempo real. Saiba quem está autorizado a sair instantaneamente.
            </p>
          </div>

          <!-- Porteiro -->
          <div class="group bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 transition-all hover:border-pink-500/50 hover:shadow-lg hover:shadow-pink-950/20 hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-pink-500/10 border border-pink-500/25 flex items-center justify-center text-pink-400 mb-4 group-hover:bg-pink-650 group-hover:text-white transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Portaria</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              Consulte autorizações ativas na hora da saída e registre o fluxo de entrada e saída com poucos cliques, reduzindo filas.
            </p>
          </div>

          <!-- Administrador -->
          <div class="group bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 transition-all hover:border-emerald-500/50 hover:shadow-lg hover:shadow-emerald-950/20 hover:-translate-y-1">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400 mb-4 group-hover:bg-emerald-650 group-hover:text-white transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Administração</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              Gerencie usuários, turmas, alunos e visualize logs detalhados do sistema com relatórios completos de conformidade.
            </p>
          </div>

        </div>
      </section>

      <!-- Active Test Credentials section (Beautiful & Elegant) -->
      <section class="mt-20 w-full max-w-3xl bg-slate-900/30 border border-slate-800/80 rounded-3xl p-8 backdrop-blur-md relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
        
        <h3 class="text-xl font-bold text-white flex items-center justify-center gap-2 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
          Perfis de Demonstração
        </h3>
        
        <p class="text-slate-400 text-sm mb-6 max-w-xl mx-auto">
          O sistema já vem pré-configurado com contas de teste para que você possa explorar a plataforma a partir de diferentes pontos de vista.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
          
          <!-- Admin / AQV -->
          <div class="p-4 bg-slate-950/60 border border-slate-800/50 rounded-xl flex flex-col justify-between">
            <div>
              <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">Administração & Família</p>
              <div class="space-y-1.5 font-mono text-xs text-slate-300">
                <p><span class="text-slate-500">Admin:</span> admin@safe-sistema.local</p>
                <p><span class="text-slate-500">Responsável:</span> maria@safe-sistema.local</p>
              </div>
            </div>
          </div>

          <!-- Professor / Porteiro -->
          <div class="p-4 bg-slate-950/60 border border-slate-800/50 rounded-xl flex flex-col justify-between">
            <div>
              <p class="text-xs font-bold text-purple-400 uppercase tracking-widest mb-2">Pedagógico & Segurança</p>
              <div class="space-y-1.5 font-mono text-xs text-slate-300">
                <p><span class="text-slate-500">Professor:</span> carlos@safe-sistema.local</p>
                <p><span class="text-slate-500">Porteiro:</span> joao@safe-sistema.local</p>
              </div>
            </div>
          </div>

        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-4 items-center justify-between border-t border-slate-800/60 pt-6">
          <span class="text-[11px] text-slate-500 font-mono">Senha de acesso para todos os perfis: <span class="text-indigo-450 font-bold">password</span></span>
          <router-link to="/login" class="text-sm font-bold text-indigo-400 hover:text-indigo-350 transition-all flex items-center gap-1.5 hover:translate-x-0.5">
            Ir para página de login
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </router-link>
        </div>
      </section>
    </main>

  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
