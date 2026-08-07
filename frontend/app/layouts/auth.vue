<script setup lang="ts">
const checklist = [
  'Créez votre espace en moins de deux minutes',
  'Invitez votre équipe par email',
  'Suivez projets, tâches et documents au même endroit',
]
</script>

<template>
  <div class="min-h-screen bg-ink-900 font-body text-white flex flex-col">
    <header class="px-8 py-5">
      <NuxtLink to="/" class="inline-flex items-center gap-2 text-sm text-mist hover:text-white transition-colors w-fit">
        <UIcon name="i-lucide-arrow-left" class="w-4 h-4" />
        Retour à l'accueil
      </NuxtLink>
    </header>

    <div class="flex-1 grid lg:grid-cols-2">
      <!-- Panneau illustré -->
      <div class="hidden lg:flex flex-col justify-center px-16 relative overflow-hidden">
        <div class="absolute top-1/4 -left-10 w-64 h-64 bg-brand/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 -right-10 w-72 h-72 bg-accent/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-md">
          <div class="flex items-center gap-2 font-display font-bold text-xl mb-8">
            <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-sm">🌳</div>
            Baobab
          </div>
          <h1 class="font-display font-bold text-3xl leading-snug mb-4">
            <slot name="side-title">Le travail d'équipe, structuré.</slot>
          </h1>
          <p class="text-mist mb-8 leading-relaxed">
            <slot name="side-text">Une plateforme unique pour administrateurs, chefs de projet et employés.</slot>
          </p>
          <div class="space-y-3 mb-10">
            <div v-for="item in checklist" :key="item" class="flex items-center gap-3">
              <div class="w-5 h-5 rounded-full bg-success-light flex items-center justify-center flex-shrink-0">
                <UIcon name="i-lucide-check" class="w-3 h-3 text-success" />
              </div>
              <span class="text-sm text-mist">{{ item }}</span>
            </div>
          </div>

          <div class="bg-ink-800 border border-ink-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-1.5 px-4 py-2.5 border-b border-ink-700">
              <span class="w-2 h-2 rounded-full bg-danger/70"></span>
              <span class="w-2 h-2 rounded-full bg-accent/70"></span>
              <span class="w-2 h-2 rounded-full bg-success/70"></span>
            </div>
            <div class="p-4 flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-brand"></div>
              <div class="space-y-1.5 flex-1">
                <div class="h-1.5 bg-ink-700 rounded w-2/3"></div>
                <div class="h-1.5 bg-ink-700 rounded w-1/2"></div>
              </div>
              <span class="text-xs px-2 py-1 rounded-full bg-success-light text-success font-medium">Actif</span>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md bg-ink-800 border border-ink-700 rounded-2xl p-8">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>