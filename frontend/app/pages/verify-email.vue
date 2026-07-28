<script setup lang="ts">
definePageMeta({ layout: 'auth' })

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const email = (route.query.email as string) || ''
const digits = ref(['', '', '', '', '', ''])
const errorMessage = ref('')
const successMessage = ref('')
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
  try {
    await authStore.verifyEmail(email, code)
    router.push('/login')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Code invalide'
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
    <h1 class="text-2xl font-semibold mb-2">Confirme ton email</h1>
    <p class="text-gray-500 mb-6 text-sm">Code envoyé à <strong>{{ email }}</strong></p>
    <div class="flex justify-center gap-2 mb-4">
      <input
        v-for="(d, i) in digits" :key="i" v-model="digits[i]" ref="inputs"
        maxlength="1" @input="onInput(i)"
        class="w-11 h-12 text-center border rounded-lg text-lg"
      />
    </div>
    <p v-if="errorMessage" class="text-red-600 text-sm mb-2">{{ errorMessage }}</p>
    <p v-if="successMessage" class="text-emerald-600 text-sm mb-2">{{ successMessage }}</p>
    <button @click="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 font-medium hover:bg-blue-700 mb-3">
      Vérifier
    </button>
    <button @click="resend" class="text-sm text-blue-600">Renvoyer le code</button>
  </div>
</template>