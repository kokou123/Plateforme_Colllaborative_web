<template>
  <div class="min-h-screen flex bg-paper font-body">
    <aside class="hidden lg:flex lg:w-[38%] flex-col justify-between p-12 text-white bg-ink relative overflow-hidden">
      <div class="absolute -top-10 -right-10 w-56 h-56 bg-brand/30 rounded-full"></div>
      <div class="absolute -bottom-16 -left-10 w-64 h-64 bg-accent/20 rounded-full"></div>

      <NuxtLink to="/" class="flex items-center gap-2 font-display font-bold text-lg relative z-10">
        <div class="w-7 h-7 bg-brand rounded-md"></div>
        Baobab
      </NuxtLink>
      <div class="relative z-10">
        <slot name="side-title">
          <h1 class="font-display font-bold text-3xl mb-3 leading-snug">Bienvenue</h1>
        </slot>
        <slot name="side-text">
          <p class="text-white/70 text-sm leading-relaxed">
            Gérez vos équipes, projets et documents dans un seul espace collaboratif.
          </p>
        </slot>
      </div>
      <div class="text-white/40 text-xs relative z-10">© {{ new Date().getFullYear() }} Baobab</div>
    </aside>

    <main class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-white rounded-2xl border border-border shadow-sm p-8">
        <slot />
      </div>
    </main>
  </div>
</template>