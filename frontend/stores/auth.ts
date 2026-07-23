// frontend/stores/auth.ts
export const useAuthStore = defineStore('auth', () => {
  const user = ref<any>(null)
  const isAuthenticated = computed(() => !!user.value)

  const register = async (payload: { nom: string; prenom: string; email: string; password: string; password_confirmation: string }) => {
    const { getCsrfCookie, apiFetch } = useApi()
    await getCsrfCookie()
    await apiFetch('/register', { method: 'POST', body: payload })
  }

  const verifyEmail = async (code: string) => {
    const { apiFetch } = useApi()
    const response = await apiFetch<{ user: any }>('/email/verify', {
      method: 'POST',
      body: { code },
    })
    user.value = response.user
  }

  const resendCode = async () => {
    const { apiFetch } = useApi()
    await apiFetch('/email/resend', { method: 'POST' })
  }

  const fetchUser = async () => {
    const { apiFetch } = useApi()
    try {
      user.value = await apiFetch('/user')
    } catch {
      user.value = null
    }
  }

  return { user, isAuthenticated, register, verifyEmail, resendCode, fetchUser }
})