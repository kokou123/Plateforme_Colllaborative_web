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
  <div class="min-h-screen bg-paper font-body text-ink">
    <!-- Nav -->
    <nav class="sticky top-0 z-20 bg-paper/90 backdrop-blur border-b border-border">
      <div class="flex items-center justify-between px-8 py-4 max-w-7xl mx-auto">
        <a href="#top" class="flex items-center gap-2 font-display font-bold text-lg">
          <div class="w-7 h-7 bg-brand rounded-md"></div>
          Baobab
        </a>
        <div class="hidden md:flex items-center gap-8">
          <a href="#roles" class="text-sm font-medium text-slate hover:text-ink transition-colors">Rôles</a>
          <a href="#fonctionnalites" class="text-sm font-medium text-slate hover:text-ink transition-colors">Fonctionnalités</a>
          <a href="#comment-ca-marche" class="text-sm font-medium text-slate hover:text-ink transition-colors">Comment ça marche</a>
          <a href="#cta" class="text-sm font-medium text-slate hover:text-ink transition-colors">Commencer</a>
          <a href="#assistant" class="text-sm font-medium text-slate hover:text-ink transition-colors">Assistant IA</a>
        </div>
        <div class="flex items-center gap-4">
          <NuxtLink to="/login" class="text-sm font-medium text-slate hover:text-ink">Connexion</NuxtLink>
          <NuxtLink to="/register" class="text-sm font-medium text-white bg-ink px-5 py-2.5 rounded-lg hover:bg-brand transition-colors">
            Créer une entreprise
          </NuxtLink>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section id="top" class="max-w-6xl mx-auto px-6 pt-16 pb-20">
      <div class="grid lg:grid-cols-2 gap-14 items-center">
        <div>
          <h1 class="font-display font-extrabold text-5xl leading-[1.1] mb-6">
            Une seule plateforme.<br />
            <span class="text-brand">Trois rôles.</span> Zéro friction.
          </h1>
          <p class="text-slate text-lg mb-10 leading-relaxed">
            Administrateurs, chefs de projet et employés travaillent enfin dans le même espace —
            chacun voit exactement ce dont il a besoin, rien de plus.
          </p>
          <div class="flex items-center gap-4">
            <NuxtLink to="/register" class="bg-brand text-white px-7 py-3.5 rounded-lg font-medium hover:bg-ink transition-colors">
              Créer mon entreprise
            </NuxtLink>
            <NuxtLink to="/login" class="border border-border px-7 py-3.5 rounded-lg font-medium hover:border-ink transition-colors">
              J'ai déjà un compte
            </NuxtLink>
          </div>
        </div>

        <div class="relative">
          <div class="absolute -top-6 -right-6 w-32 h-32 bg-accent-light rounded-full -z-10"></div>
          <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-brand-light rounded-full -z-10"></div>
          <div class="rounded-2xl overflow-hidden border border-border shadow-sm">
            <img
              src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=900&q=80"
              alt="Équipe collaborant sur un projet"
              class="w-full h-[420px] object-cover"
            />
          </div>
          <div class="absolute -bottom-6 -left-6 bg-white border border-border rounded-xl px-5 py-4 shadow-md flex items-center gap-3">
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

    <!-- Stats bar -->
    <section class="border-y border-border bg-white">
      <div class="max-w-5xl mx-auto px-6 py-10 grid grid-cols-3 gap-6 text-center">
        <div v-for="s in stats" :key="s.label">
          <p class="font-display font-extrabold text-3xl text-brand">{{ s.value }}</p>
          <p class="text-slate text-sm mt-1">{{ s.label }}</p>
        </div>
      </div>
    </section>

    <!-- Logos défilants -->
    <section class="py-14 overflow-hidden">
      <p class="text-center text-xs font-mono text-slate uppercase tracking-wider mb-8">
        Inspiré des meilleurs outils du marché
      </p>
      <div class="relative flex overflow-hidden mask-fade">
        <div class="flex gap-12 animate-marquee shrink-0 pr-12">
          <div v-for="tool in [...tools, ...tools]" :key="tool.name + Math.random()" class="flex items-center gap-2.5 text-slate whitespace-nowrap">
            <UIcon :name="tool.icon" class="w-5 h-5" />
            <span class="font-medium text-sm">{{ tool.name }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Roles -->
    <section id="roles" class="max-w-6xl mx-auto px-6 py-24">
      <div class="text-center mb-12">
        <h2 class="font-display font-bold text-3xl">Un accès pour chaque responsabilité</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-5">
        <div v-for="role in roles" :key="role.name" class="bg-white border border-border rounded-2xl p-7 relative overflow-hidden">
          <div :class="[
            'absolute top-0 left-0 w-full h-1',
            role.color === 'brand' ? 'bg-brand' : role.color === 'accent' ? 'bg-accent' : 'bg-success'
          ]"></div>
          <h3 class="font-display font-bold text-xl mb-2">{{ role.name }}</h3>
          <p class="text-slate text-sm leading-relaxed">{{ role.desc }}</p>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="fonctionnalites" class="bg-white border-y border-border py-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
          <h2 class="font-display font-bold text-3xl">Tout ce qu'il faut, rien de superflu</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-x-10 gap-y-12">
          <div v-for="f in features" :key="f.title">
            <div class="w-11 h-11 rounded-lg bg-brand-light flex items-center justify-center mb-4">
              <UIcon :name="f.icon" class="w-5 h-5 text-brand" />
            </div>
            <h4 class="font-display font-semibold mb-1.5">{{ f.title }}</h4>
            <p class="text-slate text-sm leading-relaxed">{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section id="comment-ca-marche" class="max-w-5xl mx-auto px-6 py-24">
      <div class="text-center mb-14">
        <p class="font-mono text-xs text-brand uppercase tracking-wider mb-3">Démarrage</p>
        <h2 class="font-display font-bold text-3xl">Trois étapes, pas plus</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <div v-for="step in steps" :key="step.n" class="relative">
          <p class="font-display font-extrabold text-5xl text-brand-light mb-3">{{ step.n }}</p>
          <h4 class="font-display font-semibold text-lg mb-2">{{ step.title }}</h4>
          <p class="text-slate text-sm leading-relaxed">{{ step.desc }}</p>
        </div>
      </div>
    </section>

    <!-- Assistant IA -->
    <section id="assistant" class="max-w-6xl mx-auto px-6 py-24">
      <div class="grid lg:grid-cols-2 gap-14 items-center">
        <div class="order-2 lg:order-1 relative">
          <div class="absolute -top-6 -left-6 w-28 h-28 bg-brand-light rounded-full -z-10"></div>
          <div class="bg-ink rounded-2xl p-7 text-white">
            <div class="flex items-center gap-2 mb-5">
              <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center">
                <UIcon name="i-lucide-sparkles" class="w-4 h-4 text-white" />
              </div>
              <span class="font-display font-semibold text-sm">Assistant Baobab</span>
            </div>
            <div class="space-y-3">
              <div class="bg-white/10 rounded-xl rounded-tl-sm px-4 py-2.5 text-sm max-w-[85%]">
                Résume l'avancement du projet "Site vitrine" cette semaine.
              </div>
              <div class="bg-brand rounded-xl rounded-tr-sm px-4 py-2.5 text-sm max-w-[90%] ml-auto">
                3 tâches terminées sur 5. La maquette est validée, il reste l'intégration et les tests. Aucun retard signalé.
              </div>
            </div>
          </div>
        </div>

        <div class="order-1 lg:order-2">
          <p class="font-mono text-xs text-accent uppercase tracking-wider mb-3">Nouveau</p>
          <h2 class="font-display font-bold text-3xl mb-5">Un assistant qui connaît vos projets</h2>
          <p class="text-slate leading-relaxed mb-6">
            Posez une question en langage naturel sur l'avancement d'un projet, l'état des tâches d'une équipe,
            ou les documents récemment modifiés. L'assistant répond avec les vraies données de votre entreprise —
            pas de recherche manuelle dans des dizaines d'écrans.
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
            <li class="flex items-center gap-2.5 text-sm">
              <UIcon name="i-lucide-check" class="w-4 h-4 text-success flex-shrink-0" />
              Toujours basé sur vos données réelles
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section id="cta" class="bg-ink py-20 text-center relative overflow-hidden">
      <div class="absolute -top-16 -left-16 w-64 h-64 bg-brand/20 rounded-full"></div>
      <div class="absolute -bottom-20 -right-16 w-72 h-72 bg-accent/10 rounded-full"></div>
      <div class="relative z-10">
        <h2 class="font-display font-bold text-3xl text-white mb-4">Prêt à organiser votre entreprise ?</h2>
        <p class="text-white/60 mb-8">La création de votre espace prend moins de deux minutes.</p>
        <NuxtLink to="/register" class="inline-block bg-white text-ink px-7 py-3.5 rounded-lg font-medium hover:bg-brand hover:text-white transition-colors">
          Commencer maintenant
        </NuxtLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="max-w-6xl mx-auto px-6 py-14">
      <div class="grid md:grid-cols-4 gap-10 mb-10">
        <div>
          <div class="flex items-center gap-2 font-display font-bold mb-3">
            <div class="w-6 h-6 bg-brand rounded-md"></div>
            Baobab
          </div>
          <p class="text-slate text-sm leading-relaxed">La plateforme de gestion collaborative pensée pour les entreprises togolaises.</p>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Produit</p>
          <div class="flex flex-col gap-2 text-sm text-slate">
            <a href="#roles" class="hover:text-ink">Rôles</a>
            <a href="#fonctionnalites" class="hover:text-ink">Fonctionnalités</a>
            <a href="#comment-ca-marche" class="hover:text-ink">Comment ça marche</a>
          </div>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Compte</p>
          <div class="flex flex-col gap-2 text-sm text-slate">
            <NuxtLink to="/login" class="hover:text-ink">Connexion</NuxtLink>
            <NuxtLink to="/register" class="hover:text-ink">Créer une entreprise</NuxtLink>
          </div>
        </div>
        <div>
          <p class="font-display font-semibold text-sm mb-3">Contact</p>
          <p class="text-slate text-sm">Lomé, Togo</p>
        </div>
      </div>
      <div class="border-t border-border pt-6 text-center text-slate text-xs">
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