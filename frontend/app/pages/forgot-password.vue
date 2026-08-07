<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')
const loading = ref(false)

const schema = toTypedSchema(z.object({ email: z.string().email('Email invalide') }))
const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [email] = defineField('email')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  loading.value = true
  try {
    await authStore.forgotPassword(values.email)
    router.push({ path: '/reset-password', query: { email: values.email } })
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <div class="w-14 h-14 bg-accent/15 rounded-2xl flex items-center justify-center mb-5">
      <UIcon name="i-lucide-key-round" class="w-7 h-7 text-accent" />
    </div>
    <h1 class="font-display font-bold text-2xl mb-1">Mot de passe oublié</h1>
    <p class="text-mist text-sm mb-7">Recevez un code pour réinitialiser votre mot de passe.</p>

    <form @submit="onSubmit" class="space-y-4">
      <AuthInput v-model="email" label="Email" icon="i-lucide-mail" placeholder="vous@entreprise.com" :error="errors.email" />
      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
        {{ loading ? 'Envoi...' : 'Envoyer le code' }}
      </button>
    </form>

    <p class="text-center text-sm text-mist mt-7">
      <NuxtLink to="/login" class="text-brand font-medium hover:text-white transition-colors">Retour à la connexion</NuxtLink>
    </p>
  </div>
</template>