# LAB 3 — L'appel IA en synchrone (Structured Output + Casts)

## Objectif

L'objectif de ce laboratoire est d'intégrer une Intelligence Artificielle dans l'API ThreadForge afin de générer automatiquement un post technique à partir d'un contenu brut.

L'appel à l'IA est réalisé de manière **synchrone**, c'est-à-dire que la requête HTTP attend la réponse du modèle avant d'être renvoyée au client.

---

# Environnement

- Laravel 12
- Laravel AI SDK
- Provider : Groq
- API REST protégée par Sanctum
- Base de données MySQL

---

# Étape 1 — Création de l'Agent IA

Création de l'agent :

```bash
php artisan make:agent PostGenerator --structured
```

L'agent est chargé de transformer un contenu technique brut en publication optimisée pour X (Twitter).

---

# Étape 2 — Définition du rôle de l'agent

Le fichier :

```

app/Ai/Agents/PostGenerator.php

```

contient les instructions suivantes :

- produire un hook accrocheur ;
- produire une liste de points clés ;
- calculer un score de lisibilité technique ;
- proposer des hashtags ;
- expliquer pourquoi le ton respecte le blueprint.

Ces instructions sont définies dans :

```php
public function instructions(): string
```

---

# Étape 3 — Structured Output

Le contrat JSON est garanti grâce à la méthode :

```php
public function schema(JsonSchema $schema): array
```

Le schéma retourné est :

```php
[
    'hook_propose' => string,
    'body_points' => array<string>,
    'technical_readability_score' => integer,
    'suggested_hashtags' => array<string>,
    'tone_compliance_justification' => string,
]
```

L'utilisation du Structured Output garantit que l'IA renvoie toujours un JSON valide.

---

# Étape 4 — Casts et Enum

Le modèle Post utilise les Casts Laravel :

```php
protected function casts(): array
{
    return [

        'status' => PostStatus::class,

        'body_points' => 'array',

        'suggested_hashtags' => 'array',

    ];
}
```

L'Enum :

```

app/Enums/PostStatus.php

```

contient :

```php
enum PostStatus : string
{
    case Draft = 'draft';

    case Posted = 'posted';

    case Archived = 'archived';
}
```

Grâce aux Casts :

- body_points est automatiquement converti en tableau PHP.
- suggested_hashtags est automatiquement converti en tableau.
- status est converti automatiquement en Enum.

---

# Étape 5 — Génération du post

La méthode :

```php
ContentController::repurpose()
```

effectue les opérations suivantes :

1. validation des données reçues ;
2. appel de l'agent IA ;
3. récupération de la réponse structurée ;
4. création du Post dans la base de données ;
5. retour d'un PostResource.

L'appel IA est réalisé par :

```php
$response = (new PostGenerator)
    ->prompt($request->validated('raw_content'));
```

---

# Étape 6 — Configuration de Groq

Le provider par défaut a été modifié :

```php
config/ai.php
```

Avant :

```php
'default' => 'openai',
```

Après :

```php
'default' => 'groq',
```

Le fichier `.env` contient :

```env
GROQ_API_KEY=***************
```

---

# Difficulté rencontrée

Lors du premier test, Laravel AI utilisait OpenAI par défaut.

Erreur obtenue :

```
Incorrect API key provided: ''
```

Cause :

Le provider par défaut était encore :

```php
'default' => 'openai'
```

Solution :

- publier le fichier de configuration :

```bash
php artisan vendor:publish --tag=ai-config
```

- modifier :

```php
'default' => 'groq'
```

- vérifier la variable :

```env
GROQ_API_KEY
```

- vider le cache :

```bash
php artisan optimize:clear
```

Une seconde erreur est apparue :

```
Invalid API Key
```

La cause provenait d'une erreur dans la variable d'environnement (`GROQ_API_KEY`) qui a été corrigée.

Après correction, les appels vers Groq fonctionnent correctement.

---

# Test Postman

Requête :

```
POST /api/content/repurpose
```

Body :

```json
{
    "blueprint_id": 1,
    "raw_content": "Aujourd'hui j'ai appris Laravel AI SDK. Il permet de communiquer avec les modèles d'intelligence artificielle en utilisant des Agents et un Structured Output."
}
```

Réponse :

```json
{
    "data": {
        "id": 1,
        "hook_propose": "...",
        "body_points": [
            "...",
            "...",
            "..."
        ],
        "technical_readability_score": 78,
        "suggested_hashtags": [
            "#Laravel",
            "#AI"
        ],
        "tone_compliance_justification": "...",
        "status": "draft"
    }
}
```

Le post est correctement enregistré dans la base de données.

---

# Temps de réponse observé

Temps moyen observé :

- environ 10 à 15 secondes.

Cette durée est normale puisque l'appel à l'IA est effectué de manière synchrone.

Le client HTTP attend la réponse complète du modèle avant de recevoir la réponse de l'API.

Cette limitation sera corrigée dans le laboratoire suivant grâce aux Jobs et aux Queues.

---

# Résultat obtenu

✔ Agent IA fonctionnel

✔ Structured Output respecté

✔ Casts Laravel opérationnels

✔ Enum PostStatus utilisée

✔ Génération automatique du contenu

✔ Stockage des tableaux dans la base

✔ API REST fonctionnelle

✔ Provider Groq correctement configuré

---

# Conclusion

Ce laboratoire a permis d'intégrer Laravel AI SDK avec le provider Groq afin de générer automatiquement des publications techniques.

L'utilisation du Structured Output garantit un format JSON fiable tandis que les Casts Eloquent permettent de manipuler directement les tableaux et les Enums sans conversion manuelle.

Le principal inconvénient observé est le temps de réponse relativement élevé dû au fonctionnement synchrone de l'appel IA, ce qui justifie l'utilisation des Jobs et des Queues dans le laboratoire suivant.