# LAB 7 — Tester une route protégée (401) et une validation (422)

## Objectif

Écrire des tests automatisés pour vérifier que l'API protège correctement les routes et valide les données envoyées.

## Tests réalisés

### Test 1 : Route protégée (401)

- Envoi d'une requête GET vers `/api/blueprints` sans authentification.
- Vérification que l'API retourne le code **401 Unauthorized**.

### Test 2 : Validation (422)

- Authentification d'un utilisateur avec Sanctum.
- Envoi d'une requête POST contenant des données invalides.
- Vérification que l'API retourne le code **422 Unprocessable Entity**.
- Vérification que les erreurs concernent les champs attendus.

## Résultat

Tous les tests passent avec succès.

```text
Tests: 5 passed (19 assertions)
Duration: 2.37s