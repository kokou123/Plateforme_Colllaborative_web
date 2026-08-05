export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  const { apiFetch, token } = useApi()

  if (!token.value) {
    return navigateTo('/login')
  }

  if (!authStore.user) {
    try {
      authStore.user = await apiFetch('/me')
    } catch {
      token.value = null
      return navigateTo('/login')
    }
  }
})