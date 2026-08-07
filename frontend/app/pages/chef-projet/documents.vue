<script setup lang="ts">
definePageMeta({ layout: 'chef-projet', middleware: ['auth', 'role'], role: 'Chef de projet' })

const { apiFetch, token } = useApi()
const config = useRuntimeConfig()

const documents = ref<any[]>([])
const projets = ref<any[]>([])
const loading = ref(true)
const uploading = ref(false)
const uploadError = ref('')

const showUploadModal = ref(false)
const uploadNom = ref('')
const uploadProjetId = ref('')
const uploadFile = ref<File | null>(null)

const showPermModal = ref(false)
const activeDoc = ref<any>(null)
const membres = ref<any[]>([])
const permissions = ref<any[]>([])
const selectedUserId = ref('')
const permLecture = ref(true)
const permEcriture = ref(false)
const permError = ref('')

const fetchAll = async () => {
  loading.value = true
  try {
    const [docsRes, projetsRes] = await Promise.all([
      apiFetch<{ data: any[] }>('/documents'),
      apiFetch<{ data: any[] }>('/projets'),
    ])
    documents.value = docsRes.data
    projets.value = projetsRes.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchAll)

const formatSize = (bytes: number) => {
  if (!bytes) return '—'
  const mb = bytes / (1024 * 1024)
  return mb >= 1 ? `${mb.toFixed(1)} Mo` : `${(bytes / 1024).toFixed(0)} Ko`
}

const iconForType = (type: string) => {
  const t = (type || '').toLowerCase()
  if (t === 'pdf') return 'i-lucide-file-text'
  if (['png', 'jpg', 'jpeg', 'gif'].includes(t)) return 'i-lucide-image'
  if (['xls', 'xlsx', 'csv'].includes(t)) return 'i-lucide-file-spreadsheet'
  return 'i-lucide-file'
}

const onFileChange = (e: Event) => {
  uploadFile.value = (e.target as HTMLInputElement).files?.[0] || null
}

const submitUpload = async () => {
  if (!uploadFile.value || !uploadNom.value || !uploadProjetId.value) {
    uploadError.value = 'Tous les champs sont requis.'
    return
  }
  uploading.value = true
  uploadError.value = ''
  try {
    const formData = new FormData()
    formData.append('nom', uploadNom.value)
    formData.append('projet_id', uploadProjetId.value)
    formData.append('document', uploadFile.value)

    await $fetch(`${config.public.apiBase}/api/documents`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: formData,
    })

    showUploadModal.value = false
    uploadNom.value = ''
    uploadProjetId.value = ''
    uploadFile.value = null
    await fetchAll()
  } catch (e: any) {
    uploadError.value = e?.data?.message || "Erreur lors de l'envoi."
  } finally {
    uploading.value = false
  }
}

const deleteDocument = async (doc: any) => {
  if (!confirm(`Supprimer "${doc.nom}" ?`)) return
  try {
    await apiFetch(`/documents/${doc.id}`, { method: 'DELETE' })
    await fetchAll()
  } catch (e: any) {
    alert(e?.data?.message || 'Suppression impossible.')
  }
}

const downloadDocument = async (doc: any) => {
  try {
    const blob = await $fetch<Blob>(`${config.public.apiBase}/api/documents/${doc.id}/download`, {
      headers: { Authorization: `Bearer ${token.value}` },
      responseType: 'blob',
    })
    const url = URL.createObjectURL(blob as Blob)
    const a = window.document.createElement('a')
    a.href = url
    a.download = `${doc.nom}.${doc.type}`
    a.click()
    URL.revokeObjectURL(url)
  } catch {
    alert('Téléchargement impossible.')
  }
}

const openPermModal = async (doc: any) => {
  activeDoc.value = doc
  permError.value = ''
  selectedUserId.value = ''
  showPermModal.value = true
  const [membresRes, permsRes] = await Promise.all([
    apiFetch<{ data: any[] }>(`/projets/${doc.projet_id}/membres`),
    apiFetch<{ data: any[] }>('/document-permissions'),
  ])
  membres.value = membresRes.data.filter(m => m.id !== doc.projet?.chefProjet?.id)
  permissions.value = permsRes.data.filter((p: any) => p.document?.id === doc.id)
}

const grantPermission = async () => {
  if (!selectedUserId.value) return
  permError.value = ''
  try {
    await apiFetch('/document-permissions', {
      method: 'POST',
      body: { document_id: activeDoc.value.id, user_id: selectedUserId.value, lecture: permLecture.value, ecriture: permEcriture.value },
    })
    await openPermModal(activeDoc.value)
  } catch (e: any) {
    permError.value = e?.data?.message || 'Erreur.'
  }
}

const revokePermission = async (perm: any) => {
  await apiFetch(`/document-permissions/${perm.id}`, { method: 'DELETE' })
  await openPermModal(activeDoc.value)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-accent mb-1">Projets</p>
        <h1 class="font-display font-bold text-2xl">Documents</h1>
      </div>
      <button @click="showUploadModal = true" class="bg-accent text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
        <UIcon name="i-lucide-upload" class="w-4 h-4" />
        Déposer un document
      </button>
    </div>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else-if="documents.length === 0" class="bg-ink-800 border border-ink-700 rounded-xl p-10 text-center text-mist">
      Aucun document pour l'instant.
    </div>

    <div v-else class="bg-ink-800 border border-ink-700 rounded-xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-ink-900/50 text-mist text-left">
          <tr>
            <th class="px-5 py-3 font-medium">Document</th>
            <th class="px-5 py-3 font-medium">Projet</th>
            <th class="px-5 py-3 font-medium">Déposé par</th>
            <th class="px-5 py-3 font-medium">Taille</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documents" :key="doc.id" class="border-t border-ink-700 hover:bg-ink-700/30 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-accent/15 text-accent flex items-center justify-center flex-shrink-0">
                  <UIcon :name="iconForType(doc.type)" class="w-4 h-4" />
                </div>
                <span class="font-medium text-white">{{ doc.nom }}</span>
              </div>
            </td>
            <td class="px-5 py-3 text-mist">{{ doc.projet?.nom || '—' }}</td>
            <td class="px-5 py-3 text-mist">{{ doc.utilisateur?.prenom }} {{ doc.utilisateur?.nom }}</td>
            <td class="px-5 py-3 text-mist font-mono text-xs">{{ formatSize(doc.taille) }}</td>
            <td class="px-5 py-3 text-right space-x-3">
              <button @click="openPermModal(doc)" class="text-brand text-xs font-medium hover:underline">Permissions</button>
              <button @click="downloadDocument(doc)" class="text-mist text-xs font-medium hover:text-white">Télécharger</button>
              <button @click="deleteDocument(doc)" class="text-danger text-xs font-medium hover:underline">Supprimer</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal upload -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showUploadModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md">
        <h2 class="font-display font-semibold text-lg mb-5">Déposer un document</h2>
        <div class="space-y-4">
          <AuthInput v-model="uploadNom" label="Nom du document" icon="i-lucide-file" placeholder="Ex: Cahier des charges" />
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Projet</label>
            <select v-model="uploadProjetId" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
              <option value="" disabled selected>Choisir un projet</option>
              <option v-for="p in projets" :key="p.id" :value="p.id">{{ p.nom }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Fichier</label>
            <input type="file" @change="onFileChange" class="w-full text-sm text-mist file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-accent file:text-white file:text-xs file:font-medium" />
          </div>
          <p v-if="uploadError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ uploadError }}</p>
          <div class="flex gap-2 pt-2">
            <button @click="showUploadModal = false" class="flex-1 border border-ink-700 rounded-lg py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">Annuler</button>
            <button @click="submitUpload" :disabled="uploading" class="flex-1 bg-accent text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
              {{ uploading ? 'Envoi...' : 'Déposer' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal permissions -->
    <div v-if="showPermModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showPermModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md">
        <h2 class="font-display font-semibold text-lg mb-1">Permissions</h2>
        <p class="text-mist text-sm mb-5">{{ activeDoc?.nom }}</p>

        <div class="space-y-2 mb-5">
          <div v-if="permissions.length === 0" class="text-mist text-sm">Aucune permission accordée.</div>
          <div v-for="perm in permissions" :key="perm.id" class="flex items-center justify-between bg-ink-900 rounded-lg px-3 py-2.5">
            <div>
              <p class="text-sm font-medium">{{ perm.utilisateur?.prenom }} {{ perm.utilisateur?.nom }}</p>
              <p class="text-xs text-mist">
                {{ perm.lecture ? 'Lecture' : '' }}{{ perm.lecture && perm.ecriture ? ' + ' : '' }}{{ perm.ecriture ? 'Modification' : '' }}
              </p>
            </div>
            <button @click="revokePermission(perm)" class="text-danger text-xs hover:underline">Retirer</button>
          </div>
        </div>

        <div class="border-t border-ink-700 pt-4 space-y-3">
          <select v-model="selectedUserId" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
            <option value="" disabled selected>Choisir un membre</option>
            <option v-for="m in membres" :key="m.id" :value="m.id">{{ m.prenom }} {{ m.nom }}</option>
          </select>
          <div class="flex items-center gap-5">
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" v-model="permLecture" class="accent-brand" /> Lecture
            </label>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" v-model="permEcriture" class="accent-brand" /> Modification
            </label>
          </div>
          <p v-if="permError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ permError }}</p>
          <button @click="grantPermission" class="w-full bg-brand text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors">
            Accorder
          </button>
        </div>
      </div>
    </div>
  </div>
</template>