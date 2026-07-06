# LAB 4 — Déplacer la génération dans un Job (Queue)

## Objectif

L'objectif de ce laboratoire est de déplacer la génération du contenu par l'IA hors de la requête HTTP afin d'améliorer le temps de réponse de l'API.

Au lieu d'appeler directement Groq dans le contrôleur, la génération est exécutée par un **Job** placé dans une **Queue**.

---

# Environnement

- Laravel 12
- Laravel AI SDK
- Provider : Groq
- Queue Driver : Database

---

# Étape 1 — Configuration de la Queue

Dans le fichier `.env` :

```env
QUEUE_CONNECTION=database
```

Puis exécution des migrations :

```bash
php artisan migrate
```

Laravel crée les tables nécessaires au fonctionnement des Jobs.

---

# Étape 2 — Création du Job

Création du Job :

```bash
php artisan make:job GeneratePostJob
```

Le Job implémente :

```php
ShouldQueue
```

afin que son exécution soit réalisée en arrière-plan.

---

# Étape 3 — Déplacement de la logique IA

La logique qui se trouvait dans :

```php
ContentController::repurpose()
```

a été déplacée dans :

```php
GeneratePostJob::handle()
```

Le Job réalise les opérations suivantes :

- récupération du contenu brut ;
- appel de l'agent PostGenerator ;
- génération du contenu via Groq ;
- création du Post ;
- mise à jour du statut.

---

# Étape 4 — Dispatch du Job

Depuis le contrôleur, l'appel direct à l'IA est remplacé par :

```php
GeneratePostJob::dispatch($post);
```

Le contrôleur retourne immédiatement une réponse HTTP.

Le traitement est effectué plus tard par le Worker.

---

# Étape 5 — Test du cycle de vie

Avant le lancement du Worker :

- une ligne apparaît dans la table `jobs` ;
- aucun post n'est encore généré.

Le Job est simplement placé dans la file d'attente.

---

# Étape 6 — Exécution du Worker

Lancement du Worker :

```bash
php artisan queue:work
```

Le Worker :

- récupère le Job ;
- appelle Groq ;
- génère le contenu ;
- sauvegarde les résultats dans la base de données ;
- supprime le Job de la table `jobs`.

---

# Fonctionnement de la Queue

```
Client
   │
   ▼
POST /api/content/repurpose
   │
   ▼
ContentController
   │
   ▼
dispatch(GeneratePostJob)
   │
   ▼
Table jobs
   │
   ▼
queue:work
   │
   ▼
GeneratePostJob
   │
   ▼
PostGenerator
   │
   ▼
Groq API
   │
   ▼
Base de données
```

---

# Avantages

- réponse HTTP beaucoup plus rapide ;
- aucune attente pendant la génération IA ;
- meilleure expérience utilisateur ;
- possibilité de traiter plusieurs générations en parallèle.

---

# Difficultés rencontrées

Lors de la configuration, il faut vérifier :

- `QUEUE_CONNECTION=database`
- exécuter :

```bash
php artisan optimize:clear
```

après toute modification du `.env`.

En cas de modification du Job, il faut redémarrer le Worker :

```bash
php artisan queue:restart
```

ou

```bash
php artisan queue:work
```

---

# Résultat

✔ Queue configurée

✔ Job créé

✔ Dispatch fonctionnel

✔ Worker opérationnel

✔ Génération IA exécutée en arrière-plan

✔ Amélioration des performances de l'API

---

# Conclusion

Le LAB 4 introduit le système de Queues de Laravel afin d'exécuter les tâches longues en arrière-plan.

La génération IA n'est plus réalisée pendant la requête HTTP, ce qui réduit fortement le temps de réponse de l'API et améliore la scalabilité de l'application.

Ce mécanisme constitue la base des traitements asynchrones dans ThreadForge.