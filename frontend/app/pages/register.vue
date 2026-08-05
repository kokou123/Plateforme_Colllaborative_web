<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')
const loading = ref(false)

const schema = toTypedSchema(
  z.object({
    nom: z.string().min(2, "Nom de l'entreprise requis"),
    secteur: z.string().optional(),
    taille: z.coerce.number().int().positive('Taille invalide'),
    email_entreprise: z.string().email('Email invalide'),
    telephone: z.string().optional(),
    adresse: z.string().optional(),
    nom_admin: z.string().min(2, 'Requis'),
    prenom_admin: z.string().min(2, 'Requis'),
    email_admin: z.string().email('Email invalide'),
    password: z.string().min(8, '8 caractères minimum'),
    password_confirmation: z.string(),
  }).refine((d) => d.password === d.password_confirmation, {
    message: 'Les mots de passe ne correspondent pas',
    path: ['password_confirmation'],
  })
)

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [nom] = defineField('nom')
const [secteur] = defineField('secteur')
const [taille] = defineField('taille')
const [email_entreprise] = defineField('email_entreprise')
const [telephone] = defineField('telephone')
const [adresse] = defineField('adresse')
const [nom_admin] = defineField('nom_admin')
const [prenom_admin] = defineField('prenom_admin')
const [email_admin] = defineField('email_admin')
const [password] = defineField('password')
const [password_confirmation] = defineField('password_confirmation')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  loading.value = true
  try {
    await authStore.registerEntreprise(values)
    router.push({ path: '/verify-email', query: { email: values.email_admin } })
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
})

const inputClass = "w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand"
</script>

<template>
  <div>
    <h1 class="font-display font-bold text-2xl mb-1">Créer votre entreprise</h1>
    <p class="text-slate text-sm mb-6">Vous devenez administrateur de l'espace.</p>

    <form @submit="onSubmit" class="space-y-3">
      <p class="text-xs font-mono font-semibold text-brand uppercase tracking-wide pt-2">Entreprise</p>
      <input v-model="nom" placeholder="Nom de l'entreprise" :class="inputClass" />
      <p v-if="errors.nom" class="text-danger text-xs">{{ errors.nom }}</p>

      <div class="grid grid-cols-2 gap-3">
        <input v-model="secteur" placeholder="Secteur" :class="inputClass" />
        <input v-model="taille" type="number" placeholder="Taille (nb employés)" :class="inputClass" />
      </div>
      <p v-if="errors.taille" class="text-danger text-xs">{{ errors.taille }}</p>

      <input v-model="email_entreprise" placeholder="Email entreprise" :class="inputClass" />
      <p v-if="errors.email_entreprise" class="text-danger text-xs">{{ errors.email_entreprise }}</p>

      <div class="grid grid-cols-2 gap-3">
        <input v-model="telephone" placeholder="Téléphone" :class="inputClass" />
        <input v-model="adresse" placeholder="Adresse" :class="inputClass" />
      </div>

      <p class="text-xs font-mono font-semibold text-brand uppercase tracking-wide pt-4">Administrateur</p>
      <div class="grid grid-cols-2 gap-3">
        <input v-model="prenom_admin" placeholder="Prénom" :class="inputClass" />
        <input v-model="nom_admin" placeholder="Nom" :class="inputClass" />
      </div>
      <p v-if="errors.nom_admin" class="text-danger text-xs">{{ errors.nom_admin }}</p>

      <input v-model="email_admin" placeholder="Votre email" :class="inputClass" />
      <p v-if="errors.email_admin" class="text-danger text-xs">{{ errors.email_admin }}</p>

      <input v-model="password" type="password" placeholder="Mot de passe" :class="inputClass" />
      <p v-if="errors.password" class="text-danger text-xs">{{ errors.password }}</p>

      <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" :class="inputClass" />
      <p v-if="errors.password_confirmation" class="text-danger text-xs">{{ errors.password_confirmation }}</p>

      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light rounded-lg px-3 py-2">{{ errorMessage }}</p>

      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors mt-2 disabled:opacity-50">
        {{ loading ? 'Création...' : 'Créer mon entreprise' }}
      </button>
    </form>

    <p class="text-center text-sm text-slate mt-6">
      Déjà un compte ? <NuxtLink to="/login" class="text-brand font-medium">Se connecter</NuxtLink>
    </p>
  </div>
</template>