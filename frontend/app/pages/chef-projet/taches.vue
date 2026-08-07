<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'chef-projet', middleware: ['auth', 'role'], role: 'Chef de projet' })

const { apiFetch } = useApi()
const taches = ref<any[]>([])
const projets = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const submitting = ref(false)
const submitError = ref('')
const membresProjetSelectionne = ref<any[]>([])

const colonnes = ['À faire', 'En cours', 'En révision', 'Terminée']
const priorites = ['Faible', 'Moyenne', 'Haute', 'Urgente']
const prioriteColor: Record<string, string> = {
  Faible: 'bg-white/10 text-mist',
  Moyenne: 'bg-brand/15 text-brand',
  Haute: 'bg-accent/15 text-accent',
  Urgente: 'bg-danger/15 text-danger',
}

const fetchAll = async () => {
  loading.value = true
  try {
    const [tRes, pRes] = await Promise.all([
      apiFetch<{ data: any[] }>('/taches'),
      apiFetch<{ data: any[] }>('/projets'),
    ])
    taches.value = tRes.data
    projets.value = pRes.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchAll)

const tachesParColonne = (colonne: string) => taches.value.filter(t => t.statut === colonne)

const schema = toTypedSchema(z.object({
  titre: z.string().min(2, 'Requis'),
  description: z.string().optional(),
  date_debut: z.string().min(1, 'Requis'),
  date_fin: z.string().optional(),
  priorite: z.enum(['Faible', 'Moyenne', 'Haute', 'Urgente']),
  projet_id: z.string().min(1, 'Requis'),
  assigned_to: z.string().optional(),
}))
const { handleSubmit, defineField, errors, resetForm } = useForm({ validationSchema: schema })
const [titre] = defineField('titre')
const [description] = defineField('description')
const [date_debut] = defineField('date_debut')
const [date_fin] = defineField('date_fin')
const [priorite] = defineField('priorite')
const [projet_id] = defineField('projet_id')
const [assigned_to] = defineField('assigned_to')

const openModal = () => {
  submitError.value = ''
  resetForm({ values: { priorite: 'Moyenne' } })
  membresProjetSelectionne.value = []
  showModal.value = true
}

watch(projet_id, async (id) => {
  assigned_to.value = ''
  if (!id) { membresProjetSelectionne.value = []; return }
  const res = await apiFetch<{ data: any[] }>(`/projets/${id}/membres`)
  membresProjetSelectionne.value = res.data
})

const onSubmit = handleSubmit(async (values) => {
  submitting.value = true
  submitError.value = ''
  try {
    await apiFetch('/taches', { method: 'POST', body: values })
    showModal.value = false
    await fetchAll()
  } catch (e: any) {
    submitError.value = e?.data?.message || 'Erreur lors de la création.'
  } finally {
    submitting.value = false
  }
})

const changerStatut = async (tache: any, nouveauStatut: string) => {
  try {
    await apiFetch(`/taches/${tache.id}/statut`, { method: 'PATCH', body: { statut: nouveauStatut } })
    await fetchAll()
  } catch (e: any) {
    alert(e?.data?.message || 'Erreur.')
  }
}

const deleteTache = async (tache: any) => {
  if (!confirm(`Supprimer la tâche "${tache.titre}" ?`)) return
  await apiFetch(`/taches/${tache.id}`, { method: 'DELETE' })
  await fetchAll()
}

const nextStatut = (statut: string) => {
  const i = colonnes.indexOf(statut)
  return i < colonnes.length - 1 ? colonnes[i + 1] : null
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-accent mb-1">Gestion</p>
        <h1 class="font-display font-bold text-2xl">Tâches</h1>
      </div>
      <button @click="openModal" class="bg-accent text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
        <UIcon name="i-lucide-plus" class="w-4 h-4" />
        Nouvelle tâche
      </button>
    </div>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="colonne in colonnes" :key="colonne" class="bg-ink-800/50 border border-ink-700 rounded-xl p-3">
        <div class="flex items-center justify-between mb-3 px-1">
          <span class="text-sm font-medium text-mist">{{ colonne }}</span>
          <span class="text-xs bg-white/10 text-mist px-1.5 py-0.5 rounded-md font-mono">{{ tachesParColonne(colonne).length }}</span>
        </div>

        <div class="space-y-2.5">
          <div v-for="tache in tachesParColonne(colonne)" :key="tache.id" class="bg-ink-800 border border-ink-700 rounded-lg p-3.5">
            <div class="flex items-start justify-between mb-2">
              <span :class="['px-1.5 py-0.5 rounded text-[10px] font-medium', prioriteColor[tache.priorite]]">{{ tache.priorite }}</span>
              <button @click="deleteTache(tache)" class="text-mist hover:text-danger transition-colors">
                <UIcon name="i-lucide-x" class="w-3.5 h-3.5" />
              </button>
            </div>
            <p class="text-sm font-medium mb-1">{{ tache.titre }}</p>
            <p class="text-xs text-mist mb-3">{{ tache.projet?.nom }}</p>
            <div class="flex items-center justify-between">
              <div v-if="tache.assignee" class="w-6 h-6 rounded-full bg-brand/15 text-brand flex items-center justify-center text-[10px] font-medium font-display" :title="`${tache.assignee.prenom} ${tache.assignee.nom}`">
                {{ tache.assignee.prenom?.[0] }}{{ tache.assignee.nom?.[0] }}
              </div>
              <span v-else class="text-xs text-mist">Non assignée</span>

              <button v-if="nextStatut(tache.statut)" @click="changerStatut(tache, nextStatut(tache.statut)!)" class="text-brand text-xs font-medium hover:underline flex items-center gap-1">
                Avancer <UIcon name="i-lucide-arrow-right" class="w-3 h-3" />
              </button>
            </div>
          </div>
          <div v-if="tachesParColonne(colonne).length === 0" class="text-mist text-xs text-center py-4">Vide</div>
        </div>
      </div>
    </div>

    <!-- Modal création -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-auto">
        <h2 class="font-display font-semibold text-lg mb-5">Nouvelle tâche</h2>
        <form @submit="onSubmit" class="space-y-4">
          <AuthInput v-model="titre" label="Titre" icon="i-lucide-check-square" :error="errors.titre" />
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Description</label>
            <textarea v-model="description" rows="2" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand"></textarea>
          </div>

          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Projet</label>
            <select v-model="projet_id" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
              <option value="" disabled selected>Choisir un projet</option>
              <option v-for="p in projets" :key="p.id" :value="p.id">{{ p.nom }}</option>
            </select>
            <p v-if="errors.projet_id" class="text-danger text-xs mt-1">{{ errors.projet_id }}</p>
          </div>

          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Assigner à</label>
            <select v-model="assigned_to" :disabled="!projet_id" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand disabled:opacity-50">
              <option value="">Non assignée</option>
              <option v-for="m in membresProjetSelectionne" :key="m.id" :value="m.id">{{ m.prenom }} {{ m.nom }}</option>
            </select>
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
            <label class="text-xs font-medium text-mist mb-1.5 block">Priorité</label>
            <select v-model="priorite" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
              <option v-for="p in priorites" :key="p" :value="p">{{ p }}</option>
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