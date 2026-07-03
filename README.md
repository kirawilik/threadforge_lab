# README.md — LAB 1 : API REST (ThreadForge)

## Objectif

Mettre en place le squelette REST de l'API **Blueprints** avec Laravel :

* Création d'un contrôleur API
* Déclaration des routes dans `routes/api.php`
* Réponses JSON
* Protection des routes avec `auth:sanctum`
* Test avec Postman

---

## Étapes réalisées

### 1. Installation de l'API

```bash
php artisan install:api
```

### 2. Création du contrôleur API

```bash
php artisan make:controller Api/BlueprintController --api
```

### 3. Configuration des routes

Dans `routes/api.php` :

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/blueprints', [BlueprintController::class, 'index']);
    Route::get('/blueprints/{blueprint}', [BlueprintController::class, 'show']);
});
```

### 4. Méthode `index`

```php
public function index()
{
    $blueprints = auth()->user()->blueprints()->latest()->get();

    return response()->json($blueprints);
}
```

### 5. Méthode `show`

```php
public function show(Blueprint $blueprint)
{
    return response()->json($blueprint);
}
```

---

## Création d'un utilisateur avec Tinker

Pour tester les routes protégées par Sanctum, j'ai créé un utilisateur avec Tinker.

```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);
```

---

## Génération d'un token Sanctum

Toujours dans Tinker :

```php
$token = $user->createToken('test')->plainTextToken;
```

Laravel affiche un token similaire à :

```text
1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

## Test avec Postman

Pour accéder aux routes protégées, j'ai ajouté les en-têtes suivants :

```
Authorization: Bearer MON_TOKEN
Accept: application/json
```

Tests réalisés :

### GET /api/blueprints

Résultat attendu :

* Status : **200 OK**
* Réponse JSON contenant la liste des blueprints.

### GET /api/blueprints/{id}

Résultat attendu :

* Status : **200 OK**
* Réponse JSON contenant le blueprint demandé.

---

## Résultat

Les routes API sont accessibles avec un token Sanctum valide.

Les réponses sont retournées au format JSON avec le code HTTP **200 OK**.

Le token généré avec Tinker m'a permis de tester correctement les routes protégées avant la mise en place complète du système d'authentification.
