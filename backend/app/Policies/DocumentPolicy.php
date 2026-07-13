<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
    {
        public function view(User $user, Document $document): bool
        {
            return $this->appartientAuProjet($user, $document);
        }

        public function delete(User $user, Document $document): bool
        {
            // Seul l'auteur du document (ou un membre avec un rôle admin) peut supprimer
            return $document->user_id === $user->id;
        }

        protected function appartientAuProjet(User $user, Document $document): bool
        {
            return $document->projet->membres()->where('user_id', $user->id)->exists();
        }
    }