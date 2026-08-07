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
    if (res.roles.includes('Admin')) router.push('/admin/dashboard')
    else if (res.roles.includes('Chef de projet')) router.push('/chef-projet/dashboard')
    else router.push('/employe/dashboard')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h1 class="font-display font-bold text-2xl mb-1">Connexion</h1>
    <p class="text-mist text-sm mb-7">Accédez à votre espace de travail.</p>

    <form @submit="onSubmit" class="space-y-4">
      <AuthInput v-model="email" label="Email" icon="i-lucide-mail" placeholder="vous@entreprise.com" :error="errors.email" />
      <AuthInput v-model="password" label="Mot de passe" type="password" icon="i-lucide-lock" placeholder="••••••••" :error="errors.password" />

      <div class="text-right">
        <NuxtLink to="/forgot-password" class="text-sm text-brand font-medium hover:text-white transition-colors">Mot de passe oublié ?</NuxtLink>
      </div>

      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ errorMessage }}</p>

      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
        {{ loading ? 'Connexion...' : 'Se connecter' }}
        <UIcon v-if="!loading" name="i-lucide-arrow-right" class="w-4 h-4" />
      </button>
    </form>

    <p class="text-center text-sm text-mist mt-7">
      Pas encore d'entreprise ? <NuxtLink to="/register" class="text-brand font-medium hover:text-white transition-colors">En créer une</NuxtLink>
    </p>
  </div>
</template>