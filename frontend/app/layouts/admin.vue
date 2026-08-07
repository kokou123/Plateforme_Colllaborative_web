<!-- frontend/app/layouts/admin.vue -->
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
  { label: 'Tableau de bord', to: '/admin/dashboard', icon: 'i-lucide-layout-dashboard' },
  { label: 'Entreprise', to: '/admin/entreprise', icon: 'i-lucide-building-2' },
  { label: 'Utilisateurs', to: '/admin/utilisateurs', icon: 'i-lucide-users' },
  { label: 'Équipes', to: '/admin/equipes', icon: 'i-lucide-users-round' },
  { label: 'Chef de projet', to: '/admin/chef-projet', icon: 'i-lucide-user-cog' },
  { label: 'Documents', to: '/admin/documents', icon: 'i-lucide-folder' },
  { label: 'Processus', to: '/admin/processus', icon: 'i-lucide-workflow' },
  { label: 'Paramètres', to: '/admin/parametres', icon: 'i-lucide-settings' },
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
          active-class="!bg-brand/15 !text-brand"
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
        <NuxtLink to="/" class="w-9 h-9 rounded-lg hover:bg-ink-700 flex items-center justify-center flex-shrink-0" title="Retour à l'accueil">
          <UIcon name="i-lucide-home" class="w-[18px] h-[18px] text-mist" />
        </NuxtLink>

        <div class="relative flex-1 max-w-md">
          <UIcon name="i-lucide-search" class="w-4 h-4 text-mist absolute left-3 top-1/2 -translate-y-1/2" />
          <input type="text" placeholder="Rechercher..." class="w-full bg-ink-900 border border-ink-700 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder:text-mist/50 focus:outline-none focus:border-brand transition-colors" />
        </div>

        <div class="flex items-center gap-4">
          <button class="relative w-9 h-9 rounded-lg hover:bg-ink-700 flex items-center justify-center">
            <UIcon name="i-lucide-bell" class="w-[18px] h-[18px] text-mist" />
            <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-danger rounded-full"></span>
          </button>

          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2">
              <div class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center text-sm font-medium font-display">
                {{ authStore.user?.prenom?.[0] }}{{ authStore.user?.nom?.[0] }}
              </div>
            </button>
            <div v-if="userMenuOpen" class="absolute right-0 top-11 bg-ink-800 border border-ink-700 rounded-lg shadow-xl py-1.5 w-44 z-10">
              <div class="px-3 py-2 border-b border-ink-700">
                <p class="text-sm font-medium text-white">{{ authStore.user?.prenom }} {{ authStore.user?.nom }}</p>
                <p class="text-xs text-mist">Administrateur</p>
              </div>
              <button @click="logout" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-danger hover:bg-danger/10">
                <UIcon name="i-lucide-log-out" class="w-4 h-4" />
                Déconnexion
              </button>
            </div>
          </div>
        </div>
      </header>

      <main class="flex-1 p-8 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>