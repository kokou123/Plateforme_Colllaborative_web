// frontend/stores/onboarding.ts
export const useOnboardingStore = defineStore('onboarding', () => {
  const mode = ref<'create' | 'join' | null>(null)
  const organisation = ref({
    nom: '',
    couleur: '#3B82F6',
    abreviation: '',
    taille_entreprise: '',
    secteur: '',
  })
  const inviteToken = ref<string | null>(null)

  const setMode = (value: 'create' | 'join') => {
    mode.value = value
  }

  const submitOrganisation = async () => {
    const { apiFetch } = useApi()
    return await apiFetch('/organisations', {
      method: 'POST',
      body: organisation.value,
    })
  }

  const joinOrganisation = async (token: string) => {
    const { apiFetch } = useApi()
    return await apiFetch('/organisations/join', {
      method: 'POST',
      body: { token },
    })
  }

  const reset = () => {
    mode.value = null
    organisation.value = { nom: '', couleur: '#3B82F6', abreviation: '', taille_entreprise: '', secteur: '' }
  }

  return { mode, organisation, inviteToken, setMode, submitOrganisation, joinOrganisation, reset }
})