<!-- frontend/app/pages/verify-email.vue -->
<script setup lang="ts">
definePageMeta({ layout: 'auth' })

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const email = (route.query.email as string) || ''
const digits = ref(['', '', '', '', '', ''])
const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)
const inputs = ref<HTMLInputElement[]>([])

const onInput = (index: number) => {
  if (digits.value[index] && index < 5) inputs.value[index + 1]?.focus()
}

const submit = async () => {
  errorMessage.value = ''
  const code = digits.value.join('')
  if (code.length !== 6) {
    errorMessage.value = 'Entre les 6 chiffres du code'
    return
  }
  loading.value = true
  try {
    await authStore.verifyEmail(email, code)
    router.push('/login')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Code invalide'
  } finally {
    loading.value = false
  }
}

const resend = async () => {
  errorMessage.value = ''
  successMessage.value = ''
  await authStore.resendOtp(email)
  successMessage.value = 'Code renvoyé.'
}
</script>

<template>
  <div class="text-center">
    <div class="w-12 h-12 bg-brand-light rounded-xl flex items-center justify-center mx-auto mb-5">
      <UIcon name="i-lucide-mail-check" class="w-6 h-6 text-brand" />
    </div>
    <h1 class="font-display font-bold text-2xl mb-2">Confirme ton email</h1>
    <p class="text-slate mb-6 text-sm">Code envoyé à <strong class="text-ink">{{ email }}</strong></p>
    <div class="flex justify-center gap-2 mb-4">
      <input
        v-for="(d, i) in digits" :key="i" v-model="digits[i]" ref="inputs"
        maxlength="1" @input="onInput(i)"
        class="w-11 h-12 text-center border border-border rounded-lg text-lg font-mono focus:outline-none focus:border-brand"
      />
    </div>
    <p v-if="errorMessage" class="text-danger text-sm mb-2">{{ errorMessage }}</p>
    <p v-if="successMessage" class="text-success text-sm mb-2">{{ successMessage }}</p>
    <button @click="submit" :disabled="loading" class="w-full bg-brand text-white rounded-lg py-2.5 font-medium hover:bg-ink transition-colors mb-3 disabled:opacity-50">
      {{ loading ? 'Vérification...' : 'Vérifier' }}
    </button>
    <button @click="resend" class="text-sm text-brand font-medium">Renvoyer le code</button>
  </div>
</template>