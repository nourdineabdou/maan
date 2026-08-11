# Ensemble pour la République

Plateforme Laravel de gestion d'adhésion des membres : inscription, parcours d'adhésion en
plusieurs étapes, validation administrative, matricule, carte de membre avec QR code,
notifications, statistiques et exports. Application bilingue français / arabe (RTL).

> **État du projet** : les fondations techniques et l'authentification/layout de base sont en
> place (voir « État d'avancement » ci-dessous). Le parcours d'adhésion, la validation admin, la
> carte de membre, les notifications et les statistiques sont prévus dans les phases suivantes.

## Prérequis

- PHP ^8.3 avec les extensions habituelles de Laravel (pdo_mysql, mbstring, gd, zip...)
- Composer
- MySQL 8 (via Laragon en local)
- Node.js + npm

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

## Configuration MySQL

Créer une base de données (utf8mb4) :

```sql
CREATE DATABASE ensemble_republique CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Puis renseigner dans `.env` :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ensemble_republique
DB_USERNAME=root
DB_PASSWORD=
```

## Migrations et seeders

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Les seeders créent :
- les rôles Spatie `administrateur` et `membre`, avec les permissions du cahier des charges ;
- les 15 wilayas de Mauritanie (fr/ar) et leurs moughataas (meilleur effort, à faire relire) ;
- les 7 catégories de membre et les 16 problématiques (fr/ar) ;
- un compte administrateur et un compte membre de démonstration.

## Identifiants de test (développement uniquement)

Définis via `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`), avec ces valeurs par défaut :

| Rôle           | E-mail                              | Téléphone      | Mot de passe |
|----------------|--------------------------------------|----------------|--------------|
| Administrateur | admin@ensemble-republique.test       | 22200000000    | password     |
| Membre         | membre@ensemble-republique.test      | 22212345678    | password     |

⚠️ Changez impérativement ces identifiants avant tout déploiement en production.

## Lancement local

```bash
npm install
npm run build      # ou npm run dev pendant le développement
php artisan optimize:clear
php artisan serve
```

La connexion se fait avec l'e-mail **ou** le numéro de téléphone. Après connexion, l'utilisateur
est redirigé automatiquement vers le tableau de bord correspondant à son rôle.

## Multilingue et RTL

- Fichiers de traduction dans `lang/fr/` et `lang/ar/` (`auth.php`, `dashboard.php`, `members.php`,
  `forms.php`, `messages.php`, `validation.php`, `passwords.php`, `pagination.php`).
- La langue active est stockée en session pour les visiteurs, et dans `users.preferred_locale`
  pour les utilisateurs connectés (mise à jour via `/lang/{fr|ar}`).
- Le sens RTL est appliqué automatiquement (`<html dir="rtl">`) via le middleware
  `App\Http\Middleware\SetLocale`, qui partage la variable `$isRtl` à toutes les vues.
- Les contenus de référentiel (régions, catégories, problématiques...) sont stockés en JSON
  bilingue `{"fr": "...", "ar": "..."}` et lus via le trait `App\Models\Concerns\HasTranslatedAttributes`.

## Logos et identité visuelle

- `public/logo_fr.png` et `public/logo_ar.png` sont affichés automatiquement selon la langue active.
- La palette de couleurs (extraite des logos — vert, or, rouge du drapeau mauritanien) est
  centralisée dans `resources/css/app.css` (bloc `@theme` : `--color-primary`, `--color-secondary`,
  `--color-accent`, etc.), et génère automatiquement les utilitaires Tailwind correspondants
  (`bg-primary`, `text-accent`...).

## Stack frontend

Tailwind CSS v4 (choix du projet, à la place de Bootstrap 5) + Bootstrap Icons, jQuery,
DataTables, Select2, SweetAlert2 et Chart.js pour les futures listes, filtres, formulaires et
graphiques.

## Carte de membre et QR code

- Attribution du matricule (`ER-{année}-{6 chiffres}`) via `App\Services\MatriculeGeneratorService`,
  incrément atomique en base pour éviter tout doublon en cas de validations simultanées.
- `App\Services\MembershipApprovalService` valide une adhésion (statut, matricule, token QR,
  activation de la carte) dans une transaction.
- Carte affichable dans l'app (`/card`), téléchargeable en PDF (`/card/pdf`, via dompdf), et
  imprimable par un administrateur autorisé (`/admin/members/{id}/card`, permission `cards.print`).
- Le QR code pointe vers une page publique de vérification (`/membership/verify/{token}`) qui
  n'expose que des informations non sensibles (nom, matricule, date d'adhésion, statut de la
  carte) — jamais le NNI, les documents ou l'adresse complète.

## Rôles et permissions

Basé sur Spatie Laravel Permission. Le rôle `administrateur` reçoit l'ensemble des permissions
métier (gestion des membres, validation des adhésions, documents, exports, statistiques,
régions/catégories/problématiques, utilisateurs, rôles, paramètres). Le rôle `membre` n'a pas de
permission Spatie : son accès est limité à ses propres données par ownership.

## Stockage des documents

Les fichiers uploadés (photos, pièces d'identité, justificatifs) seront stockés sur le disque
`public` de Laravel (`storage/app/public`, lié via `php artisan storage:link`) avec un accès
contrôlé par route protégée pour les documents sensibles (à mettre en place avec le parcours
d'adhésion).

## État d'avancement

**Fait :**
- Schéma de base de données complet (adhésion, documents, catégories, problématiques, régions/
  moughataas/communes, notifications, campagnes de messages).
- Authentification unique (e-mail ou téléphone), inscription avec création automatique du rôle et
  du profil membre, limitation des tentatives de connexion.
- Layout complet (sidebar responsive/offcanvas, header, footer, navigation mobile), RTL complet,
  sélecteur de langue.
- Tableaux de bord membre et administrateur (avec statistiques réelles pour l'admin), statistiques
  publiques sur la page de connexion.
- Fichiers de traduction fr/ar.
- Matricule automatique transactionnel, carte de membre (web + PDF), QR code opérationnel et page
  de vérification publique.

**À venir (phases suivantes) :**
- Parcours d'adhésion en plusieurs étapes (informations personnelles, géographiques,
  professionnelles, problématiques, documents, récapitulatif).
- Écran de validation administrative des dossiers (liste, filtres, actions d'approbation/rejet —
  les services `MembershipApprovalService`/`MatriculeGeneratorService` existent déjà).
- Notifications internes et campagnes de messages.
- Statistiques avancées avec graphiques, exports Excel/PDF filtrés.
- Tests automatisés, policies fines, documentation de déploiement.
