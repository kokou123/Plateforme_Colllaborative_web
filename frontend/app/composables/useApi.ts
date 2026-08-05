export const useApi = () => {
  const config = useRuntimeConfig()
  const base = config.public.apiBase
  const token = useCookie<string | null>('auth_token', { default: () => null })

  const apiFetch = async <T>(url: string, options: any = {}) => {
    return $fetch<T>(url, {
      baseURL: `${base}/api`,
      headers: {
        Accept: 'application/json',
        ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
        ...options.headers,
      },
      ...options,
    })
  }

  return { apiFetch, token }
}