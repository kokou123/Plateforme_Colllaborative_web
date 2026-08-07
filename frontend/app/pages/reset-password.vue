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
    <p class="text-mist text-sm mb-7">Code envoyé à <strong class="text-white">{{ email }}</strong></p>

    <form @submit="onSubmit" class="space-y-4">
      <AuthInput v-model="otp" label="Code à 6 chiffres" icon="i-lucide-hash" placeholder="000000" :error="errors.otp" />
      <AuthInput v-model="password" label="Nouveau mot de passe" type="password" icon="i-lucide-lock" placeholder="8 caractères minimum" :error="errors.password" />
      <AuthInput v-model="password_confirmation" label="Confirmer le mot de passe" type="password" icon="i-lucide-lock" placeholder="••••••••" :error="errors.password_confirmation" />

      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light/10 border border-danger/20 rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button type="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-white hover:text-ink-900 transition-colors disabled:opacity-50">
        {{ loading ? 'Réinitialisation...' : 'Réinitialiser' }}
      </button>
    </form>
  </div>
</template>