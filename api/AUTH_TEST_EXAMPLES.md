# 🧪 Exemples de tests d'authentification MoloLo+

## 📋 Tests de connexion

### **Test 1 : Connexion réussie**
```bash
curl -X POST http://localhost/api/connexion \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "identifiant=john.doe@example.com&password=motdepasse123"
```

**Réponse attendue (200) :**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "refresh_token": "a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"
}
```

### **Test 2 : Email incorrect**
```bash
curl -X POST http://localhost/api/connexion \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "identifiant=email.inexistant@example.com&password=motdepasse123"
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

### **Test 3 : Mot de passe incorrect**
```bash
curl -X POST http://localhost/api/connexion \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "identifiant=john.doe@example.com&password=mauvais_mot_de_passe"
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

### **Test 4 : Identifiant manquant**
```bash
curl -X POST http://localhost/api/connexion \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "password=motdepasse123"
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

### **Test 5 : Mot de passe manquant**
```bash
curl -X POST http://localhost/api/connexion \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "identifiant=john.doe@example.com"
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

### **Test 6 : Méthode HTTP incorrecte**
```bash
curl -X GET http://localhost/api/connexion
```

**Réponse attendue (405) :**
```json
{
  "error": "Méthode non autorisée"
}
```

---

## 🔄 Tests de renouvellement de token

### **Test 1 : Renouvellement réussi**
```bash
curl -X POST http://localhost/api/refresh-token \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"}'
```

**Réponse attendue (200) :**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "refresh_token": "x9y8z7w6v5u4nouveau_refresh_token..."
}
```

### **Test 2 : Refresh token invalide**
```bash
curl -X POST http://localhost/api/refresh-token \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"token_invalide"}'
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

### **Test 3 : Refresh token manquant**
```bash
curl -X POST http://localhost/api/refresh-token \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Réponse attendue (401) :**
```json
{
  "error": "Unauthorized"
}
```

---

## 🚪 Tests de déconnexion

### **Test 1 : Déconnexion réussie**
```bash
curl -X POST http://localhost/api/deconnexion \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"}'
```

**Réponse attendue (200) :**
```json
{
  "ok": true
}
```

### **Test 2 : Déconnexion sans token**
```bash
curl -X POST http://localhost/api/deconnexion \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Réponse attendue (200) :**
```json
{
  "ok": true
}
```

---

## 🔑 Tests d'oubli de mot de passe

### **Phase 1 : Demande de réinitialisation**

#### **Test 1 : Demande avec email valide**
```bash
curl -X POST http://localhost/api/password/forgot/request \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"john.doe@example.com"}'
```

**Réponse attendue (200) :**
```json
{
  "ok": true
}
```

#### **Test 2 : Demande avec email inexistant**
```bash
curl -X POST http://localhost/api/password/forgot/request \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"email.inexistant@example.com"}'
```

**Réponse attendue (200) :**
```json
{
  "ok": true
}
```

### **Phase 2 : Vérification OTP**

#### **Test 1 : Code OTP correct**
```bash
curl -X POST http://localhost/api/password/forgot/verify \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"john.doe@example.com","otp_code":"123456"}'
```

**Réponse attendue (200) :**
```json
{
  "valid": true
}
```

#### **Test 2 : Code OTP incorrect**
```bash
curl -X POST http://localhost/api/password/forgot/verify \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"john.doe@example.com","otp_code":"000000"}'
```

**Réponse attendue (200) :**
```json
{
  "valid": false
}
```

### **Phase 3 : Réinitialisation**

#### **Test 1 : Réinitialisation réussie**
```bash
curl -X POST http://localhost/api/password/reset \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"john.doe@example.com","otp_code":"123456","new_password":"nouveaumotdepasse123"}'
```

**Réponse attendue (200) :**
```json
{
  "ok": true
}
```

#### **Test 2 : Mot de passe trop faible**
```bash
curl -X POST http://localhost/api/password/reset \
  -H "Content-Type: application/json" \
  -d '{"identifiant":"john.doe@example.com","otp_code":"123456","new_password":"123"}'
```

**Réponse attendue (400) :**
```json
{
  "ok": false,
  "error": "password_weak"
}
```

---

## 🔒 Tests de requêtes authentifiées

### **Test 1 : Requête avec token valide**
```bash
curl -X GET http://localhost/api/profile \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**Réponse attendue (200) :**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "ville": "Paris",
    "bio_courte": "Artiste électronique"
  }
}
```

### **Test 2 : Requête sans token**
```bash
curl -X GET http://localhost/api/profile
```

**Réponse attendue (401) :**
```json
{
  "success": false,
  "error": "Missing bearer token"
}
```

### **Test 3 : Requête avec token invalide**
```bash
curl -X GET http://localhost/api/profile \
  -H "Authorization: Bearer token_invalide"
```

**Réponse attendue (401) :**
```json
{
  "success": false,
  "error": "Invalid or expired token"
}
```

---

## 📊 Tests avec le tester.php

### **Utilisation du tester intégré**

1. **Ouvrir** : `http://localhost/api/tester.php`
2. **Sélectionner** : "POST /connexion (JWT)" dans les préconfigurations
3. **Modifier** les identifiants si nécessaire
4. **Cliquer** : "Envoyer la requête"
5. **Vérifier** : Les tokens sont automatiquement stockés

### **Tests de scénarios complets**

#### **Scénario 1 : Connexion → Requête authentifiée → Déconnexion**
1. Connexion avec `POST /connexion`
2. Requête authentifiée avec `GET /profile` (cocher "Utiliser l'authentification JWT")
3. Déconnexion avec `POST /deconnexion`

#### **Scénario 2 : Oubli de mot de passe complet**
1. Demande avec `POST /password/forgot/request`
2. Vérification avec `POST /password/forgot/verify`
3. Réinitialisation avec `POST /password/reset`
4. Nouvelle connexion avec le nouveau mot de passe

#### **Scénario 3 : Renouvellement de token**
1. Connexion avec `POST /connexion`
2. Renouvellement avec `POST /refresh-token`
3. Vérification que les nouveaux tokens fonctionnent

---

## 🐛 Dépannage

### **Problèmes courants**

#### **Erreur 401 sur toutes les requêtes**
- Vérifier que le token JWT est correct
- Vérifier que le token n'est pas expiré
- Essayer de renouveler le token

#### **Erreur "Missing bearer token"**
- Vérifier l'en-tête Authorization
- Format : `Authorization: Bearer <token>`
- Pas d'espace après "Bearer"

#### **Erreur "Invalid or expired token"**
- Token expiré ou invalide
- Utiliser le refresh token pour obtenir un nouveau token
- Si refresh token invalide, se reconnecter

#### **OTP "valid: false"**
- Vérifier que l'OTP n'est pas expiré (10 minutes)
- Vérifier que l'OTP n'a pas été utilisé
- Vérifier que le nombre de tentatives < 5
- Regarder les logs serveur pour le code OTP

---

**🎵 MoloLo+ - Tests d'authentification complets !**
