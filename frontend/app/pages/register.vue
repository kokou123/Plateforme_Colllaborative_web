<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')
const loading = ref(false)
const activeTab = ref<'entreprise' | 'admin'>('entreprise')

const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'\-\/]+$/

const schema = toTypedSchema(
  z.object({
    nom: z.string().min(2, "Nom de l'entreprise requis"), // entreprise : chiffres autorisés (ex: "24H Services")
    secteur: z.string().optional(),
    taille: z.coerce.number().int().positive('Taille invalide'),
    email_entreprise: z.string().email('Email invalide'),
    telephone: z.string().optional(),
    adresse: z.string().optional(),
    nom_admin: z.string().min(2, 'Requis').regex(nameRegex, 'Lettres uniquement'),
    prenom_admin: z.string().min(2, 'Requis').regex(nameRegex, 'Lettres uniquement'),
    email_admin: z.string().email('Email invalide'),
    password: z.string().min(8, '8 caractères minimum'),
    password_confirmation: z.string(),
  }).refine((d) => d.password === d.password_confirmation, {
    message: 'Les mots de passe ne correspondent pas',
    path: ['password_confirmation'],
  })
)

const { handleSubmit, defineField, errors, validateField } = useForm({ validationSchema: schema })
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

const goToAdminTab = async () => {
  const fields = ['nom', 'taille', 'email_entreprise'] as const
  const results = await Promise.all(fields.map(f => validateField(f)))
  if (results.every(r => r.valid)) activeTab.value = 'admin'
}

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
</script>

<template>
  <div>
    <h1 class="font-display font-bold text-2xl mb-1">Créer votre entreprise</h1>
    <p class="text-mist text-sm mb-6">Vous devenez administrateur de l'espace.</p>

    <!-- Onglets -->
    <div class="flex items-center gap-2 mb-6">
      <button
        type="button" @click="activeTab = 'entreprise'"
        :class="['flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-colors',
          activeTab === 'entreprise' ? 'bg-brand text-white' : 'bg-ink-900 text-mist border border-ink-700']"
      >
        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs">1</span>
        Entreprise
      </button>
      <button
        type="button" @click="goToAdminTab"
        :class="['flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-colors',
          activeTab === 'admin' ? 'bg-brand text-white' : 'bg-ink-900 text-mist border border-ink-700']"
      >
        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs">2</span>
        Administrateur
      </button>
    </div>

    <form @submit="onSubmit" class="space-y-4">
      <div v-show="activeTab === 'entreprise'" class="space-y-4">
        <AuthInput v-model="nom" label="Nom de l'entreprise" icon="i-lucide-building-2" placeholder="Ex: Baobab SARL" :error="errors.nom" />
        <div class="grid grid-cols-2 gap-3">
          <AuthInput v-model="secteur" label="Secteur" icon="i-lucide-briefcase" placeholder="Informatique" />
          <AuthInput v-model="taille" label="Taille" icon="i-lucide-users" placeholder="20" type="number" :error="errors.taille" />
        </div>
        <AuthInput v-model="email_entreprise" label="Email entreprise" icon="i-lucide-mail" placeholder="contact@entreprise.com" :error="errors.email_entreprise" />
        <div class="grid grid-cols-2 gap-3">
          <AuthInput v-model="telephone" label="Téléphone" icon="i-lucide-phone" placeholder="+228..." />
          <AuthInput v-model="adresse" label="Adresse" icon="i-lucide-map-pin" placeholder="Lomé" />
        </div>

        <button type="button" @click="goToAdminTab" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-white hover:text-ink-900 transition-colors flex items-center justify-center gap-2">
          Continuer
          <UIcon name="i-lucide-arrow-right" class="w-4 h-4" />
        </button>
      </div>

      <div v-show="activeTab === 'admin'" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <AuthInput v-model="prenom_admin" label="Prénom" icon="i-lucide-user" placeholder="Prénom" :error="errors.prenom_admin" />
          <AuthInput v-model="nom_admin" label="Nom" icon="i-lucide-user" placeholder="Nom" />
        </div>
        <AuthInput v-model="email_admin" label="Votre email" icon="i-lucide-mail" placeholder="vous@entreprise.com" :error="errors.email_admin" />
        <AuthInput v-model="password" label="Mot de passe" type="password" icon="i-lucide-lock" placeholder="8 caractères minimum" :error="errors.password" />
        <AuthInput v-model="password_confirmation" label="Confirmer le mot de passe" type="password" icon="i-lucide-lock" placeholder="••••••••" :error="errors.password_confirmation" />

        <p v-if="errorMessage" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ errorMessage }}</p>

        <div class="flex gap-3">
          <button type="button" @click="activeTab = 'entreprise'" class="border border-ink-700 rounded-lg px-4 py-2.5 text-sm font-medium text-mist hover:text-white transition-colors">
            Retour
          </button>
          <button type="submit" :disabled="loading" class="flex-1 bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
            {{ loading ? 'Création...' : 'Créer mon entreprise' }}
          </button>
        </div>
      </div>
    </form>

    <p class="text-center text-sm text-mist mt-7">
      Déjà un compte ? <NuxtLink to="/login" class="text-brand font-medium hover:text-white transition-colors">Se connecter</NuxtLink>
    </p>
  </div>
</template>