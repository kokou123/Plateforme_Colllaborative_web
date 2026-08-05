<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

definePageMeta({ layout: 'auth' })

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const errorMessage = ref('')
const loading = ref(false)
const email = (route.query.email as string) || ''

const schema = toTypedSchema(
  z.object({
    otp: z.string().length(6, 'Le code doit faire 6 chiffres'),
    password: z.string().min(8, '8 caractères minimum'),
    password_confirmation: z.string(),
  }).refine((d) => d.password === d.password_confirmation, {
    message: 'Les mots de passe ne correspondent pas',
    path: ['password_confirmation'],
  })
)

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [otp] = defineField('otp')
const [password] = defineField('password')
const [password_confirmation] = defineField('password_confirmation')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  loading.value = true
  try {
    await authStore.resetPassword({ email, ...values })
    router.push('/login')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Code invalide ou expiré'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h1 class="font-display font-bold text-2xl mb-1">Réinitialiser le mot de passe</h1>
    <p class="text-slate text-sm mb-6">Code envoyé à <strong class="text-ink">{{ email }}</strong></p>

    <form @submit="onSubmit" class="space-y-4">
      <input v-model="otp" placeholder="Code à 6 chiffres" maxlength="6" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm font-mono tracking-widest focus:outline-none focus:border-brand" />
      <p v-if="errors.otp" class="text-danger text-xs">{{ errors.otp }}</p>

      <input v-model="password" type="password" placeholder="Nouveau mot de passe" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
      <p v-if="errors.password" class="text-danger text-xs">{{ errors.password }}</p>

      <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
      <p v-if="errors.password_confirmation" class="text-danger text-xs">{{ errors.password_confirmation }}</p>

      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors disabled:opacity-50">
        {{ loading ? 'Réinitialisation...' : 'Réinitialiser' }}
      </button>
    </form>
  </div>
</template>