<script setup lang="ts">
definePageMeta({ layout: 'auth' })

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const token = route.params.token as string

const invite = ref<any>(null)
const loadError = ref('')
const password = ref('')
const password_confirmation = ref('')
const errorMessage = ref('')

const { apiFetch } = useApi()

onMounted(async () => {
  try {
    invite.value = await apiFetch(`/auth/activation/${token}`)
  } catch (e: any) {
    loadError.value = e?.data?.message || 'Lien invalide ou expiré.'
  }
})

const submit = async () => {
  errorMessage.value = ''
  if (password.value !== password_confirmation.value) {
    errorMessage.value = 'Les mots de passe ne correspondent pas'
    return
  }
  try {
    const res = await authStore.activateAccount(token, password.value, password_confirmation.value)
    if (res.roles.includes('Chef de projet')) router.push('/chef-projet/dashboard')
    else router.push('/employe/dashboard')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || "Erreur lors de l'activation"
  }
}
</script>

<template>
  <div v-if="loadError" class="text-center text-red-600">{{ loadError }}</div>

  <div v-else-if="invite">
    <h1 class="text-2xl font-semibold mb-1">Bienvenue chez {{ invite.entreprise }}</h1>
    <p class="text-gray-500 text-sm mb-6">{{ invite.prenom }} {{ invite.nom }}, choisissez votre mot de passe.</p>

    <div class="space-y-4">
      <input v-model="password" type="password" placeholder="Mot de passe" class="w-full border rounded-lg px-3 py-2" />
      <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full border rounded-lg px-3 py-2" />
      <p v-if="errorMessage" class="text-red-600 text-sm">{{ errorMessage }}</p>
      <button @click="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 font-medium hover:bg-blue-700">
        Activer mon compte
      </button>
    </div>
  </div>

  <div v-else class="text-center text-gray-400">Chargement...</div>
</template>