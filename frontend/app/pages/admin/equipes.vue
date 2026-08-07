<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const equipes = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const submitting = ref(false)
const submitError = ref('')

const fetchEquipes = async () => {
  loading.value = true
  try {
    const res = await apiFetch<{ data: any[] }>('/equipes')
    equipes.value = res.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchEquipes)

const schema = toTypedSchema(z.object({
  nom: z.string().min(2, 'Requis'),
  description: z.string().optional(),
}))
const { handleSubmit, defineField, errors, resetForm } = useForm({ validationSchema: schema })
const [nom] = defineField('nom')
const [description] = defineField('description')

const openModal = () => { submitError.value = ''; resetForm(); showModal.value = true }

const onSubmit = handleSubmit(async (values) => {
  submitting.value = true
  submitError.value = ''
  try {
    await apiFetch('/equipes', { method: 'POST', body: values })
    showModal.value = false
    await fetchEquipes()
  } catch (e: any) {
    submitError.value = e?.data?.message || 'Erreur lors de la création.'
  } finally {
    submitting.value = false
  }
})

const deleteEquipe = async (equipe: any) => {
  if (!confirm(`Supprimer l'équipe "${equipe.nom}" ?`)) return
  await apiFetch(`/equipes/${equipe.id}`, { method: 'DELETE' })
  await fetchEquipes()
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Organisation</p>
        <h1 class="font-display font-bold text-2xl">Équipes</h1>
      </div>
      <button @click="openModal" class="bg-brand text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
        <UIcon name="i-lucide-plus" class="w-4 h-4" />
        Nouvelle équipe
      </button>
    </div>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else-if="equipes.length === 0" class="bg-ink-800 border border-ink-700 rounded-xl p-10 text-center text-mist">
      Aucune équipe pour l'instant.
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="equipe in equipes" :key="equipe.id" class="bg-ink-800 border border-ink-700 rounded-xl p-5">
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 rounded-lg bg-brand/15 flex items-center justify-center">
            <UIcon name="i-lucide-users-round" class="w-5 h-5 text-brand" />
          </div>
          <button @click="deleteEquipe(equipe)" class="text-mist hover:text-danger transition-colors">
            <UIcon name="i-lucide-trash-2" class="w-4 h-4" />
          </button>
        </div>
        <h3 class="font-display font-semibold mb-1">{{ equipe.nom }}</h3>
        <p class="text-mist text-sm mb-3 line-clamp-2">{{ equipe.description || 'Aucune description' }}</p>
        <div class="flex items-center gap-1.5 text-xs text-mist">
          <UIcon name="i-lucide-user" class="w-3.5 h-3.5" />
          {{ equipe.utilisateurs?.length || 0 }} membre(s)
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md">
        <h2 class="font-display font-semibold text-lg mb-5">Nouvelle équipe</h2>
        <form @submit="onSubmit" class="space-y-4">
          <AuthInput v-model="nom" label="Nom de l'équipe" icon="i-lucide-users-round" placeholder="Ex: Équipe Design" :error="errors.nom" />
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Description</label>
            <textarea v-model="description" rows="3" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand"></textarea>
          </div>
          <p v-if="submitError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ submitError }}</p>
          <div class="flex gap-2 pt-2">
            <button type="button" @click="showModal = false" class="flex-1 border border-ink-700 rounded-lg py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">Annuler</button>
            <button type="submit" :disabled="submitting" class="flex-1 bg-brand text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
              {{ submitting ? 'Création...' : 'Créer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>