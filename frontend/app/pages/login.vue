<!-- frontend/app/pages/login.vue -->
<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')
const loading = ref(false)

const schema = toTypedSchema(z.object({
  email: z.string().email('Email invalide'),
  password: z.string().min(1, 'Mot de passe requis'),
}))

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [email] = defineField('email')
const [password] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  loading.value = true
  try {
    const res = await authStore.login(values.email, values.password)
    console.log('LOGIN OK — roles reçus:', res.roles)
    console.log('authStore.roles après login:', authStore.roles)

    if (res.roles.includes('Admin')) {
      console.log('Redirection vers /admin/dashboard')
      await router.push('/admin/dashboard')
    } else if (res.roles.includes('Chef de projet')) {
      await router.push('/chef-projet/dashboard')
    } else {
      await router.push('/employe/dashboard')
    }
  } catch (e: any) {
    console.error('ERREUR LOGIN:', e)
    errorMessage.value = e?.data?.message || 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h1 class="font-display font-bold text-2xl mb-1">Connexion</h1>
    <p class="text-slate text-sm mb-6">Accédez à votre espace de travail.</p>

    <form @submit="onSubmit" class="space-y-4">
      <div>
        <label class="text-xs font-medium text-slate mb-1.5 block">Email</label>
        <input v-model="email" placeholder="vous@entreprise.com" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
        <p v-if="errors.email" class="text-danger text-xs mt-1">{{ errors.email }}</p>
      </div>
      <div>
        <label class="text-xs font-medium text-slate mb-1.5 block">Mot de passe</label>
        <input v-model="password" type="password" placeholder="••••••••" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
        <p v-if="errors.password" class="text-danger text-xs mt-1">{{ errors.password }}</p>
      </div>
      <div class="text-right">
        <NuxtLink to="/forgot-password" class="text-sm text-brand font-medium">Mot de passe oublié ?</NuxtLink>
      </div>
      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors disabled:opacity-50">
        {{ loading ? 'Connexion...' : 'Se connecter' }}
      </button>
    </form>

    <p class="text-center text-sm text-slate mt-6">
      Pas encore d'entreprise ? <NuxtLink to="/register" class="text-brand font-medium">En créer une</NuxtLink>
    </p>
  </div>
</template>