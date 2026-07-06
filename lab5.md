# LAB 5 — Brancher le Job sur l'endpoint réel (réponse 202)

## Objectif

L'objectif de ce laboratoire est d'améliorer les performances de l'API en remplaçant la génération synchrone du post par une génération asynchrone à l'aide des **Jobs** et des **Queues** de Laravel.

Au lieu d'attendre que l'IA termine la génération avant de répondre au client, l'API enregistre simplement la demande, place un Job dans la file d'attente et retourne immédiatement une réponse **HTTP 202 Accepted**.

---

## Ce que j'ai réalisé

### 1. Simplification du contrôleur

J'ai modifié la méthode `repurpose()` du `ContentController`.

Au LAB 3, cette méthode :

- créait le post ;
- appelait directement `PostGenerator`;
- attendait la réponse de Groq ;
- enregistrait le résultat dans la base de données.

Dans ce laboratoire, j'ai supprimé l'appel direct à l'IA.

Le contrôleur crée uniquement le post puis envoie un Job dans la Queue grâce à :

```php
GeneratePostJob::dispatch($post);
```

---

### 2. Réponse immédiate

Après avoir placé le Job dans la file d'attente, le contrôleur retourne immédiatement :

```http
HTTP/1.1 202 Accepted
```

avec une réponse similaire à :

```json
{
    "message": "Post generation queued.",
    "post_id": 1
}
```

L'utilisateur n'a plus besoin d'attendre plusieurs secondes.

---

### 3. Traitement en arrière-plan

Le traitement est effectué par le Worker Laravel.

Après avoir lancé :

```bash
php artisan queue:work
```

le Worker :

- récupère le `GeneratePostJob` depuis la table `jobs`;
- appelle l'agent `PostGenerator`;
- envoie le contenu à Groq;
- récupère la réponse générée;
- met à jour le `Post` avec :
  - hook_propose
  - body_points
  - technical_readability_score
  - suggested_hashtags
  - tone_compliance_justification.

---

## Fonctionnement du flux

```
Client
   │
   ▼
POST /api/content/repurpose
   │
   ▼
ContentController
   │
   ├── Création du Post
   │
   ├── GeneratePostJob::dispatch($post)
   │
   ▼
HTTP 202 Accepted
   │
   ▼
Queue (Database)
   │
   ▼
Worker (queue:work)
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
Mise à jour du Post
```

---

## Tests réalisés

### Test 1

Lancement du Worker :

```bash
php artisan queue:work
```

Résultat :

Le Worker attend les nouveaux Jobs.

---

### Test 2

Envoi d'une requête POST depuis Postman.

Résultat :

Le serveur répond immédiatement avec :

```http
202 Accepted
```

---

### Test 3

Observation du Worker.

Résultat :

Le Job est traité automatiquement et la génération IA est exécutée.

---

### Test 4

Vérification dans la base de données.

Résultat :

Le Post contient maintenant :

- hook_propose
- body_points
- technical_readability_score
- suggested_hashtags
- tone_compliance_justification

générés par l'IA.

---

## Comparaison avec le LAB 3

| LAB 3 | LAB 5 |
|--------|--------|
| Génération dans le contrôleur | Génération dans un Job |
| Réponse après plusieurs secondes | Réponse immédiate |
| L'utilisateur attend l'IA | L'IA travaille en arrière-plan |
| HTTP 201 | HTTP 202 Accepted |

---

## Conclusion

Ce laboratoire m'a permis de mettre en place une architecture asynchrone avec Laravel.

Le contrôleur ne réalise plus le traitement IA directement. Il se contente de créer le post et de dispatcher un `GeneratePostJob`. Le Worker exécute ensuite la génération en arrière-plan, ce qui améliore considérablement les performances de l'application et l'expérience utilisateur.