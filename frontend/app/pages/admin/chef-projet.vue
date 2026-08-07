<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const users = ref<any[]>([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await apiFetch<{ data: any[] }>('/users')
    users.value = res.data
  } finally {
    loading.value = false
  }
})

const chefProjet = computed(() => users.value.find(u => u.roles?.includes('Chef de projet')))
</script>

<template>
  <div>
    <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Organisation</p>
    <h1 class="font-display font-bold text-2xl mb-6">Chef de projet</h1>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else-if="!chefProjet" class="bg-ink-800 border border-ink-700 rounded-xl p-10 text-center">
      <UIcon name="i-lucide-user-cog" class="w-10 h-10 text-mist mx-auto mb-3" />
      <p class="text-mist mb-4">Aucun chef de projet n'a encore été désigné.</p>
      <NuxtLink to="/admin/utilisateurs" class="text-brand text-sm font-medium">Inviter un chef de projet →</NuxtLink>
    </div>

    <div v-else class="bg-ink-800 border border-ink-700 rounded-xl p-6 max-w-lg">
      <div class="flex items-center gap-4 mb-5">
        <div class="w-14 h-14 rounded-full bg-accent/15 text-accent flex items-center justify-center text-lg font-display font-semibold">
          {{ chefProjet.prenom?.[0] }}{{ chefProjet.nom?.[0] }}
        </div>
        <div>
          <h3 class="font-display font-semibold text-lg">{{ chefProjet.prenom }} {{ chefProjet.nom }}</h3>
          <p class="text-mist text-sm">{{ chefProjet.email }}</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 text-xs font-medium" :class="chefProjet.email_verifie ? 'text-success' : 'text-accent'">
          <span :class="['w-1.5 h-1.5 rounded-full', chefProjet.email_verifie ? 'bg-success' : 'bg-accent']"></span>
          {{ chefProjet.email_verifie ? 'Actif' : 'Invitation en attente' }}
        </span>
      </div>
    </div>
  </div>
</template>