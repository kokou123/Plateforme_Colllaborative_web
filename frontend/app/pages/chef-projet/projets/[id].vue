<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'chef-projet', middleware: ['auth', 'role'], role: 'Chef de projet' })

const route = useRoute()
const router = useRouter()
const { apiFetch } = useApi()
const projetId = route.params.id as string

const projet = ref<any>(null)
const loading = ref(true)
const activeTab = ref<'apercu' | 'membres' | 'taches'>('apercu')

const loadError = ref('')

const fetchProjet = async () => {
  loading.value = true
  loadError.value = ''
  try {
    const res = await apiFetch<{ data: any }>(`/projets/${projetId}`)
    projet.value = res.data
  } catch (e: any) {
    loadError.value = e?.data?.message || `Erreur ${e?.status || ''} lors du chargement du projet.`
    console.error('ERREUR CHARGEMENT PROJET:', e)
  } finally {
    loading.value = false
  }
}
onMounted(fetchProjet)

const statutColor: Record<string, string> = {
  'À faire': 'bg-white/10 text-mist',
  'En cours': 'bg-brand/15 text-brand',
  'Suspendu': 'bg-danger/15 text-danger',
  'Terminé': 'bg-success/15 text-success',
}

/* --- Membres --- */
const allUsers = ref<any[]>([])
const selectedUserId = ref('')
const membresError = ref('')

const loadUsers = async () => {
  const res = await apiFetch<{ data: any[] }>('/employes')
  allUsers.value = res.data
}

const membresDisponibles = computed(() => {
  const membreIds = (projet.value?.membres || []).map((m: any) => m.id)
  return allUsers.value.filter(u => !membreIds.includes(u.id))
})

const addMembre = async () => {
  if (!selectedUserId.value) return
  membresError.value = ''
  try {
    await apiFetch(`/projets/${projetId}/membres`, { method: 'POST', body: { user_id: selectedUserId.value } })
    selectedUserId.value = ''
    await fetchProjet()
  } catch (e: any) {
    membresError.value = e?.data?.message || 'Erreur.'
  }
}

const removeMembre = async (userId: number) => {
  await apiFetch(`/projets/${projetId}/membres/${userId}`, { method: 'DELETE' })
  await fetchProjet()
}

/* --- Tâches --- */
const showTacheModal = ref(false)
const submitting = ref(false)
const submitError = ref('')
const colonnes = ['À faire', 'En cours', 'En révision', 'Terminée']
const priorites = ['Faible', 'Moyenne', 'Haute', 'Urgente']
const prioriteColor: Record<string, string> = {
  Faible: 'bg-white/10 text-mist', Moyenne: 'bg-brand/15 text-brand',
  Haute: 'bg-accent/15 text-accent', Urgente: 'bg-danger/15 text-danger',
}

const tachesParColonne = (colonne: string) => (projet.value?.taches || []).filter((t: any) => t.statut === colonne)

const schema = toTypedSchema(z.object({
  titre: z.string().min(2, 'Requis'),
  description: z.string().optional(),
  date_debut: z.string().min(1, 'Requis'),
  date_fin: z.string().optional(),
  priorite: z.enum(['Faible', 'Moyenne', 'Haute', 'Urgente']),
  projet_id: z.coerce.string().min(1, 'Requis'),
  assigned_to: z.coerce.string().optional(),
}))
const { handleSubmit, defineField, errors, resetForm } = useForm({
  validationSchema: schema,
  initialValues: { titre: '', description: '', date_debut: '', date_fin: '', priorite: 'Moyenne', projet_id: '', assigned_to: '' },
})
const [titre] = defineField('titre')
const [description] = defineField('description')
const [date_debut] = defineField('date_debut')
const [date_fin] = defineField('date_fin')
const [priorite] = defineField('priorite')
const [assigned_to] = defineField('assigned_to')

const openTacheModal = () => { submitError.value = ''; resetForm({ values: { priorite: 'Moyenne' } }); showTacheModal.value = true }

const onSubmitTache = handleSubmit(async (values) => {
  console.log('SUBMIT TACHE — valeurs:', values)
  submitting.value = true
  submitError.value = ''
  try {
    await apiFetch('/taches', { method: 'POST', body: { ...values, projet_id: projetId } })
    showTacheModal.value = false
    await fetchProjet()
  } catch (e: any) {
    console.error('ERREUR CREATION TACHE:', e)
    submitError.value = e?.data?.message || 'Erreur lors de la création.'
  } finally {
    submitting.value = false
  }
}, (errors) => {
  console.log('VALIDATION ECHOUEE:', errors)
})

const changerStatut = async (tache: any, nouveauStatut: string) => {
  await apiFetch(`/taches/${tache.id}/statut`, { method: 'PATCH', body: { statut: nouveauStatut } })
  await fetchProjet()
}

const deleteTache = async (tache: any) => {
  if (!confirm(`Supprimer "${tache.titre}" ?`)) return
  await apiFetch(`/taches/${tache.id}`, { method: 'DELETE' })
  await fetchProjet()
}

const nextStatut = (statut: string) => {
  const i = colonnes.indexOf(statut)
  return i < colonnes.length - 1 ? colonnes[i + 1] : null
}

watch(activeTab, (tab) => { if (tab === 'membres' && allUsers.value.length === 0) loadUsers() })
</script>

<template>
  <div v-if="loading" class="text-mist">Chargement...</div>


  <div v-if="loadError" class="text-danger bg-danger-light/10 border border-danger/20 rounded-lg px-4 py-3">
    {{ loadError }}
  </div>
  <div v-else-if="projet">
    <button @click="router.push('/chef-projet/projets')" class="flex items-center gap-1.5 text-sm text-mist hover:text-white transition-colors mb-4">
      <UIcon name="i-lucide-arrow-left" class="w-4 h-4" /> Retour aux projets
    </button>

    <div class="flex items-start justify-between mb-6">
      <div>
        <span :class="['px-2 py-1 rounded-md text-xs font-medium inline-block mb-2', statutColor[projet.statut]]">{{ projet.statut }}</span>
        <h1 class="font-display font-bold text-2xl">{{ projet.nom }}</h1>
        <p class="text-mist text-sm mt-1">{{ projet.description || 'Aucune description' }}</p>
      </div>
    </div>

    <!-- Onglets -->
    <div class="flex items-center gap-2 mb-6 border-b border-ink-700">
      <button
        v-for="tab in [{k:'apercu',l:'Aperçu'},{k:'membres',l:'Membres'},{k:'taches',l:'Tâches'}]" :key="tab.k"
        @click="activeTab = tab.k as any"
        :class="['px-4 py-2.5 text-sm font-medium border-b-2 transition-colors',
          activeTab === tab.k ? 'border-accent text-white' : 'border-transparent text-mist hover:text-white']"
      >
        {{ tab.l }}
      </button>
    </div>

    <!-- Aperçu -->
    <div v-if="activeTab === 'apercu'" class="grid md:grid-cols-3 gap-4">
      <div class="bg-ink-800 border border-ink-700 rounded-xl p-5">
        <p class="text-xs text-mist uppercase font-mono tracking-wide mb-2">Date de début</p>
        <p class="font-display font-semibold">{{ projet.date_debut }}</p>
      </div>
      <div class="bg-ink-800 border border-ink-700 rounded-xl p-5">
        <p class="text-xs text-mist uppercase font-mono tracking-wide mb-2">Date de fin</p>
        <p class="font-display font-semibold">{{ projet.date_fin || '—' }}</p>
      </div>
      <div class="bg-ink-800 border border-ink-700 rounded-xl p-5">
        <p class="text-xs text-mist uppercase font-mono tracking-wide mb-2">Membres</p>
        <p class="font-display font-semibold">{{ projet.nombre_membres }}</p>
      </div>
    </div>

    <!-- Membres -->
    <div v-else-if="activeTab === 'membres'" class="max-w-lg">
      <div class="space-y-2 mb-5">
        <div v-if="!projet.membres || projet.membres.length === 0" class="text-mist text-sm">Aucun membre pour l'instant.</div>
        <div v-for="m in projet.membres" :key="m.id" class="flex items-center justify-between bg-ink-800 border border-ink-700 rounded-lg px-4 py-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-accent/15 text-accent flex items-center justify-center text-xs font-medium font-display">
              {{ m.prenom?.[0] }}{{ m.nom?.[0] }}
            </div>
            <div>
              <p class="text-sm font-medium">{{ m.prenom }} {{ m.nom }}</p>
              <p class="text-xs text-mist">{{ m.roles?.[0] }}</p>
            </div>
          </div>
          <button @click="removeMembre(m.id)" class="text-danger text-xs hover:underline">Retirer</button>
        </div>
      </div>

      <div class="bg-ink-800 border border-ink-700 rounded-xl p-4 space-y-3">
        <select v-model="selectedUserId" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
          <option value="" disabled selected>Ajouter un membre</option>
          <option v-for="u in membresDisponibles" :key="u.id" :value="u.id">{{ u.prenom }} {{ u.nom }}</option>
        </select>
        <p v-if="membresError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ membresError }}</p>
        <button @click="addMembre" class="w-full bg-brand text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors">
          Ajouter
        </button>
      </div>
    </div>

    <!-- Tâches -->
    <div v-else-if="activeTab === 'taches'">
      <div class="flex justify-end mb-4">
        <button @click="openTacheModal" class="bg-accent text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
          <UIcon name="i-lucide-plus" class="w-4 h-4" /> Nouvelle tâche
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
              <p class="text-sm font-medium mb-3">{{ tache.titre }}</p>
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

      <!-- Modal création tâche -->
      <div v-if="showTacheModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showTacheModal = false">
        <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-auto">
          <h2 class="font-display font-semibold text-lg mb-5">Nouvelle tâche</h2>
          <form @submit="onSubmitTache" class="space-y-4">
            <AuthInput v-model="titre" label="Titre" icon="i-lucide-check-square" :error="errors.titre" />
            <div>
              <label class="text-xs font-medium text-mist mb-1.5 block">Description</label>
              <textarea v-model="description" rows="2" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand"></textarea>
            </div>
            <div>
              <label class="text-xs font-medium text-mist mb-1.5 block">Assigner à</label>
              <select v-model="assigned_to" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
                <option value="">Non assignée</option>
                <option v-for="m in projet.membres" :key="m.id" :value="m.id">{{ m.prenom }} {{ m.nom }}</option>
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
              <button type="button" @click="showTacheModal = false" class="flex-1 border border-ink-700 rounded-lg py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">Annuler</button>
              <button type="submit" :disabled="submitting" class="flex-1 bg-accent text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
                {{ submitting ? 'Création...' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>