export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()
  const requiredRole = to.meta.role as string | undefined

  console.log('MIDDLEWARE ROLE — requis:', requiredRole, '— utilisateur a:', authStore.roles)

  if (requiredRole && !authStore.roles.includes(requiredRole)) {
    console.log('BLOQUÉ par role.ts, retour login')
    return navigateTo('/login')
  }
})