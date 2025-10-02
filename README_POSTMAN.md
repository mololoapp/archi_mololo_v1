# 🧪 Guide de test API MoloLo+ avec Postman

## 📋 Table des matières
1. [Configuration initiale](#-configuration-initiale)
2. [Tests d'authentification](#-tests-dauthentification)
3. [Tests des endpoints publics](#-tests-des-endpoints-publics)
4. [Tests des endpoints protégés](#-tests-des-endpoints-protégés)
5. [Collection Postman](#-collection-postman)
6. [Variables d'environnement](#-variables-denvironnement)
7. [Scénarios de test complets](#-scénarios-de-test-complets)
8. [Dépannage](#-dépannage)

---

## 🚀 Configuration initiale

### 1. **Installation de Postman**
- Télécharger Postman : https://www.postman.com/downloads/
- Créer un compte (optionnel)
- Lancer l'application

### 2. **Configuration de base**
```
Base URL: http://localhost/api
ou
Base URL: http://localhost/archi_mololo_v1/api
```

### 3. **En-têtes par défaut**
Pour tous les tests, ajouter ces en-têtes :
```
Content-Type: application/x-www-form-urlencoded
Accept: application/json
```

---

## 🔐 Tests d'authentification

### 1. **Test d'inscription**

**Endpoint :** `POST /inscription`

**Headers :**
```
Content-Type: application/x-www-form-urlencoded
```

**Body (form-data ou x-www-form-urlencoded) :**
```
nom: John Doe
nom_artiste: DJ John
email: john.doe@example.com
numero: +33123456789
style_musique: Electronic
password: motdepasse123
```

**Réponse attendue (201) :**
```json
{
    "success": true,
    "message": "Compte créé avec succès",
    "user_id": 1
}
```

**Tests d'erreurs :**
- Email manquant → 400
- Email invalide → 400
- Mot de passe trop court → 400
- Email déjà utilisé → 400

### 2. **Test de connexion**

**Endpoint :** `POST /connexion`

**Headers :**
```
Content-Type: application/x-www-form-urlencoded
```

**Body :**
```
identifiant: john.doe@example.com
password: motdepasse123
```

**Réponse attendue (200) :**
```json
{
    "success": true,
    "message": "Connexion réussie",
    "user": {
        "id": 1,
        "nom": "John Doe",
        "nom_artiste": "DJ John",
        "email": "john.doe@example.com",
        "numero": "+33123456789",
        "style_musique": "Electronic",
        "date_inscription": "2024-10-02 10:30:00"
    }
}
```

**⚠️ Important :** Après la connexion, Postman sauvegarde automatiquement les cookies de session.

### 3. **Test de déconnexion**

**Endpoint :** `POST /deconnexion`

**Headers :**
```
Content-Type: application/json
```

**Réponse attendue (200) :**
```json
{
    "success": true,
    "message": "Déconnexion réussie"
}
```

---

## 🌐 Tests des endpoints publics

### 1. **Statut de l'API**

**Endpoint :** `GET /status`

**Headers :** Aucun requis

**Réponse attendue (200) :**
```json
{
    "success": true,
    "message": "API MoloLo+ opérationnelle",
    "version": "1.0",
    "endpoints": {
        "POST /inscription": "Création de compte artiste",
        "POST /connexion": "Connexion utilisateur",
        // ... liste complète
    }
}
```

### 2. **Liste des artistes**

**Endpoint :** `GET /artistes`

**Réponse attendue (200) :**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "John Doe",
            "nom_artiste": "DJ John",
            "email": "john.doe@example.com",
            "numero": "+33123456789",
            "style_musique": "Electronic",
            "date_inscription": "2024-10-02 10:30:00"
        }
    ]
}
```

### 3. **Détails d'un artiste**

**Endpoint :** `GET /artiste/1`

**Réponse attendue (200) :**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nom": "John Doe",
        "nom_artiste": "DJ John",
        "email": "john.doe@example.com",
        "numero": "+33123456789",
        "style_musique": "Electronic",
        "date_inscription": "2024-10-02 10:30:00"
    }
}
```

### 4. **Liste des opportunités**

**Endpoint :** `GET /opportunites`

**Réponse attendue (200) :**
```json
{
    "success": true,
    "data": []
}
```

---

## 🔒 Tests des endpoints protégés

**⚠️ Prérequis :** Être connecté (avoir effectué un `POST /connexion` avec succès)

### 1. **Profil utilisateur**

#### **Récupérer son profil**
**Endpoint :** `GET /profile`

**Réponse attendue (200 ou 404) :**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "photo_couverture": "",
        "photo_profile": "",
        "ville": "Paris",
        "bio_courte": "Artiste électronique",
        // ... autres champs
    }
}
```

#### **Créer/Modifier son profil**
**Endpoint :** `POST /profile` ou `PUT /profile`

**Headers :**
```
Content-Type: application/json
```

**Body (JSON) :**
```json
{
    "ville": "Paris",
    "bio_courte": "Artiste électronique passionné",
    "bio_detailles": "Plus de 10 ans d'expérience...",
    "instagram": "@djjohn",
    "facebook": "facebook.com/djjohn",
    "style_musique": "Electronic, House"
}
```

### 2. **EPK (Electronic Press Kit)**

#### **Créer un EPK**
**Endpoint :** `POST /epk`

**Headers :**
```
Content-Type: application/json
```

**Body (JSON) :**
```json
{
    "nom_artiste": "DJ John",
    "genre_musical": "Electronic",
    "localisation": "Paris, France",
    "biographie": "Artiste électronique depuis 2010...",
    "discographie": "Album 1: Future Sounds (2020), Single: Night Vibes (2024)",
    "contact": "john.doe@example.com"
}
```

### 3. **Booking**

#### **Consulter les bookings**
**Endpoint :** `GET /booking`

#### **Faire une demande de booking**
**Endpoint :** `POST /booking`

**Body (JSON) :**
```json
{
    "nom_utilisateur": "John Doe",
    "lieux": "Club XYZ",
    "adresse": "123 Rue de la Musique, Paris",
    "montant": "500€",
    "heure": "22:00:00",
    "date": "2024-12-15 22:00:00",
    "message": "Soirée électronique, 3h de set"
}
```

### 4. **Agenda**

#### **Voir l'agenda**
**Endpoint :** `GET /agenda`

#### **Ajouter un événement**
**Endpoint :** `POST /agenda`

**Body (JSON) :**
```json
{
    "nom_concert": "Electronic Night",
    "date": "2024-12-20 21:00:00",
    "heure": "21:00:00",
    "adresse": "Salle Pleyel, Paris",
    "description": "Concert électronique avec invités spéciaux",
    "montant": "50€",
    "nombre_personne": "500"
}
```

---

## 📦 Collection Postman

### Créer une collection
1. Cliquer sur "New" → "Collection"
2. Nommer : "MoloLo+ API Tests"
3. Ajouter une description

### Organiser les dossiers
```
MoloLo+ API Tests/
├── 🔐 Authentication/
│   ├── POST Inscription
│   ├── POST Connexion
│   └── POST Déconnexion
├── 🌐 Public Endpoints/
│   ├── GET Status
│   ├── GET Artistes
│   └── GET Artiste Details
├── 🔒 Protected Endpoints/
│   ├── Profile/
│   ├── EPK/
│   ├── Booking/
│   ├── Agenda/
│   ├── Galerie/
│   ├── Notifications/
│   └── SmartLinks/
└── 🧪 Error Tests/
    ├── 401 Unauthorized
    ├── 404 Not Found
    └── 400 Bad Request
```

---

## 🔧 Variables d'environnement

### Configuration dans Postman
1. Cliquer sur l'œil 👁️ en haut à droite
2. "Add" → "Environment"
3. Nommer : "MoloLo Local"

### Variables à créer
```
base_url: http://localhost/api
user_email: john.doe@example.com
user_password: motdepasse123
user_id: 1
artiste_id: 1
```

### Utilisation dans les requêtes
- URL : `{{base_url}}/status`
- Body : `"email": "{{user_email}}"`

---

## 🧪 Scénarios de test complets

### **Scénario 1 : Nouvel utilisateur**
1. ✅ `GET /status` → Vérifier que l'API fonctionne
2. ✅ `POST /inscription` → Créer un compte
3. ✅ `POST /connexion` → Se connecter
4. ✅ `GET /profile` → Vérifier le profil (peut être vide)
5. ✅ `POST /profile` → Créer son profil
6. ✅ `POST /epk` → Créer son EPK
7. ✅ `POST /deconnexion` → Se déconnecter

### **Scénario 2 : Utilisateur existant**
1. ✅ `POST /connexion` → Se connecter
2. ✅ `GET /artistes` → Voir tous les artistes
3. ✅ `GET /profile` → Voir son profil
4. ✅ `PUT /profile` → Modifier son profil
5. ✅ `GET /agenda` → Voir son agenda
6. ✅ `POST /booking` → Faire une demande de booking

### **Scénario 3 : Tests d'erreurs**
1. ❌ `POST /connexion` avec mauvais identifiants → 401
2. ❌ `GET /profile` sans être connecté → 401
3. ❌ `GET /artiste/999` → 404
4. ❌ `POST /inscription` avec email invalide → 400

---

## 🛠️ Scripts de test automatiques

### Pre-request Script (pour sauvegarder des variables)
```javascript
// Sauvegarder l'email de test
pm.environment.set("test_email", "test" + Date.now() + "@example.com");
```

### Tests (pour valider les réponses)
```javascript
// Test basique
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

// Test JSON
pm.test("Response is JSON", function () {
    pm.response.to.be.json;
});

// Test contenu
pm.test("Response has success field", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('success');
});

// Sauvegarder l'ID utilisateur après inscription
pm.test("Save user ID", function () {
    var jsonData = pm.response.json();
    if (jsonData.user_id) {
        pm.environment.set("user_id", jsonData.user_id);
    }
});
```

---

## 🔍 Dépannage

### **Erreur 404 - Not Found**
- Vérifier l'URL de base
- S'assurer que mod_rewrite est activé
- Vérifier que le fichier `.htaccess` existe

### **Erreur 500 - Internal Server Error**
- Vérifier les logs Apache/PHP
- S'assurer que la base de données est accessible
- Vérifier la configuration dans `database.php`

### **Erreur 401 - Unauthorized**
- Vérifier que vous êtes connecté
- Refaire une connexion si nécessaire
- Vérifier que les cookies sont activés dans Postman

### **Problèmes de cookies/sessions**
1. Dans Postman : Settings → General
2. Activer "Automatically follow redirects"
3. Activer "Send cookies"
4. Dans l'onglet "Cookies", vérifier la présence du cookie de session

### **Base de données vide**
```sql
-- Vérifier les tables
SHOW TABLES;

-- Vérifier les données
SELECT * FROM artiste;
```

---

## 📊 Codes de statut attendus

| Code | Signification | Cas d'usage |
|------|---------------|-------------|
| 200 | OK | Requête réussie |
| 201 | Created | Ressource créée |
| 400 | Bad Request | Données invalides |
| 401 | Unauthorized | Non authentifié |
| 404 | Not Found | Ressource non trouvée |
| 405 | Method Not Allowed | Mauvaise méthode HTTP |
| 500 | Internal Server Error | Erreur serveur |

---

## 🚀 Tests avancés

### **Test de charge**
Utiliser Postman Runner avec 100+ itérations

### **Tests automatisés**
1. Configurer Newman (CLI Postman)
2. Exporter la collection
3. Lancer : `newman run collection.json -e environment.json`

### **Monitoring**
Configurer Postman Monitor pour tests périodiques

---

**🎵 Bon testing avec MoloLo+ API !**

*Pour toute question, consulter la documentation technique dans `api/doc/mololo_technique.txt`*
