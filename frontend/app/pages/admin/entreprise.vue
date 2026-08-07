<!-- frontend/app/pages/admin/entreprise.vue -->
<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'admin', middleware: ['auth', 'role'], role: 'Admin' })

const { apiFetch } = useApi()
const loading = ref(true)
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const schema = toTypedSchema(z.object({
  nom: z.string().min(2, 'Requis'),
  secteur: z.string().optional(),
  taille: z.coerce.number().int().positive('Invalide'),
  email: z.string().email('Email invalide'),
  telephone: z.string().optional(),
  adresse: z.string().optional(),
}))

const { handleSubmit, defineField, errors, setValues } = useForm({ validationSchema: schema })
const [nom] = defineField('nom')
const [secteur] = defineField('secteur')
const [taille] = defineField('taille')
const [email] = defineField('email')
const [telephone] = defineField('telephone')
const [adresse] = defineField('adresse')

onMounted(async () => {
  try {
    const res = await apiFetch<{ data: any }>('/entreprise')
    setValues(res.data)
  } finally {
    loading.value = false
  }
})

const onSubmit = handleSubmit(async (values) => {
  saving.value = true
  successMessage.value = ''
  errorMessage.value = ''
  try {
    await apiFetch('/entreprise', { method: 'PUT', body: values })
    successMessage.value = 'Informations mises à jour.'
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Erreur lors de la mise à jour.'
  } finally {
    saving.value = false
  }
})
</script>

<template>
  <div>
    <p class="text-xs font-semibold uppercase tracking-wide text-brand mb-1">Organisation</p>
    <h1 class="font-display font-bold text-2xl mb-6">Entreprise</h1>

    <div v-if="loading" class="text-mist">Chargement...</div>

    <div v-else class="bg-ink-800 border border-ink-700 rounded-xl p-6 max-w-2xl">
      <form @submit="onSubmit" class="space-y-4">
        <AuthInput v-model="nom" label="Nom de l'entreprise" icon="i-lucide-building-2" :error="errors.nom" />

        <div class="grid grid-cols-2 gap-3">
          <AuthInput v-model="secteur" label="Secteur" icon="i-lucide-briefcase" />
          <AuthInput v-model="taille" label="Taille" icon="i-lucide-users" type="number" :error="errors.taille" />
        </div>

        <AuthInput v-model="email" label="Email" icon="i-lucide-mail" :error="errors.email" />

        <div class="grid grid-cols-2 gap-3">
          <AuthInput v-model="telephone" label="Téléphone" icon="i-lucide-phone" />
          <AuthInput v-model="adresse" label="Adresse" icon="i-lucide-map-pin" />
        </div>

        <p v-if="successMessage" class="text-success text-sm bg-success-light/10 border border-success/20 rounded-lg px-3 py-2">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ errorMessage }}</p>

        <button type="submit" :disabled="saving" class="bg-brand text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
          {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </form>
    </div>
  </div>
</template>