// frontend/composables/useApi.ts
export const useApi = () => {
  const config = useRuntimeConfig()
  const base = config.public.apiBase

  const getCsrfCookie = async () => {
    await $fetch('/sanctum/csrf-cookie', {
      baseURL: base,
      credentials: 'include',
    })
  }

  const apiFetch = async <T>(url: string, options: any = {}) => {
    return $fetch<T>(url, {
      baseURL: `${base}/api`,
      credentials: 'include',
      headers: {
        Accept: 'application/json',
        ...options.headers,
      },
      ...options,
    })
  }

  return { getCsrfCookie, apiFetch }
}