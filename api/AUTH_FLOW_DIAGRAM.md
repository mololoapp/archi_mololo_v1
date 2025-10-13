# 🔄 Diagramme de flux d'authentification MoloLo+

## 1. Connexion normale

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /connexion ------>|                            |
  |   identifiant            |                            |
  |   password               |                            |
  |                          |-- normalize_identifiant()  |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- Utilisateur trouvé -----|
  |                          |-- password_verify()        |
  |                          |-- make_access_token()      |
  |                          |-- make_refresh_token()     |
  |                          |-- hash('sha256', refresh)  |
  |                          |-- INSERT jwt_refresh_tokens|
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   access_token           |                            |
  |   refresh_token          |                            |
```

## 2. Erreur de connexion (email incorrect)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /connexion ------>|                            |
  |   identifiant:           |                            |
  |   email.inexistant@...   |                            |
  |   password: xxx          |                            |
  |                          |-- normalize_identifiant()  |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- NULL (utilisateur -----|
  |                          |    non trouvé)             |
  |<-- 401 Unauthorized ----|                            |
  |   error: "Unauthorized"  |                            |
```

## 3. Erreur de connexion (mot de passe incorrect)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /connexion ------>|                            |
  |   identifiant:           |                            |
  |   john.doe@example.com   |                            |
  |   password: mauvais      |                            |
  |                          |-- normalize_identifiant()  |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- Utilisateur trouvé -----|
  |                          |-- password_verify()        |
  |                          |   (retourne FALSE)        |
  |                          |-- hash_equals() check      |
  |                          |   (ancien mot de passe)    |
  |<-- 401 Unauthorized ----|                            |
  |   error: "Unauthorized"  |                            |
```

## 4. Renouvellement de token

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /refresh-token -->|                            |
  |   refresh_token          |                            |
  |                          |-- hash('sha256', token)    |
  |                          |-- SELECT jwt_refresh_tokens|
  |                          |--------------------------->|
  |                          |<-- Token valide -----------|
  |                          |-- UPDATE revoked = 1       |
  |                          |--------------------------->|
  |                          |-- make_refresh_token()     |
  |                          |-- make_access_token()      |
  |                          |-- INSERT nouveau token     |
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   access_token (nouveau) |                            |
  |   refresh_token (nouveau)|                            |
```

## 5. Oubli de mot de passe - Phase 1 (Demande)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /password/ -------|                            |
  |   forgot/request         |                            |
  |   identifiant: email     |                            |
  |                          |-- normalize_identifiant()  |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- Utilisateur trouvé -----|
  |                          |-- random_otp_6()           |
  |                          |-- random_bytes(16)         |
  |                          |-- sha256_hex(otp + salt)   |
  |                          |-- INSERT password_resets   |
  |                          |--------------------------->|
  |                          |-- error_log("[OTP] code")  |
  |<-- 200 OK --------------|                            |
  |   ok: true               |                            |
```

## 6. Oubli de mot de passe - Phase 2 (Vérification OTP)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /password/ -------|                            |
  |   forgot/verify          |                            |
  |   identifiant: email     |                            |
  |   otp_code: 123456       |                            |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- Utilisateur trouvé -----|
  |                          |-- latest_active_reset()    |
  |                          |--------------------------->|
  |                          |<-- OTP actif --------------|
  |                          |-- sha256_hex(otp + salt)   |
  |                          |-- hash_equals() check      |
  |                          |-- increment_attempts()     |
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   valid: true/false      |                            |
```

## 7. Oubli de mot de passe - Phase 3 (Réinitialisation)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /password/reset ->|                            |
  |   identifiant: email     |                            |
  |   otp_code: 123456       |                            |
  |   new_password: xxx      |                            |
  |                          |-- is_strong_password()     |
  |                          |-- find_user_by_identifiant()|
  |                          |--------------------------->|
  |                          |<-- Utilisateur trouvé -----|
  |                          |-- latest_active_reset()    |
  |                          |--------------------------->|
  |                          |<-- OTP valide -------------|
  |                          |-- password_hash()          |
  |                          |-- UPDATE artiste.password  |
  |                          |--------------------------->|
  |                          |-- mark_consumed()          |
  |                          |--------------------------->|
  |                          |-- UPDATE jwt_refresh_tokens|
  |                          |   SET revoked = 1          |
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   ok: true               |                            |
```

## 8. Déconnexion

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- POST /deconnexion ---->|                            |
  |   refresh_token          |                            |
  |                          |-- hash('sha256', token)    |
  |                          |-- UPDATE jwt_refresh_tokens|
  |                          |   SET revoked = 1          |
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   ok: true               |                            |
```

## 9. Requête authentifiée

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- GET /profile --------->|                            |
  |   Authorization:         |                            |
  |   Bearer access_token    |                            |
  |                          |-- get_bearer_token()       |
  |                          |-- jwt_verify_hs256()       |
  |                          |-- require_jwt_auth()       |
  |                          |-- SELECT profile WHERE     |
  |                          |   user_id = ?              |
  |                          |--------------------------->|
  |<-- 200 OK --------------|                            |
  |   profile data           |                            |
```

## 10. Token expiré (requête authentifiée)

```
Client                    Serveur                    Base de données
  |                          |                            |
  |-- GET /profile --------->|                            |
  |   Authorization:         |                            |
  |   Bearer expired_token   |                            |
  |                          |-- jwt_verify_hs256()       |
  |                          |   (retourne NULL)          |
  |<-- 401 Unauthorized ----|                            |
  |   error: "Invalid or     |                            |
  |   expired token"         |                            |
  |                          |                            |
  |-- POST /refresh-token -->|                            |
  |   refresh_token          |                            |
  |                          |-- [Processus de refresh]   |
  |<-- 200 OK --------------|                            |
  |   new access_token       |                            |
  |   new refresh_token      |                            |
  |                          |                            |
  |-- GET /profile --------->|                            |
  |   Authorization:         |                            |
  |   Bearer new_token       |                            |
  |                          |-- [Processus normal]       |
  |<-- 200 OK --------------|                            |
  |   profile data           |                            |
```
