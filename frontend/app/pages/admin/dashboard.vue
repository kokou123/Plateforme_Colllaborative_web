<!-- frontend/app/pages/admin/dashboard.vue -->
<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const data = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try { data.value = await apiFetch('/dashboard/admin') } finally { loading.value = false }
})

const statCards = computed(() => {
  if (!data.value) return []
  const s = data.value.statistiques
  return [
    { label: 'Utilisateurs', value: s.utilisateurs, icon: 'i-lucide-users', color: 'brand' },
    { label: 'Projets', value: s.projets, icon: 'i-lucide-folder-kanban', color: 'accent' },
    { label: 'Tâches', value: s.taches, icon: 'i-lucide-check-square', color: 'success' },
    { label: 'Documents', value: s.documents, icon: 'i-lucide-file', color: 'danger' },
  ]
})

const colorClasses: Record<string, string> = {
  brand: 'bg-brand/15 text-brand',
  accent: 'bg-accent/15 text-accent',
  success: 'bg-success/15 text-success',
  danger: 'bg-danger/15 text-danger',
}

const projetStatuts = computed(() => data.value?.projets ?? {})
const maxProjet = computed(() => Math.max(1, ...Object.values(projetStatuts.value) as number[]))
const statutLabel: Record<string, string> = { en_attente: 'En attente', en_cours: 'En cours', termine: 'Terminés', termines: 'Terminés' }
const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
</script>

<template>
  <div>
    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else-if="data">
      <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Vue d'ensemble</p>
        <h1 class="font-display font-bold text-2xl">Bonjour {{ data.utilisateur.prenom }}</h1>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div v-for="card in statCards" :key="card.label" class="bg-ink-800 border border-ink-700 rounded-xl p-5">
          <div :class="['w-10 h-10 rounded-lg flex items-center justify-center mb-4', colorClasses[card.color]]">
            <UIcon :name="card.icon" class="w-5 h-5" />
          </div>
          <p class="font-display font-bold text-3xl">{{ card.value }}</p>
          <p class="text-xs text-mist mt-1">{{ card.label }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-ink-800 border border-ink-700 rounded-xl p-6">
          <h2 class="font-display font-semibold mb-6">Répartition des projets</h2>
          <div class="flex items-end gap-6 h-40">
            <div v-for="(count, key) in projetStatuts" :key="key" class="flex-1 flex flex-col items-center justify-end h-full">
              <span class="font-mono text-sm font-semibold mb-2">{{ count }}</span>
              <div class="w-full bg-brand rounded-t-md transition-all" :style="{ height: `${Math.max(6, (count / maxProjet) * 100)}%` }"></div>
              <span class="text-xs text-mist mt-2">{{ statutLabel[key] || key }}</span>
            </div>
          </div>
        </div>

        <div class="bg-ink-800 border border-ink-700 rounded-xl p-6">
          <h2 class="font-display font-semibold mb-4">Activité récente</h2>
          <div v-if="data.activites.length === 0" class="text-sm text-mist">Aucune activité récente.</div>
          <div v-else class="space-y-4">
            <div v-for="log in data.activites" :key="log.id" class="flex gap-3">
              <div class="w-7 h-7 rounded-full bg-brand/15 text-brand flex items-center justify-center text-xs font-medium flex-shrink-0 font-display">
                {{ log.utilisateur?.prenom?.[0] }}{{ log.utilisateur?.nom?.[0] }}
              </div>
              <div>
                <p class="text-sm"><span class="font-medium">{{ log.utilisateur?.prenom }}</span> <span class="text-mist">{{ log.description }}</span></p>
                <p class="font-mono text-xs text-mist mt-0.5">{{ formatDate(log.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>