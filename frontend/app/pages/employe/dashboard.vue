<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Employé' })

const { apiFetch } = useApi()
const data = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  try { data.value = await apiFetch('/dashboard/employe') } finally { loading.value = false }
})
</script>

<template>
  <div>
    <div v-if="loading" class="text-slate">Chargement...</div>
    <div v-else-if="data">
      <p class="font-mono text-xs text-brand uppercase tracking-wider mb-1">Vue d'ensemble</p>
      <h1 class="font-display font-bold text-2xl mb-6">Bonjour {{ data.utilisateur.prenom }}</h1>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-border border-l-4 border-l-brand rounded-xl p-5">
          <p class="text-xs text-slate uppercase font-mono tracking-wide mb-2">Mes tâches</p>
          <p class="font-display font-bold text-3xl">{{ data.mes_taches }}</p>
        </div>
        <div class="bg-white border border-border border-l-4 border-l-accent rounded-xl p-5">
          <p class="text-xs text-slate uppercase font-mono tracking-wide mb-2">En attente</p>
          <p class="font-display font-bold text-3xl">{{ data.en_attente }}</p>
        </div>
        <div class="bg-white border border-border border-l-4 border-l-success rounded-xl p-5">
          <p class="text-xs text-slate uppercase font-mono tracking-wide mb-2">Terminées</p>
          <p class="font-display font-bold text-3xl">{{ data.terminees }}</p>
        </div>
        <div class="bg-white border border-border border-l-4 border-l-danger rounded-xl p-5">
          <p class="text-xs text-slate uppercase font-mono tracking-wide mb-2">Notifications</p>
          <p class="font-display font-bold text-3xl">{{ data.notifications }}</p>
        </div>
      </div>
    </div>
  </div>
</template>