<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const data = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    data.value = await apiFetch('/dashboard/admin')
  } finally {
    loading.value = false
  }
})

const statCards = computed(() => {
  if (!data.value) return []
  const s = data.value.statistiques
  return [
    { label: 'Utilisateurs', value: s.utilisateurs, border: 'border-l-brand' },
    { label: 'Projets', value: s.projets, border: 'border-l-accent' },
    { label: 'Tâches', value: s.taches, border: 'border-l-success' },
    { label: 'Documents', value: s.documents, border: 'border-l-danger' },
  ]
})

const projetStatuts = computed(() => data.value?.projets ?? {})
const maxProjet = computed(() => Math.max(1, ...Object.values(projetStatuts.value) as number[]))

const statutLabel: Record<string, string> = {
  en_attente: 'En attente', en_cours: 'En cours', termine: 'Terminés', termines: 'Terminés',
}

const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
</script>

<template>
  <div>
    <div v-if="loading" class="text-slate">Chargement...</div>

    <div v-else-if="data">
      <div class="mb-8">
        <p class="font-mono text-xs text-brand uppercase tracking-wider mb-1">Vue d'ensemble</p>
        <h1 class="font-display font-bold text-2xl">Bonjour {{ data.utilisateur.prenom }}</h1>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div v-for="card in statCards" :key="card.label"
          :class="['bg-white border border-border border-l-4 rounded-xl p-5', card.border]">
          <p class="text-xs text-slate uppercase font-mono tracking-wide mb-2">{{ card.label }}</p>
          <p class="font-display font-bold text-3xl">{{ card.value }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Répartition projets en barres -->
        <div class="lg:col-span-2 bg-white border border-border rounded-xl p-6">
          <h2 class="font-display font-semibold mb-6">Répartition des projets</h2>
          <div class="flex items-end gap-6 h-40">
            <div v-for="(count, key) in projetStatuts" :key="key" class="flex-1 flex flex-col items-center justify-end h-full">
              <span class="font-mono text-sm font-semibold mb-2">{{ count }}</span>
              <div
                class="w-full bg-brand rounded-t-md"
                :style="{ height: `${Math.max(6, (count / maxProjet) * 100)}%` }"
              ></div>
              <span class="text-xs text-slate mt-2">{{ statutLabel[key] || key }}</span>
            </div>
          </div>
        </div>

        <!-- Activités -->
        <div class="bg-white border border-border rounded-xl p-6">
          <h2 class="font-display font-semibold mb-4">Activité récente</h2>
          <div v-if="data.activites.length === 0" class="text-sm text-slate">Aucune activité récente.</div>
          <div v-else class="space-y-4">
            <div v-for="log in data.activites" :key="log.id" class="flex gap-3">
              <div class="w-1.5 h-1.5 rounded-full bg-brand mt-1.5 flex-shrink-0"></div>
              <div>
                <p class="text-sm"><span class="font-medium">{{ log.utilisateur?.prenom }}</span> {{ log.description }}</p>
                <p class="font-mono text-xs text-slate mt-0.5">{{ formatDate(log.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>