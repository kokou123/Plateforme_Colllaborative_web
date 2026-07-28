<script setup lang="ts">
definePageMeta({ layout: false })
</script>

<template>
  <div class="min-h-screen bg-white">
    <nav class="flex items-center justify-between px-8 py-5 border-b">
      <div class="flex items-center gap-2 font-semibold text-lg">
        <div class="w-6 h-6 bg-gradient-to-br from-blue-600 to-emerald-500 rounded"></div>
        Plateforme Collaborative de gestion de projets
      </div>
      <div class="flex items-center gap-4">
        <NuxtLink to="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900">Connexion</NuxtLink>
        <NuxtLink to="/register" class="text-sm font-medium text-white bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">
          Créer une entreprise
        </NuxtLink>
      </div>
    </nav>

    <section class="max-w-4xl mx-auto text-center py-24 px-6">
      <h1 class="text-5xl font-bold mb-6 leading-tight">
        <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-emerald-500 bg-clip-text text-transparent">
          Tout votre travail. Toute votre équipe. Un seul espace.
        </span>
      </h1>
      <p class="text-gray-500 text-lg mb-10">
        Gérez vos projets, tâches, équipes et documents dans une plateforme unifiée, pensée pour votre entreprise.
      </p>
      <NuxtLink to="/register" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700">
        Commencer gratuitement
      </NuxtLink>
    </section>
  </div>
</template>