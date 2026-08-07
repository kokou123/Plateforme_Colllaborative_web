<!-- frontend/app/pages/index.vue -->
<script setup lang="ts">
definePageMeta({ layout: false })

const roles = [
  { name: 'Administrateur', desc: "Pilote l'entreprise entière : équipes, accès, et vision d'ensemble sur chaque projet en cours.", color: 'brand' },
  { name: 'Chef de projet', desc: "Organise le travail, répartit les tâches et suit chaque livrable jusqu'à la ligne d'arrivée.", color: 'accent' },
  { name: 'Employé', desc: 'Voit exactement ce qu\'il doit faire aujourd\'hui, sans se perdre dans des outils annexes.', color: 'success' },
]

const features = [
  { icon: 'i-lucide-users-round', title: 'Équipes structurées', desc: 'Organisez vos collaborateurs par équipe et donnez à chacun le bon niveau d\'accès.' },
  { icon: 'i-lucide-folder-kanban', title: 'Suivi de projets', desc: 'Statuts, échéances et membres assignés — visibles en un coup d\'œil.' },
  { icon: 'i-lucide-file-clock', title: 'Documents versionnés', desc: 'Chaque fichier garde son historique. Aucune version ne se perd.' },
  { icon: 'i-lucide-workflow', title: 'Processus métier', desc: 'Modélisez vos étapes de validation internes et suivez leur avancement.' },
  { icon: 'i-lucide-bell', title: 'Notifications ciblées', desc: 'Chacun est informé de ce qui le concerne, au bon moment.' },
  { icon: 'i-lucide-shield-check', title: 'Accès sécurisés', desc: 'Chaque rôle voit uniquement les informations qui le concernent.' },
]

const tools = [
  { name: 'Google Drive', icon: 'i-lucide-hard-drive' },
  { name: 'Trello', icon: 'i-lucide-columns-3' },
  { name: 'Slack', icon: 'i-lucide-message-square' },
  { name: 'Notion', icon: 'i-lucide-file-text' },
  { name: 'Google Calendar', icon: 'i-lucide-calendar' },
  { name: 'Asana', icon: 'i-lucide-check-circle-2' },
  { name: 'Microsoft Teams', icon: 'i-lucide-video' },
  { name: 'Dropbox', icon: 'i-lucide-box' },
]

const steps = [
  { n: '01', title: 'Créez votre entreprise', desc: 'Renseignez les informations de votre structure et devenez administrateur en moins de deux minutes.' },
  { n: '02', title: 'Invitez votre équipe', desc: "L'administrateur invite le chef de projet et les employés — chacun reçoit un lien d'activation par email." },
  { n: '03', title: 'Travaillez ensemble', desc: "Projets, tâches, documents et processus s'organisent dans un seul espace, adapté au rôle de chacun." },
]

const stats = [
  { value: '3', label: 'Rôles distincts' },
  { value: '100%', label: 'Traçabilité des actions' },
  { value: '<2min', label: 'Pour créer son espace' },
]
</script>

<template>
  <div class="min-h-screen bg-ink-900 font-body text-white">
    <!-- Nav -->
    <nav class="sticky top-0 z-20 bg-ink-900/85 backdrop-blur border-b border-ink-700">
      <div class="flex items-center justify-between px-8 py-5 max-w-7xl mx-auto">
        <a href="#top" class="flex items-center gap-2 font-display font-bold text-lg">
          <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-sm">🌳</div>
          Baobab
        </a>
        <div class="hidden md:flex items-center gap-9">
          <a href="#roles" class="text-xs font-semibold uppercase tracking-wide text-mist hover:text-white transition-colors">Rôles</a>
          <a href="#fonctionnalites" class="text-xs font-semibold uppercase tracking-wide text-mist hover:text-white transition-colors">Fonctionnalités</a>
          <a href="#comment-ca-marche" class="text-xs font-semibold uppercase tracking-wide text-mist hover:text-white transition-colors">Comment ça marche</a>
          <a href="#assistant" class="text-xs font-semibold uppercase tracking-wide text-mist hover:text-white transition-colors">Assistant IA</a>
        </div>
        <div class="flex items-center gap-4">
          <NuxtLink to="/login" class="text-sm font-medium text-mist hover:text-white">Connexion</NuxtLink>
          <NuxtLink to="/register" class="text-sm font-medium text-ink-900 bg-white px-5 py-2.5 rounded-lg hover:bg-brand hover:text-white transition-colors">
            Créer une entreprise
          </NuxtLink>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section id="top" class="max-w-6xl mx-auto px-6 pt-20 pb-16">
      <div class="grid lg:grid-cols-2 gap-14 items-center">
        <div>
          <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-brand mb-6">
            <span class="w-2.5 h-2.5 bg-brand rounded-sm"></span>
            La plateforme collaborative togolaise
          </div>
          <h1 class="font-display font-extrabold text-5xl leading-[1.08] mb-6">
            Une seule plateforme.<br />
            <span class="text-brand">Trois rôles.</span> Zéro friction.
          </h1>
          <p class="text-mist text-lg mb-10 leading-relaxed max-w-lg">
            Administrateurs, chefs de projet et employés travaillent enfin dans le même espace —
            chacun voit exactement ce dont il a besoin, rien de plus.
          </p>
          <div class="flex items-center gap-5">
            <NuxtLink to="/register" class="bg-brand text-white px-7 py-3.5 rounded-lg font-medium hover:bg-white hover:text-ink-900 transition-colors">
              Créer mon entreprise
            </NuxtLink>
            <NuxtLink to="/login" class="flex items-center gap-2 text-sm font-semibold text-white hover:text-brand transition-colors">
              J'ai déjà un compte
              <UIcon name="i-lucide-arrow-right" class="w-4 h-4" />
            </NuxtLink>
          </div>
        </div>

        <!-- Mockup produit (illustration originale, pas de photo) -->
        <div class="relative">
          <div class="absolute -top-10 -right-6 w-40 h-40 bg-brand/20 rounded-full blur-2xl"></div>
          <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-accent/10 rounded-full blur-2xl"></div>

          <div class="relative bg-ink-800 border border-ink-700 rounded-2xl overflow-hidden shadow-2xl">
            <div class="flex items-center gap-1.5 px-4 py-3 border-b border-ink-700">
              <span class="w-2.5 h-2.5 rounded-full bg-danger/70"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-accent/70"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-success/70"></span>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-brand"></div>
                <div class="space-y-1.5 flex-1">
                  <div class="h-2 bg-ink-700 rounded w-1/3"></div>
                  <div class="h-2 bg-ink-700 rounded w-1/2"></div>
                </div>
              </div>
              <div class="grid grid-cols-3 gap-3 pt-2">
                <div class="bg-ink-700/60 rounded-lg p-3">
                  <div class="h-2 bg-brand/60 rounded w-2/3 mb-2"></div>
                  <div class="h-5 bg-white/10 rounded w-1/2"></div>
                </div>
                <div class="bg-ink-700/60 rounded-lg p-3">
                  <div class="h-2 bg-accent/60 rounded w-2/3 mb-2"></div>
                  <div class="h-5 bg-white/10 rounded w-1/2"></div>
                </div>
                <div class="bg-ink-700/60 rounded-lg p-3">
                  <div class="h-2 bg-success/60 rounded w-2/3 mb-2"></div>
                  <div class="h-5 bg-white/10 rounded w-1/2"></div>
                </div>
              </div>
              <div class="h-24 bg-ink-700/40 rounded-lg flex items-end gap-2 p-3">
                <div class="flex-1 bg-brand rounded-t h-[40%]"></div>
                <div class="flex-1 bg-brand rounded-t h-[65%]"></div>
                <div class="flex-1 bg-brand rounded-t h-[45%]"></div>
                <div class="flex-1 bg-accent rounded-t h-[80%]"></div>
                <div class="flex-1 bg-brand rounded-t h-[55%]"></div>
              </div>
            </div>
          </div>

          <div class="absolute -bottom-6 -left-6 bg-white text-ink rounded-xl px-5 py-4 shadow-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-success-light flex items-center justify-center">
              <UIcon name="i-lucide-check" class="w-4 h-4 text-success" />
            </div>
            <div>
              <p class="text-sm font-semibold font-display">12 tâches</p>
              <p class="text-xs text-slate">terminées cette semaine</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Partenaires / outils -->
    <section class="border-y border-ink-700 py-12">
      <p class="text-center text-xs font-semibold uppercase tracking-widest text-mist mb-8">
        Inspiré des meilleurs outils du marché
      </p>
      <div class="relative flex overflow-hidden mask-fade">
        <div class="flex gap-14 animate-marquee shrink-0 pr-14">
          <div v-for="tool in [...tools, ...tools]" :key="tool.name + Math.random()" class="flex items-center gap-2.5 text-mist/70 hover:text-white transition-colors whitespace-nowrap">
            <UIcon :name="tool.icon" class="w-5 h-5" />
            <span class="font-medium text-sm">{{ tool.name }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="max-w-5xl mx-auto px-6 py-16 grid grid-cols-3 gap-6 text-center">
      <div v-for="s in stats" :key="s.label">
        <p class="font-display font-extrabold text-4xl text-brand">{{ s.value }}</p>
        <p class="text-mist text-sm mt-1">{{ s.label }}</p>
      </div>
    </section>

    <!-- Roles -->
    <section id="roles" class="max-w-6xl mx-auto px-6 py-20">
      <div class="text-center mb-12">
        <p class="text-xs font-semibold uppercase tracking-widest text-brand mb-3">Organisation</p>
        <h2 class="font-display font-bold text-3xl">Un accès pour chaque responsabilité</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-5">
        <div v-for="role in roles" :key="role.name" class="bg-ink-800 border border-ink-700 rounded-2xl p-7 relative overflow-hidden">
          <div :class="[
            'absolute top-0 left-0 w-full h-1',
            role.color === 'brand' ? 'bg-brand' : role.color === 'accent' ? 'bg-accent' : 'bg-success'
          ]"></div>
          <h3 class="font-display font-bold text-xl mb-2">{{ role.name }}</h3>
          <p class="text-mist text-sm leading-relaxed">{{ role.desc }}</p>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="fonctionnalites" class="bg-ink-800/50 border-y border-ink-700 py-20">
      <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
          <p class="text-xs font-semibold uppercase tracking-widest text-brand mb-3">Fonctionnalités</p>
          <h2 class="font-display font-bold text-3xl">Tout ce qu'il faut, rien de superflu</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-x-10 gap-y-12">
          <div v-for="f in features" :key="f.title">
            <div class="w-11 h-11 rounded-lg bg-brand/15 flex items-center justify-center mb-4">
              <UIcon :name="f.icon" class="w-5 h-5 text-brand" />
            </div>
            <h4 class="font-display font-semibold mb-1.5">{{ f.title }}</h4>
            <p class="text-mist text-sm leading-relaxed">{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section id="comment-ca-marche" class="max-w-5xl mx-auto px-6 py-20">
      <div class="text-center mb-14">
        <p class="text-xs font-semibold uppercase tracking-widest text-brand mb-3">Démarrage</p>
        <h2 class="font-display font-bold text-3xl">Trois étapes, pas plus</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <div v-for="step in steps" :key="step.n">
          <p class="font-display font-extrabold text-5xl text-ink-700 mb-3">{{ step.n }}</p>
          <h4 class="font-display font-semibold text-lg mb-2">{{ step.title }}</h4>
          <p class="text-mist text-sm leading-relaxed">{{ step.desc }}</p>
        </div>
      </div>
    </section>

    <!-- Assistant IA -->
    <section id="assistant" class="bg-ink-800/50 border-y border-ink-700 py-20">
      <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-14 items-center">
        <div class="order-2 lg:order-1 bg-ink-900 border border-ink-700 rounded-2xl p-7">
          <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center">
              <UIcon name="i-lucide-sparkles" class="w-4 h-4 text-white" />
            </div>
            <span class="font-display font-semibold text-sm">Assistant Baobab</span>
          </div>
          <div class="space-y-3">
            <div class="bg-ink-700 rounded-xl rounded-tl-sm px-4 py-2.5 text-sm max-w-[85%]">
              Résume l'avancement du projet "Site vitrine" cette semaine.
            </div>
            <div class="bg-brand rounded-xl rounded-tr-sm px-4 py-2.5 text-sm max-w-[90%] ml-auto">
              3 tâches terminées sur 5. La maquette est validée, il reste l'intégration et les tests.
            </div>
          </div>
        </div>

        <div class="order-1 lg:order-2">
          <p class="text-xs font-semibold uppercase tracking-widest text-accent mb-3">Nouveau</p>
          <h2 class="font-display font-bold text-3xl mb-5">Un assistant qui connaît vos projets</h2>
          <p class="text-mist leading-relaxed mb-6">
            Posez une question en langage naturel sur l'avancement d'un projet ou l'état des tâches d'une équipe.
            L'assistant répond avec les vraies données de votre entreprise.
          </p>
          <ul class="space-y-3">
            <li class="flex items-center gap-2.5 text-sm">
              <UIcon name="i-lucide-check" class="w-4 h-4 text-success flex-shrink-0" />
              Résumés de projets instantanés
            </li>
            <li class="flex items-center gap-2.5 text-sm">
              <UIcon name="i-lucide-check" class="w-4 h-4 text-success flex-shrink-0" />
              Disponible pour les chefs de projet
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section id="cta" class="py-20 text-center relative overflow-hidden">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-brand/10 rounded-full blur-3xl"></div>
      <div class="relative z-10">
        <h2 class="font-display font-bold text-3xl mb-4">Prêt à organiser votre entreprise ?</h2>
        <p class="text-mist mb-8">La création de votre espace prend moins de deux minutes.</p>
        <NuxtLink to="/register" class="inline-block bg-brand text-white px-7 py-3.5 rounded-lg font-medium hover:bg-white hover:text-ink-900 transition-colors">
          Commencer maintenant
        </NuxtLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-ink-700">
      <div class="max-w-6xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">
        <div>
          <div class="flex items-center gap-2 font-display font-bold mb-3">
            <div class="w-6 h-6 bg-brand rounded-md"></div>
            Baobab
          </div>
          <p class="text-mist text-sm leading-relaxed">La plateforme de gestion collaborative pensée pour les entreprises togolaises.</p>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Produit</p>
          <div class="flex flex-col gap-2 text-sm text-mist">
            <a href="#roles" class="hover:text-white">Rôles</a>
            <a href="#fonctionnalites" class="hover:text-white">Fonctionnalités</a>
          </div>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Compte</p>
          <div class="flex flex-col gap-2 text-sm text-mist">
            <NuxtLink to="/login" class="hover:text-white">Connexion</NuxtLink>
            <NuxtLink to="/register" class="hover:text-white">Créer une entreprise</NuxtLink>
          </div>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Contact</p>
          <p class="text-mist text-sm">Lomé, Togo</p>
        </div>
      </div>
      <div class="border-t border-ink-700 py-6 text-center text-mist text-xs">
        © {{ new Date().getFullYear() }} Baobab — Plateforme de gestion d'entreprise
      </div>
    </footer>
  </div>
</template>

<style scoped>
.mask-fade {
  mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
  -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
}
</style>