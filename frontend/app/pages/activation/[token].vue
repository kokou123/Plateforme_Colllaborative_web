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
const loading = ref(false)

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
  loading.value = true
  try {
    const res = await authStore.activateAccount(token, password.value, password_confirmation.value)
    if (res.roles.includes('Chef de projet')) router.push('/chef-projet/dashboard')
    else router.push('/employe/dashboard')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || "Erreur lors de l'activation"
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div v-if="loadError" class="text-center">
    <div class="w-12 h-12 bg-danger-light rounded-xl flex items-center justify-center mx-auto mb-4">
      <UIcon name="i-lucide-link-2-off" class="w-6 h-6 text-danger" />
    </div>
    <p class="text-danger font-medium">{{ loadError }}</p>
  </div>

  <div v-else-if="invite">
    <h1 class="font-display font-bold text-2xl mb-1">Bienvenue chez {{ invite.entreprise }}</h1>
    <p class="text-slate text-sm mb-6">{{ invite.prenom }} {{ invite.nom }}, choisissez votre mot de passe.</p>

    <div class="space-y-4">
      <input v-model="password" type="password" placeholder="Mot de passe" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
      <input v-model="password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full border border-border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand" />
      <p v-if="errorMessage" class="text-danger text-sm bg-danger-light rounded-lg px-3 py-2">{{ errorMessage }}</p>
      <button @click="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors disabled:opacity-50">
        {{ loading ? 'Activation...' : 'Activer mon compte' }}
      </button>
    </div>
  </div>

  <div v-else class="text-center text-slate">Chargement...</div>
</template>