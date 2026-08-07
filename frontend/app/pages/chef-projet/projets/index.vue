<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'chef-projet', middleware: ['auth', 'role'], role: 'Chef de projet' })

const { apiFetch } = useApi()
const projets = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const submitting = ref(false)
const submitError = ref('')

const statuts = ['À faire', 'En cours', 'Suspendu', 'Terminé']
const statutColor: Record<string, string> = {
  'À faire': 'bg-white/10 text-mist',
  'En cours': 'bg-brand/15 text-brand',
  'Suspendu': 'bg-danger/15 text-danger',
  'Terminé': 'bg-success/15 text-success',
}

const fetchProjets = async () => {
  loading.value = true
  try {
    const res = await apiFetch<{ data: any[] }>('/projets')
    projets.value = res.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchProjets)

const schema = toTypedSchema(z.object({
  nom: z.string().min(2, 'Requis'),
  description: z.string().optional(),
  date_debut: z.string().min(1, 'Requis'),
  date_fin: z.string().optional(),
  statut: z.string().optional(),
}))
const { handleSubmit, defineField, errors, resetForm } = useForm({ validationSchema: schema })
const [nom] = defineField('nom')
const [description] = defineField('description')
const [date_debut] = defineField('date_debut')
const [date_fin] = defineField('date_fin')
const [statut] = defineField('statut')

const openModal = () => { submitError.value = ''; resetForm({ values: { statut: 'À faire' } }); showModal.value = true }

const onSubmit = handleSubmit(async (values) => {
  submitting.value = true
  submitError.value = ''
  try {
    await apiFetch('/projets', { method: 'POST', body: values })
    showModal.value = false
    await fetchProjets()
  } catch (e: any) {
    submitError.value = e?.data?.message || 'Erreur lors de la création.'
  } finally {
    submitting.value = false
  }
})

const deleteProjet = async (projet: any) => {
  if (!confirm(`Supprimer le projet "${projet.nom}" ?`)) return
  try {
    await apiFetch(`/projets/${projet.id}`, { method: 'DELETE' })
    await fetchProjets()
  } catch (e: any) {
    alert(e?.data?.message || 'Suppression impossible.')
  }
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-accent mb-1">Gestion</p>
        <h1 class="font-display font-bold text-2xl">Projets</h1>
      </div>
      <button @click="openModal" class="bg-accent text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
        <UIcon name="i-lucide-plus" class="w-4 h-4" />
        Nouveau projet
      </button>
    </div>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else-if="projets.length === 0" class="bg-ink-800 border border-ink-700 rounded-xl p-10 text-center text-mist">
      Aucun projet pour l'instant.
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="projet in projets" :key="projet.id" class="bg-ink-800 border border-ink-700 rounded-xl p-5">
        <div class="flex items-start justify-between mb-3">
          <span :class="['px-2 py-1 rounded-md text-xs font-medium', statutColor[projet.statut] || 'bg-white/10 text-mist']">
            {{ projet.statut }}
          </span>
          <button @click="deleteProjet(projet)" class="text-mist hover:text-danger transition-colors">
            <UIcon name="i-lucide-trash-2" class="w-4 h-4" />
          </button>
        </div>
        <h3 class="font-display font-semibold mb-1.5">{{ projet.nom }}</h3>
        <p class="text-mist text-sm mb-4 line-clamp-2">{{ projet.description || 'Aucune description' }}</p>
        <div class="flex items-center justify-between text-xs text-mist">
          <span class="flex items-center gap-1.5">
            <UIcon name="i-lucide-users" class="w-3.5 h-3.5" />
            {{ projet.nombre_membres || 0 }} membre(s)
          </span>
          <NuxtLink :to="`/chef-projet/projets/${projet.id}`" class="text-brand font-medium hover:underline">
            Ouvrir →
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Modal création -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md">
        <h2 class="font-display font-semibold text-lg mb-5">Nouveau projet</h2>
        <form @submit="onSubmit" class="space-y-4">
          <AuthInput v-model="nom" label="Nom du projet" icon="i-lucide-folder-kanban" :error="errors.nom" />
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Description</label>
            <textarea v-model="description" rows="3" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-medium text-mist mb-1.5 block">Date de début</label>
              <input v-model="date_debut" type="date" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand" />
              <p v-if="errors.date_debut" class="text-danger text-xs mt-1">{{ errors.date_debut }}</p>
            </div>
            <div>
              <label class="text-xs font-medium text-mist mb-1.5 block">Date de fin</label>
              <input v-model="date_fin" type="date" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand" />
            </div>
          </div>
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Statut</label>
            <select v-model="statut" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
              <option v-for="s in statuts" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <p v-if="submitError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ submitError }}</p>
          <div class="flex gap-2 pt-2">
            <button type="button" @click="showModal = false" class="flex-1 border border-ink-700 rounded-lg py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">Annuler</button>
            <button type="submit" :disabled="submitting" class="flex-1 bg-accent text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
              {{ submitting ? 'Création...' : 'Créer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>