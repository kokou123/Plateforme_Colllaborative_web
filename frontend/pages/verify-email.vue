<!-- frontend/pages/verify-email.vue -->
<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()
const digits = ref(['', '', '', '', '', ''])
const errorMessage = ref('')
const inputs = ref<HTMLInputElement[]>([])

const onInput = (index: number) => {
  if (digits.value[index] && index < 5) {
    inputs.value[index + 1]?.focus()
  }
}

const submit = async () => {
  errorMessage.value = ''
  const code = digits.value.join('')
  if (code.length !== 6) {
    errorMessage.value = 'Entre les 6 chiffres du code'
    return
  }
  try {
    await authStore.verifyEmail(code)
    router.push('/onboarding/choice')
  } catch (e: any) {
    errorMessage.value = e?.data?.message || 'Code invalide'
  }
}

const resend = async () => {
  errorMessage.value = ''
  await authStore.resendCode()
}
</script>

<template>
  <div class="max-w-md mx-auto mt-20 p-8 text-center">
    <h1 class="text-2xl font-medium mb-2">Confirme ton email</h1>
    <p class="text-gray-500 mb-6">Entre le code reçu par email</p>
    <div class="flex justify-center gap-2 mb-4">
      <input
        v-for="(d, i) in digits"
        :key="i"
        v-model="digits[i]"
        ref="inputs"
        maxlength="1"
        @input="onInput(i)"
        class="w-10 h-12 text-center border rounded text-lg"
      />
    </div>
    <p v-if="errorMessage" class="text-red-600 text-sm mb-4">{{ errorMessage }}</p>
    <button @click="submit" class="w-full bg-blue-600 text-white rounded py-2 mb-3">Vérifier</button>
    <button @click="resend" class="text-sm text-blue-600">Renvoyer le code</button>
  </div>
</template>