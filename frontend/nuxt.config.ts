export default defineNuxtConfig({
  compatibilityDate: '2026-07-23',
  modules: ['@pinia/nuxt', '@nuxt/ui', '@vueuse/nuxt'],
  css: ['~/assets/css/main.css'],
  colorMode: {
    preference: 'light',
    fallback: 'light',
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000',
    },
  },
})