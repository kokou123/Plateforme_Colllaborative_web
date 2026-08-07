<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const users = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const submitError = ref('')
const submitting = ref(false)

const chefProjetExiste = computed(() => users.value.some(u => u.roles?.includes('Chef de projet')))

const fetchUsers = async () => {
  loading.value = true
  try {
    const res = await apiFetch<{ data: any[] }>('/users')
    users.value = res.data
  } finally {
    loading.value = false
  }
}
onMounted(fetchUsers)

const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'\-\/]+$/
const schema = toTypedSchema(z.object({
  prenom: z.string().min(2, 'Requis').regex(nameRegex, 'Lettres uniquement'),
  nom: z.string().min(2, 'Requis').regex(nameRegex, 'Lettres uniquement'),
  email: z.string().email('Email invalide'),
  role: z.enum(['Chef de projet', 'Employé'], { required_error: 'Choisissez un rôle' }),
}))

const { handleSubmit, defineField, errors, resetForm } = useForm({ validationSchema: schema })
const [prenom] = defineField('prenom')
const [nom] = defineField('nom')
const [email] = defineField('email')
const [role] = defineField('role')

const openModal = () => { submitError.value = ''; resetForm(); showModal.value = true }

const onSubmit = handleSubmit(async (values) => {
  submitting.value = true
  submitError.value = ''
  try {
    await apiFetch('/users', { method: 'POST', body: values })
    showModal.value = false
    await fetchUsers()
  } catch (e: any) {
    submitError.value = e?.data?.message || "Erreur lors de l'invitation"
  } finally {
    submitting.value = false
  }
})

const deleteError = ref('')

const deleteUser = async (user: any) => {
  deleteError.value = ''
  if (!confirm(`Supprimer ${user.prenom} ${user.nom} ?`)) return
  try {
    await apiFetch(`/users/${user.id}`, { method: 'DELETE' })
    await fetchUsers()
  } catch (e: any) {
    deleteError.value = e?.data?.message || 'Suppression impossible.'
  }
}

const roleBadge = (roles: string[]) => {
  if (roles?.includes('Admin')) return 'bg-brand/15 text-brand'
  if (roles?.includes('Chef de projet')) return 'bg-accent/15 text-accent'
  if (roles?.includes('Employé')) return 'bg-success/15 text-success'
  return 'bg-white/10 text-mist'
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Équipe</p>
        <h1 class="font-display font-bold text-2xl">Utilisateurs</h1>
      </div>
      <button @click="openModal" class="bg-brand text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center gap-2">
        <UIcon name="i-lucide-user-plus" class="w-4 h-4" />
        Inviter un utilisateur
      </button>
    </div>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else class="bg-ink-800 border border-ink-700 rounded-xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-ink-900/50 text-mist text-left">
          <tr>
            <th class="px-5 py-3 font-medium">Utilisateur</th>
            <th class="px-5 py-3 font-medium">Rôle</th>
            <th class="px-5 py-3 font-medium">Statut</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-t border-ink-700 hover:bg-ink-700/30 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand/15 text-brand flex items-center justify-center text-xs font-medium font-display flex-shrink-0">
                  {{ user.prenom?.[0] }}{{ user.nom?.[0] }}
                </div>
                <div>
                  <p class="font-medium text-white">{{ user.prenom }} {{ user.nom }}</p>
                  <p class="text-xs text-mist">{{ user.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-3">
              <span :class="['px-2 py-1 rounded-md text-xs font-medium', roleBadge(user.roles)]">
                {{ user.roles?.[0] || '—' }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span class="inline-flex items-center gap-1.5 text-xs font-medium" :class="user.email_verifie ? 'text-success' : 'text-accent'">
                <span :class="['w-1.5 h-1.5 rounded-full', user.email_verifie ? 'bg-success' : 'bg-accent']"></span>
                {{ user.email_verifie ? 'Actif' : 'Invitation en attente' }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <button v-if="!user.roles?.includes('Admin')" @click="deleteUser(user)" class="text-danger text-xs font-medium hover:underline">
                Supprimer
              </button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="4" class="px-5 py-10 text-center text-mist">Aucun utilisateur pour l'instant.</td>
          </tr>
        </tbody>
      </table>
      <p v-if="deleteError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2 mt-3">{{ deleteError }}</p>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="showModal = false">
      <div class="bg-ink-800 border border-ink-700 rounded-2xl p-6 w-full max-w-md">
        <h2 class="font-display font-semibold text-lg mb-5 text-white">Inviter un utilisateur</h2>

        <form @submit="onSubmit" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <AuthInput v-model="prenom" label="Prénom" icon="i-lucide-user" placeholder="Prénom" :error="errors.prenom" />
            <AuthInput v-model="nom" label="Nom" icon="i-lucide-user" placeholder="Nom" :error="errors.nom" />
          </div>
          <AuthInput v-model="email" label="Email" icon="i-lucide-mail" placeholder="email@entreprise.com" :error="errors.email" />

          <div>
            <label class="text-xs font-medium text-mist mb-1.5 block">Rôle</label>
            <select v-model="role" class="w-full bg-ink-900 border border-ink-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-brand">
              <option value="" disabled selected>Choisir un rôle</option>
              <option value="Chef de projet" :disabled="chefProjetExiste">
                Chef de projet {{ chefProjetExiste ? '(déjà assigné)' : '' }}
              </option>
              <option value="Employé">Employé</option>
            </select>
            <p v-if="errors.role" class="text-danger text-xs mt-1.5">{{ errors.role }}</p>
          </div>

          <p v-if="submitError" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ submitError }}</p>

          <div class="flex gap-2 pt-2">
            <button type="button" @click="showModal = false" class="flex-1 border border-ink-700 rounded-lg py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">
              Annuler
            </button>
            <button type="submit" :disabled="submitting" class="flex-1 bg-brand text-white rounded-lg py-2.5 text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
              {{ submitting ? 'Envoi...' : "Envoyer l'invitation" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>