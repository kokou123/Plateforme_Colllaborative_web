<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()
const userMenuOpen = ref(false)

const logout = async () => {
  const { apiFetch, token } = useApi()
  try { await apiFetch('/logout', { method: 'POST' }) } catch {}
  token.value = null
  authStore.user = null
  router.push('/login')
}

const links = [
  { label: 'Mes tâches', to: '/employe/dashboard', icon: 'i-lucide-check-square' },
  { label: 'Mes projets', to: '/employe/projets', icon: 'i-lucide-folder-kanban' },
  { label: 'Mes documents', to: '/employe/documents', icon: 'i-lucide-folder' },
  { label: 'Profil', to: '/employe/profil', icon: 'i-lucide-user' },
]
</script>

<template>
  <div class="min-h-screen flex bg-ink-900 font-body text-white">
    <aside class="w-64 bg-ink-800 border-r border-ink-700 flex flex-col">
      <div class="flex items-center gap-2 px-6 py-5 font-display font-bold border-b border-ink-700">
        <div class="w-7 h-7 bg-brand rounded-lg flex items-center justify-center text-sm">🌳</div>
        Baobab
      </div>
      <nav class="flex-1 px-3 py-4 space-y-0.5">
        <NuxtLink
          v-for="link in links" :key="link.to" :to="link.to"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-mist hover:bg-ink-700 hover:text-white transition-colors"
          active-class="!bg-success/15 !text-success"
        >
          <UIcon :name="link.icon" class="w-[18px] h-[18px] flex-shrink-0" />
          {{ link.label }}
        </NuxtLink>
      </nav>
      <div class="px-3 py-4 border-t border-ink-700">
        <button @click="logout" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-mist hover:bg-danger/10 hover:text-danger transition-colors">
          <UIcon name="i-lucide-log-out" class="w-[18px] h-[18px]" />
          Déconnexion
        </button>
      </div>
    </aside>

    <div class="flex-1 flex flex-col">
      <header class="h-16 bg-ink-800 border-b border-ink-700 flex items-center justify-between px-6 gap-4">
        <NuxtLink to="/" class="w-9 h-9 rounded-lg hover:bg-ink-700 flex items-center justify-center flex-shrink-0">
          <UIcon name="i-lucide-home" class="w-[18px] h-[18px] text-mist" />
        </NuxtLink>
        <div class="flex-1"></div>
        <div class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-full bg-success text-white flex items-center justify-center text-sm font-medium font-display">
              {{ authStore.user?.prenom?.[0] }}{{ authStore.user?.nom?.[0] }}
            </div>
          </button>
          <div v-if="userMenuOpen" class="absolute right-0 top-11 bg-ink-800 border border-ink-700 rounded-lg shadow-xl py-1.5 w-44 z-10">
            <div class="px-3 py-2 border-b border-ink-700">
              <p class="text-sm font-medium">{{ authStore.user?.prenom }} {{ authStore.user?.nom }}</p>
              <p class="text-xs text-mist">Employé</p>
            </div>
            <button @click="logout" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-danger hover:bg-danger/10">
              <UIcon name="i-lucide-log-out" class="w-4 h-4" /> Déconnexion
            </button>
          </div>
        </div>
      </header>
      <main class="flex-1 p-8 overflow-auto"><slot /></main>
    </div>
  </div>
</template>