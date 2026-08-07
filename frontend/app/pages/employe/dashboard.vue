<script setup lang="ts">
definePageMeta({ layout: 'employe', middleware: ['auth', 'role'], role: 'Employé' })

const { apiFetch } = useApi()
const data = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try { data.value = await apiFetch('/dashboard/employe') } finally { loading.value = false }
})

const cards = computed(() => !data.value ? [] : [
  { label: 'Mes tâches', value: data.value.mes_taches, icon: 'i-lucide-list-checks', color: 'brand' },
  { label: 'En attente', value: data.value.en_attente, icon: 'i-lucide-clock', color: 'accent' },
  { label: 'Terminées', value: data.value.terminees, icon: 'i-lucide-check-circle-2', color: 'success' },
  { label: 'Notifications', value: data.value.notifications, icon: 'i-lucide-bell', color: 'danger' },
])
const colorClasses: Record<string, string> = {
  brand: 'bg-brand/15 text-brand', accent: 'bg-accent/15 text-accent',
  success: 'bg-success/15 text-success', danger: 'bg-danger/15 text-danger',
}
</script>

<template>
  <div>
    <div v-if="loading" class="text-mist">Chargement...</div>
    <div v-else-if="data">
      <p class="text-xs font-semibold uppercase tracking-wide text-success mb-1">Vue d'ensemble</p>
      <h1 class="font-display font-bold text-2xl mb-8">Bonjour {{ data.utilisateur.prenom }}</h1>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div v-for="c in cards" :key="c.label" class="bg-ink-800 border border-ink-700 rounded-xl p-5">
          <div :class="['w-10 h-10 rounded-lg flex items-center justify-center mb-4', colorClasses[c.color]]">
            <UIcon :name="c.icon" class="w-5 h-5" />
          </div>
          <p class="font-display font-bold text-3xl">{{ c.value }}</p>
          <p class="text-xs text-mist mt-1">{{ c.label }}</p>
        </div>
      </div>
    </div>
  </div>
</template>