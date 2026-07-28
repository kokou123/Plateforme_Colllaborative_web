<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')

const schema = toTypedSchema(z.object({
  email: z.string().email('Email invalide'),
}))

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [email] = defineField('email')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  try {
    await authStore.forgotPassword(values.email)
    router.push({ path: '/reset-password', query: { email: values.email } })
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Une erreur est survenue'
  }
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-semibold mb-1">Mot de passe oublié</h1>
    <p class="text-gray-500 text-sm mb-6">Recevez un code pour réinitialiser votre mot de passe.</p>

    <form @submit="onSubmit" class="space-y-4">
      <div>
        <input v-model="email" placeholder="Email" class="w-full border rounded-lg px-3 py-2" />
        <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
      </div>
      <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>
      <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 font-medium hover:bg-blue-700">
        Envoyer le code
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
      <NuxtLink to="/login" class="text-blue-600 font-medium">Retour à la connexion</NuxtLink>
    </p>
  </div>
</template>