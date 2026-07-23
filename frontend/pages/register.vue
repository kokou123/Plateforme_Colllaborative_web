<!-- frontend/pages/register.vue -->
<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { useForm } from 'vee-validate'

const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')

const schema = toTypedSchema(
  z.object({
    nom: z.string().min(2, 'Le nom est requis'),
    prenom: z.string().min(2, 'Le prénom est requis'),
    email: z.string().email('Email invalide'),
    password: z.string().min(8, '8 caractères minimum'),
    password_confirmation: z.string(),
  }).refine((data) => data.password === data.password_confirmation, {
    message: 'Les mots de passe ne correspondent pas',
    path: ['password_confirmation'],
  })
)

const { handleSubmit, defineField, errors } = useForm({ validationSchema: schema })
const [nom] = defineField('nom')
const [prenom] = defineField('prenom')
const [email] = defineField('email')
const [password] = defineField('password')
const [password_confirmation] = defineField('password_confirmation')

const onSubmit = handleSubmit(async (values) => {
  errorMessage.value = ''
  try {
    await authStore.register(values)
    router.push('/verify-email')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Une erreur est survenue'
  }
})
</script>

<template>
  <div class="max-w-md mx-auto mt-20 p-8">
    <h1 class="text-2xl font-medium mb-6">Créer un compte</h1>
    <form @submit="onSubmit" class="space-y-4">
      <div>
        <input v-model="prenom" placeholder="Prénom" class="w-full border rounded px-3 py-2" />
        <p v-if="errors.prenom" class="text-red-600 text-sm">{{ errors.prenom }}</p>
      </div>
      <div>
        <input v-model="nom" placeholder="Nom" class="w-full border rounded px-3 py-2" />
        <p v-if="errors.nom" class="text-red-600 text-sm">{{ errors.nom }}</p>
      </div>
      <div>
        <input v-model="email" type="email" placeholder="Email" class="w-full border rounded px-3 py-2" />
        <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email }}</p>
      </div>
      <div>
        <input v-model="password" type="password" placeholder="Mot de passe" class="w-full border rounded px-3 py-2" />
        <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password }}</p>
      </div>
      <div>
        <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full border rounded px-3 py-2" />
        <p v-if="errors.password_confirmation" class="text-red-600 text-sm">{{ errors.password_confirmation }}</p>
      </div>
      <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>
      <button type="submit" class="w-full bg-blue-600 text-white rounded py-2">S'inscrire</button>
    </form>
  </div>
</template>