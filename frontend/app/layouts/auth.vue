<!-- frontend/app/layouts/auth.vue -->
<template>
  <div class="min-h-screen flex bg-white">
    <aside class="hidden lg:flex lg:w-[38%] flex-col justify-between p-12 text-white bg-gradient-to-br from-blue-600 via-purple-600 to-emerald-500">
      <div class="flex items-center gap-2 font-semibold text-lg">
        <div class="w-7 h-7 bg-white/90 rounded-md"></div>
        Plateforme Collaborative de gestion de projets
      </div>
      <div>
        <slot name="side-title">
          <h1 class="text-3xl font-bold mb-3 leading-snug">Bienvenue</h1>
        </slot>
        <slot name="side-text">
          <p class="text-white/85 text-sm leading-relaxed">
            Gérez vos équipes, projets et documents dans un seul espace collaboratif.
          </p>
        </slot>
      </div>
      <div class="text-white/60 text-xs">© {{ new Date().getFullYear() }} Plateforme Collaborative de gestion de projets</div>
    </aside>

    <main class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <slot />
      </div>
    </main>
  </div>
</template>