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
  password: z.string().min(1, 'Mot de passe requis'),
}))

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [email] = defineField('email')
const [password] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  try {
    const res = await authStore.login(values.email, values.password)
    if (res.roles.includes('Admin')) router.push('/admin/dashboard')
    else if (res.roles.includes('Chef de projet')) router.push('/chef-projet/dashboard')
    else router.push('/employe/dashboard')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Email ou mot de passe incorrect'
  }
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-semibold mb-1">Connexion</h1>
    <p class="text-gray-500 text-sm mb-6">Accédez à votre espace de travail.</p>

    <form @submit="onSubmit" class="space-y-4">
      <div>
        <input v-model="email" placeholder="Email" class="w-full border rounded-lg px-3 py-2" />
        <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
      </div>
      <div>
        <input v-model="password" type="password" placeholder="Mot de passe" class="w-full border rounded-lg px-3 py-2" />
        <p v-if="errors.password" class="text-red-600 text-xs mt-1">{{ errors.password }}</p>
      </div>
      <div class="text-right">
        <NuxtLink to="/forgot-password" class="text-sm text-blue-600">Mot de passe oublié ?</NuxtLink>
      </div>
      <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>
      <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 font-medium hover:bg-blue-700">
        Se connecter
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
      Pas encore d'entreprise ? <NuxtLink to="/register" class="text-blue-600 font-medium">En créer une</NuxtLink>
    </p>
  </div>
</template>