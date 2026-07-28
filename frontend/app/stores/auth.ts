// frontend/app/stores/auth.ts
export const useAuthStore = defineStore('auth', () => {
  const user = ref<any>(null)
  const roles = ref<string[]>([])
  const isAuthenticated = computed(() => !!user.value)

  const registerEntreprise = async (payload: {
    nom: string; secteur: string; taille: number
    email_entreprise: string; telephone?: string; adresse?: string
    nom_admin: string; prenom_admin: string
    email_admin: string; password: string; password_confirmation: string
  }) => {
    const { getCsrfCookie, apiFetch } = useApi()
    await getCsrfCookie()
    return await apiFetch('/auth/register', { method: 'POST', body: payload })
  }

  const verifyEmail = async (email: string, otp: string) => {
    const { apiFetch } = useApi()
    return await apiFetch('/auth/verify-email', { method: 'POST', body: { email, otp } })
  }

  const resendOtp = async (email: string) => {
    const { apiFetch } = useApi()
    return await apiFetch('/auth/resend-otp', { method: 'POST', body: { email } })
  }

  const login = async (email: string, password: string) => {
    const { getCsrfCookie, apiFetch } = useApi()
    await getCsrfCookie()
    const response = await apiFetch<{ token: string; user: any; roles: string[] }>('/auth/login', {
      method: 'POST',
      body: { email, password },
    })
    user.value = response.user
    roles.value = response.roles
    return response
  }

  const activateAccount = async (token: string, password: string, password_confirmation: string) => {
    const { apiFetch } = useApi()
    const response = await apiFetch<{ token: string; user: any; roles: string[] }>('/auth/activation', {
      method: 'POST',
      body: { token, password, password_confirmation },
    })
    user.value = response.user
    roles.value = response.roles
    return response
  }

  const forgotPassword = async (email: string) => {
    const { apiFetch } = useApi()
    return await apiFetch('/auth/forgot-password', { method: 'POST', body: { email } })
  }

  const resetPassword = async (payload: { email: string; otp: string; password: string; password_confirmation: string }) => {
    const { apiFetch } = useApi()
    return await apiFetch('/auth/reset-password', { method: 'POST', body: payload })
  }

  return {
    user, roles, isAuthenticated,
    registerEntreprise, verifyEmail, resendOtp, login,
    activateAccount, forgotPassword, resetPassword,
  }
})