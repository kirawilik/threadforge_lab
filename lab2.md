# 📝 LAB 2 — Form Request + Status Codes + API Resource

## Objectif

Sécuriser la création d'un blueprint en utilisant une Form Request pour la validation, retourner le bon code HTTP (201 Created) lors de la création, et utiliser une API Resource afin de contrôler les données renvoyées au client.

---

## Étape 1 — Form Request

Commande utilisée :

```bash
php artisan make:request StoreBlueprintRequest
```

Fichier : `app/Http/Requests/StoreBlueprintRequest.php`

```php
public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'nom'            => ['required', 'string', 'max:100'],
        'ton'            => ['required', 'string', 'max:255'],
        'max_hashtags'   => ['required', 'integer', 'min:0', 'max:10'],
        'max_caracteres' => ['required', 'integer', 'min:50', 'max:280'],
    ];
}
```

---

## Étape 2 — API Resource

Commande utilisée :

```bash
php artisan make:resource BlueprintResource
```

Fichier : `app/Http/Resources/BlueprintResource.php`

```php
public function toArray($request): array
{
    return [
        'id'             => $this->id,
        'nom'            => $this->nom,
        'ton'            => $this->ton,
        'max_hashtags'   => $this->max_hashtags,
        'max_caracteres' => $this->max_caracteres,
    ];
}
```

---

## Étape 3 — Route POST

Dans `routes/api.php`

```php
Route::post('/blueprints', [BlueprintController::class, 'store']);
```

---

## Étape 4 — Méthode store()

Dans `BlueprintController.php`

```php
public function store(StoreBlueprintRequest $request)
{
    $blueprint = auth()->user()->blueprints()->create(
        $request->validated()
    );

    return (new BlueprintResource($blueprint))
        ->response()
        ->setStatusCode(201);
}
```

---

## Étape 5 — Mise à jour de index() et show()

### index()

```php
public function index()
{
    return BlueprintResource::collection(
        auth()->user()->blueprints()->latest()->get()
    );
}
```

### show()

```php
public function show(Blueprint $blueprint)
{
    return new BlueprintResource($blueprint);
}
```

---

# Tests Postman

## ✅ Test 1 — POST valide

**Requête**

```
POST /api/blueprints
```

Body

```json
{
    "nom": "Instagram Marketing",
    "ton": "Professionnel",
    "max_hashtags": 5,
    "max_caracteres": 250
}
```

**Résultat**

- Status : **201 Created**
- Réponse :

```json
{
    "data": {
        "id": 1,
        "nom": "Instagram Marketing",
        "ton": "Professionnel",
        "max_hashtags": 5,
        "max_caracteres": 250
    }
}
```

📷 **Capture d'écran à insérer ici**

---

## ✅ Test 2 — POST invalide

Body

```json
{
    "nom": "",
    "ton": "Professionnel",
    "max_hashtags": 5,
    "max_caracteres": 250
}
```

**Résultat**

- Status : **422 Unprocessable Entity**

Réponse

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "nom": [
            "The nom field is required."
        ]
    }
}
```

📷 **Capture d'écran à insérer ici**

---

## ✅ Test 3 — GET /api/blueprints

Réponse

```json
{
    "data": [
        {
            "id": 1,
            "nom": "Instagram Marketing",
            "ton": "Professionnel",
            "max_hashtags": 5,
            "max_caracteres": 250
        }
    ]
}
```

Vérification :

- ✅ `user_id` absent
- ✅ `created_at` absent
- ✅ `updated_at` absent

---

# Réponse à la question

Sans utiliser une **API Resource**, un appel comme :

```php
Blueprint::all();
```

aurait exposé des champs internes du modèle, notamment **user_id**, **created_at** et **updated_at**. Grâce à `BlueprintResource`, seuls les champs explicitement définis (`id`, `nom`, `ton`, `max_hashtags`, `max_caracteres`) sont renvoyés dans la réponse JSON.

---

# Conclusion

Le LAB 2 est fonctionnel :

- ✅ Validation via `StoreBlueprintRequest`
- ✅ Retour automatique des erreurs en **422**
- ✅ Création avec le code **201 Created**
- ✅ Utilisation de `BlueprintResource`
- ✅ Aucune donnée interne exposée dans les réponses JSON