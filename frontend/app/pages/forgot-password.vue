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
    <h1 class="font-display font-bold text-2xl mb-1">Mot de passe oublié</h1>
    <p class="text-slate text-sm mb-6">Recevez un code pour réinitialiser votre mot de passe.</p>

    <form @submit="onSubmit" class="space-y-4">
      <div>
        <input v-model="email" placeholder="Email" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
        <p v-if="errors.email" class="text-danger text-xs mt-1">{{ errors.email }}</p>
      </div>
      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors disabled:opacity-50">
        {{ loading ? 'Envoi...' : 'Envoyer le code' }}
      </button>
    </form>

    <p class="text-center text-sm text-slate mt-6">
      <NuxtLink to="/login" class="text-brand font-medium">Retour à la connexion</NuxtLink>
    </p>
  </div>
</template>