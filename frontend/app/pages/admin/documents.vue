<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch, token } = useApi()
const config = useRuntimeConfig()
const documents = ref<any[]>([])
const loading = ref(true)
const downloadingId = ref<number | null>(null)

const fetchDocuments = async () => {
  loading.value = true
  try {
    const res = await apiFetch<{ data: any[] }>('/documents')
    documents.value = res.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchDocuments)

const formatSize = (bytes: number) => {
  if (!bytes) return '—'
  const mb = bytes / (1024 * 1024)
  return mb >= 1 ? `${mb.toFixed(1)} Mo` : `${(bytes / 1024).toFixed(0)} Ko`
}

const iconForType = (type: string) => {
  const t = (type || '').toLowerCase()
  if (['pdf'].includes(t)) return 'i-lucide-file-text'
  if (['png', 'jpg', 'jpeg', 'gif'].includes(t)) return 'i-lucide-image'
  if (['xls', 'xlsx', 'csv'].includes(t)) return 'i-lucide-file-spreadsheet'
  if (['doc', 'docx'].includes(t)) return 'i-lucide-file-text'
  return 'i-lucide-file'
}

const downloadDocument = async (doc: any) => {
  downloadingId.value = doc.id
  try {
    const blob = await $fetch<Blob>(`${config.public.apiBase}/api/documents/${doc.id}/download`, {
      headers: { Authorization: `Bearer ${token.value}` },
      responseType: 'blob',
    })
    const url = URL.createObjectURL(blob as Blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${doc.nom}.${doc.type}`
    a.click()
    URL.revokeObjectURL(url)
  } catch {
    alert('Téléchargement impossible.')
  } finally {
    downloadingId.value = null
  }
}
</script>

<template>
  <div>
    <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Organisation</p>
    <h1 class="font-display font-bold text-2xl mb-6">Documents</h1>

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
            <th class="px-5 py-3 font-medium">Versions</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documents" :key="doc.id" class="border-t border-ink-700 hover:bg-ink-700/30 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-brand/15 text-brand flex items-center justify-center flex-shrink-0">
                  <UIcon :name="iconForType(doc.type)" class="w-4 h-4" />
                </div>
                <span class="font-medium text-white">{{ doc.nom }}</span>
              </div>
            </td>
            <td class="px-5 py-3 text-mist">{{ doc.projet?.nom || '—' }}</td>
            <td class="px-5 py-3 text-mist">{{ doc.utilisateur?.prenom }} {{ doc.utilisateur?.nom }}</td>
            <td class="px-5 py-3 text-mist font-mono text-xs">{{ formatSize(doc.taille) }}</td>
            <td class="px-5 py-3 text-mist">{{ doc.versions?.length || 1 }}</td>
            <td class="px-5 py-3 text-right">
              <button @click="downloadDocument(doc)" :disabled="downloadingId === doc.id" class="text-brand text-xs font-medium hover:underline disabled:opacity-50">
                {{ downloadingId === doc.id ? 'Téléchargement...' : 'Télécharger' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>