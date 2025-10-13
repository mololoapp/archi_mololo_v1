# 🔐 Système d'Authentification MoloLo+ API

## 📋 Table des matières
1. [Vue d'ensemble](#-vue-densemble)
2. [Connexion normale](#-connexion-normale)
3. [Gestion des erreurs de connexion](#-gestion-des-erreurs-de-connexion)
4. [Renouvellement de token](#-renouvellement-de-token)
5. [Déconnexion](#-déconnexion)
6. [Oubli de mot de passe](#-oubli-de-mot-de-passe)
7. [Sécurité](#-sécurité)
8. [Exemples d'utilisation](#-exemples-dutilisation)
9. [Codes d'erreur](#-codes-derreur)

---

## 🎯 Vue d'ensemble

L'API MoloLo+ utilise un système d'authentification **JWT (JSON Web Tokens)** sécurisé avec les caractéristiques suivantes :

- ✅ **Authentification JWT uniquement** (plus de sessions PHP)
- ✅ **Access tokens** valides 60 jours
- ✅ **Refresh tokens** persistés en base avec rotation sécurisée
- ✅ **Support de transition** pour les anciens mots de passe
- ✅ **Réinitialisation de mot de passe** par OTP (email/SMS)

---

## 🚀 Connexion normale

### **Endpoint : `POST /connexion`**

#### **Requête**
```http
POST /api/connexion
Content-Type: application/x-www-form-urlencoded

identifiant=john.doe@example.com
password=motdepasse123
```

#### **Réponse de succès (200)**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlhdCI6MTY5ODc2NDAwMCwiZXhwIjoxNzA2NTQwMDAwLCJ2ZXIiOiJhYmNkZWYxMjM0In0.signature",
  "refresh_token": "a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"
}
```

#### **Processus de connexion**
1. **Normalisation** de l'identifiant (email en minuscules, téléphone sans espaces)
2. **Recherche utilisateur** par email ou numéro de téléphone
3. **Vérification du mot de passe** avec `password_verify()`
4. **Support de transition** : Re-hachage automatique des anciens mots de passe
5. **Génération** des tokens JWT
6. **Stockage sécurisé** du refresh token (haché SHA-256)

---

## ❌ Gestion des erreurs de connexion

### **Cas d'erreur 1 : Email/Identifiant incorrect**

#### **Requête**
```http
POST /api/connexion
Content-Type: application/x-www-form-urlencoded

identifiant=email.inexistant@example.com
password=motdepasse123
```

#### **Réponse d'erreur (401)**
```json
{
  "error": "Unauthorized"
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ L'identifiant est normalisé : `email.inexistant@example.com`
2. ✅ Recherche dans la table `artiste` par email
3. ❌ **Aucun utilisateur trouvé**
4. ❌ **Réponse immédiate** : `401 Unauthorized`
5. 🔒 **Aucun token généré**
6. 📝 **Log d'erreur** : Tentative de connexion avec identifiant inexistant

---

### **Cas d'erreur 2 : Mot de passe incorrect**

#### **Requête**
```http
POST /api/connexion
Content-Type: application/x-www-form-urlencoded

identifiant=john.doe@example.com
password=mauvais_mot_de_passe
```

#### **Réponse d'erreur (401)**
```json
{
  "error": "Unauthorized"
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ L'identifiant est normalisé : `john.doe@example.com`
2. ✅ **Utilisateur trouvé** dans la base de données
3. ✅ Tentative de vérification avec `password_verify()`
4. ❌ **Mot de passe incorrect**
5. ✅ Vérification de transition (ancien mot de passe en clair)
6. ❌ **Aucune correspondance trouvée**
7. ❌ **Réponse** : `401 Unauthorized`
8. 🔒 **Aucun token généré**
9. 📝 **Log d'erreur** : Tentative de connexion avec mot de passe incorrect

---

### **Cas d'erreur 3 : Identifiant manquant**

#### **Requête**
```http
POST /api/connexion
Content-Type: application/x-www-form-urlencoded

password=motdepasse123
```

#### **Réponse d'erreur (401)**
```json
{
  "error": "Unauthorized"
}
```

#### **Ce qui se passe côté serveur :**
1. ❌ **Identifiant vide** après normalisation
2. ❌ **Recherche utilisateur** : `null`
3. ❌ **Réponse immédiate** : `401 Unauthorized`
4. 🔒 **Aucun token généré**

---

### **Cas d'erreur 4 : Mot de passe manquant**

#### **Requête**
```http
POST /api/connexion
Content-Type: application/x-www-form-urlencoded

identifiant=john.doe@example.com
```

#### **Réponse d'erreur (401)**
```json
{
  "error": "Unauthorized"
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ L'identifiant est normalisé : `john.doe@example.com`
2. ✅ **Utilisateur trouvé** dans la base de données
3. ❌ **Mot de passe vide**
4. ❌ **Vérification échoue** (mot de passe vide)
5. ❌ **Réponse** : `401 Unauthorized`
6. 🔒 **Aucun token généré**

---

### **Cas d'erreur 5 : Méthode HTTP incorrecte**

#### **Requête**
```http
GET /api/connexion
```

#### **Réponse d'erreur (405)**
```json
{
  "error": "Méthode non autorisée"
}
```

---

## 🔄 Renouvellement de token

### **Endpoint : `POST /refresh-token`**

#### **Requête**
```http
POST /api/refresh-token
Content-Type: application/json

{
  "refresh_token": "a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"
}
```

#### **Réponse de succès (200)**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.nouveau_token...",
  "refresh_token": "x9y8z7w6v5u4nouveau_refresh_token..."
}
```

#### **Cas d'erreur : Refresh token invalide (401)**
```json
{
  "error": "Unauthorized"
}
```

---

## 🚪 Déconnexion

### **Endpoint : `POST /deconnexion`**

#### **Requête**
```http
POST /api/deconnexion
Content-Type: application/json

{
  "refresh_token": "a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"
}
```

#### **Réponse de succès (200)**
```json
{
  "ok": true
}
```

---

## 🔑 Oubli de mot de passe

### **Phase 1 : Demande de réinitialisation**

#### **Endpoint : `POST /password/forgot/request`**

#### **Requête**
```http
POST /api/password/forgot/request
Content-Type: application/json

{
  "identifiant": "john.doe@example.com"
}
```

#### **Réponse (200) - Toujours identique pour la sécurité**
```json
{
  "ok": true
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ **Normalisation** de l'identifiant
2. ✅ **Recherche utilisateur** par email/téléphone
3. ✅ **Génération OTP** à 6 chiffres (ex: `123456`)
4. ✅ **Salt aléatoire** de 16 bytes
5. ✅ **Hachage sécurisé** : `SHA256(OTP + salt)`
6. ✅ **Stockage** dans `password_resets` (expiration 10 min)
7. ✅ **Envoi simulé** (en production : email/SMS)
8. ✅ **Réponse générique** (même si utilisateur inexistant)

---

### **Phase 2 : Vérification OTP**

#### **Endpoint : `POST /password/forgot/verify`**

#### **Requête**
```http
POST /api/password/forgot/verify
Content-Type: application/json

{
  "identifiant": "john.doe@example.com",
  "otp_code": "123456"
}
```

#### **Réponse de succès (200)**
```json
{
  "valid": true
}
```

#### **Réponse d'erreur (200) - Code incorrect**
```json
{
  "valid": false
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ **Vérification utilisateur** existe
2. ✅ **Récupération OTP** actif (non expiré, non consommé)
3. ✅ **Vérification tentatives** < 5
4. ✅ **Calcul** : `SHA256(otp_code + salt)`
5. ✅ **Comparaison sécurisée** avec `hash_equals()`
6. ✅ **Incrémentation tentatives** si incorrect

---

### **Phase 3 : Réinitialisation**

#### **Endpoint : `POST /password/reset`**

#### **Requête**
```http
POST /api/password/reset
Content-Type: application/json

{
  "identifiant": "john.doe@example.com",
  "otp_code": "123456",
  "new_password": "nouveaumotdepasse123"
}
```

#### **Réponse de succès (200)**
```json
{
  "ok": true
}
```

#### **Réponse d'erreur (400) - Mot de passe faible**
```json
{
  "ok": false,
  "error": "password_weak"
}
```

#### **Ce qui se passe côté serveur :**
1. ✅ **Vérification mot de passe** fort (≥ 8 caractères)
2. ✅ **Vérification OTP** valide (même processus qu'avant)
3. ✅ **Hachage** nouveau mot de passe avec `password_hash()`
4. ✅ **Mise à jour** base de données
5. ✅ **Marquage OTP** comme consommé
6. ✅ **Révocation** tous les refresh tokens existants

---

## 🛡️ Sécurité

### **Protection des mots de passe**
- 🔒 **Hachage** : `password_hash()` avec `PASSWORD_BCRYPT`
- 🔒 **Vérification** : `password_verify()`
- 🔒 **Transition** : Support des anciens mots de passe en clair
- 🔒 **Force** : Minimum 8 caractères pour nouveaux mots de passe

### **Protection JWT**
- 🔒 **Secret** : Variable d'environnement `JWT_SECRET`
- 🔒 **Expiration** : Access token (60j), Refresh token (30j)
- 🔒 **Rotation** : Nouveau refresh token à chaque renouvellement
- 🔒 **Révocation** : Refresh tokens révocables

### **Protection OTP**
- 🔒 **Salt aléatoire** : 16 bytes par OTP
- 🔒 **Hachage** : SHA-256 du code + salt
- 🔒 **Expiration** : 10 minutes
- 🔒 **Tentatives** : Maximum 5 essais
- 🔒 **Usage unique** : OTP consommé après utilisation

### **Protection contre les attaques**
- 🛡️ **Timing attacks** : `hash_equals()` pour comparaisons
- 🛡️ **Brute force** : Limitation des tentatives OTP
- 🛡️ **Session hijacking** : Rotation des refresh tokens
- 🛡️ **Password reuse** : Révocation des tokens après reset

---

## 📝 Exemples d'utilisation

### **JavaScript - Connexion**
```javascript
async function login(email, password) {
    try {
        const response = await fetch('/api/connexion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                identifiant: email,
                password: password
            })
        });
        
        if (response.ok) {
            const data = await response.json();
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);
            return { success: true, data };
        } else {
            const error = await response.json();
            return { success: false, error: error.error };
        }
    } catch (error) {
        return { success: false, error: 'Erreur réseau' };
    }
}
```

### **JavaScript - Requête authentifiée**
```javascript
async function makeAuthenticatedRequest(url) {
    const accessToken = localStorage.getItem('access_token');
    
    try {
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json'
            }
        });
        
        if (response.status === 401) {
            // Token expiré, essayer de le renouveler
            const refreshed = await refreshToken();
            if (refreshed) {
                // Retry avec le nouveau token
                return makeAuthenticatedRequest(url);
            }
        }
        
        return await response.json();
    } catch (error) {
        console.error('Erreur requête authentifiée:', error);
    }
}
```

### **JavaScript - Renouvellement de token**
```javascript
async function refreshToken() {
    const refreshToken = localStorage.getItem('refresh_token');
    
    try {
        const response = await fetch('/api/refresh-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                refresh_token: refreshToken
            })
        });
        
        if (response.ok) {
            const data = await response.json();
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);
            return true;
        } else {
            // Refresh token invalide, rediriger vers login
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            window.location.href = '/login';
            return false;
        }
    } catch (error) {
        console.error('Erreur renouvellement token:', error);
        return false;
    }
}
```

---

## 📊 Codes d'erreur

| Code | Signification | Cas d'usage |
|------|---------------|-------------|
| 200 | OK | Connexion réussie, tokens générés |
| 400 | Bad Request | Données invalides, mot de passe faible |
| 401 | Unauthorized | Identifiants incorrects, token invalide |
| 405 | Method Not Allowed | Mauvaise méthode HTTP |
| 500 | Internal Server Error | Erreur serveur |

---

## 🔧 Configuration

### **Variables d'environnement**
```bash
# Secret pour la signature JWT (OBLIGATOIRE en production)
JWT_SECRET=your-super-secret-key-here

# Configuration base de données
DB_HOST=localhost
DB_NAME=mololo
DB_USER=root
DB_PASS=
```

### **Structure de base de données requise**
- `artiste` : Table des utilisateurs
- `jwt_refresh_tokens` : Stockage des refresh tokens
- `password_resets` : Gestion des réinitialisations de mot de passe

---

## 🚨 Bonnes pratiques

### **Côté client**
- ✅ Stocker les tokens de manière sécurisée
- ✅ Implémenter le renouvellement automatique
- ✅ Gérer les erreurs 401 (token expiré)
- ✅ Ne jamais exposer les refresh tokens

### **Côté serveur**
- ✅ Utiliser HTTPS en production
- ✅ Configurer un JWT_SECRET fort
- ✅ Implémenter la rotation des refresh tokens
- ✅ Logger les tentatives de connexion échouées

---

**🎵 MoloLo+ - Système d'authentification sécurisé pour les artistes musicaux !**
