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

const chefProjetExiste = computed(() =>
  users.value.some(u => u.roles?.includes('Chef de projet'))
)

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

const schema = toTypedSchema(z.object({
  prenom: z.string().min(2, 'Requis'),
  nom: z.string().min(2, 'Requis'),
  email: z.string().email('Email invalide'),
  role: z.enum(['Chef de projet', 'Employé'], { required_error: 'Choisissez un rôle' }),
}))

const { handleSubmit, defineField, errors, resetForm } = useForm({ validationSchema: schema })
const [prenom] = defineField('prenom')
const [nom] = defineField('nom')
const [email] = defineField('email')
const [role] = defineField('role')

const openModal = () => {
  submitError.value = ''
  resetForm()
  showModal.value = true
}

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

const deleteUser = async (user: any) => {
  if (!confirm(`Supprimer ${user.prenom} ${user.nom} ?`)) return
  await apiFetch(`/users/${user.id}`, { method: 'DELETE' })
  await fetchUsers()
}

const roleBadgeClass = (roles: string[]) => {
  if (roles?.includes('Chef de projet')) return 'bg-purple-50 text-purple-600'
  if (roles?.includes('Employé')) return 'bg-emerald-50 text-emerald-600'
  return 'bg-gray-100 text-gray-600'
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold">Utilisateurs</h1>
        <p class="text-gray-500 text-sm mt-1">Invitez et gérez les membres de votre entreprise.</p>
      </div>
      <button @click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
        + Inviter un utilisateur
      </button>
    </div>

    <div v-if="loading" class="text-gray-400">Chargement...</div>

    <div v-else class="bg-white border rounded-xl overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-left">
          <tr>
            <th class="px-5 py-3 font-medium">Nom</th>
            <th class="px-5 py-3 font-medium">Email</th>
            <th class="px-5 py-3 font-medium">Rôle</th>
            <th class="px-5 py-3 font-medium">Statut</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-t">
            <td class="px-5 py-3">{{ user.prenom }} {{ user.nom }}</td>
            <td class="px-5 py-3 text-gray-500">{{ user.email }}</td>
            <td class="px-5 py-3">
              <span :class="['px-2 py-1 rounded-md text-xs font-medium', roleBadgeClass(user.roles)]">
                {{ user.roles?.[0] || '—' }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span :class="user.email_verifie ? 'text-emerald-600' : 'text-amber-600'" class="text-xs font-medium">
                {{ user.email_verifie ? 'Actif' : 'Invitation en attente' }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <button @click="deleteUser(user)" class="text-red-500 text-xs hover:underline">Supprimer</button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="5" class="px-5 py-8 text-center text-gray-400">Aucun utilisateur pour l'instant.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal d'invitation -->
    <div v-if="showModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50" @click.self="showModal = false">
      <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Inviter un utilisateur</h2>

        <form @submit="onSubmit" class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <input v-model="prenom" placeholder="Prénom" class="w-full border rounded-lg px-3 py-2" />
            <input v-model="nom" placeholder="Nom" class="w-full border rounded-lg px-3 py-2" />
          </div>
          <p v-if="errors.prenom || errors.nom" class="text-red-600 text-xs">{{ errors.prenom || errors.nom }}</p>

          <input v-model="email" placeholder="Email" class="w-full border rounded-lg px-3 py-2" />
          <p v-if="errors.email" class="text-red-600 text-xs">{{ errors.email }}</p>

          <select v-model="role" class="w-full border rounded-lg px-3 py-2 text-gray-700">
            <option value="" disabled selected>Choisir un rôle</option>
            <option value="Chef de projet" :disabled="chefProjetExiste">
              Chef de projet {{ chefProjetExiste ? '(déjà assigné)' : '' }}
            </option>
            <option value="Employé">Employé</option>
          </select>
          <p v-if="errors.role" class="text-red-600 text-xs">{{ errors.role }}</p>

          <p v-if="submitError" class="text-red-600 text-sm">{{ submitError }}</p>

          <div class="flex gap-2 pt-2">
            <button type="button" @click="showModal = false" class="flex-1 border rounded-lg py-2 text-sm font-medium text-gray-600">
              Annuler
            </button>
            <button type="submit" :disabled="submitting" class="flex-1 bg-blue-600 text-white rounded-lg py-2 text-sm font-medium hover:bg-blue-700 disabled:opacity-50">
              {{ submitting ? 'Envoi...' : 'Envoyer l\'invitation' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>