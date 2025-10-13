# MoloLo+ API REST - Documentation

## 🎵 À propos

MoloLo+ est une API REST complète dédiée aux artistes musicaux, permettant la gestion de profils, EPK (Electronic Press Kit), bookings, agenda et bien plus encore.

## 🚀 Mises à jour récentes

### Restructuration complète de l'architecture (Octobre 2024)

L'API a été entièrement refactorisée pour améliorer la maintenabilité, la sécurité et les performances :

#### ✅ **Avant vs Après**

| **Avant** | **Après** |
|-----------|-----------|
| 1 fichier monolithique (564 lignes) | Architecture modulaire (13+ fichiers) |
| Requêtes SQL dans l'index.php | Routes séparées avec logique métier |
| Mots de passe en clair | Hachage sécurisé avec `password_hash()` |
| Pas de gestion de sessions | Authentification par sessions PHP |
| Base de données incorrecte | Structure alignée avec `mololo_plus` |
### Migration Auth JWT-only (Octobre 2025)

- API désormais en authentification JWT (Bearer) uniquement, plus de sessions PHP.
- Access tokens valides 60 jours par défaut.
- Refresh tokens persistés serveur avec rotation sécurisée.


## 📁 Structure du projet

```
api/
├── index.php                 # 🎯 Routeur principal (116 lignes)
├── .htaccess                 # ⚙️ Configuration Apache
├── config/
│   └── config.php           # 🔐 Configuration CORS et sécurité
├── models/
│   ├── database.php         # 🗄️ Classe Database (PDO)
│   ├── api.php              # 🛠️ Utilitaires
│   └── data.sql             # 📊 Structure de la base de données
├── routes/                   # 🛣️ Routes de l'API (13 fichiers)
│   ├── inscription.php      # 📝 Création de compte
│   ├── jwt_connexion.php    # 🔑 Authentification (JWT)
│   ├── jwt_refresh.php      # ♻️ Refresh token (JWT)
│   ├── jwt_deconnexion.php  # 🚪 Révocation refresh (JWT)
│   ├── artistes.php         # 👥 Liste des artistes
│   ├── artiste.php          # 👤 Gestion d'un artiste
│   ├── profile.php          # 📋 Gestion des profils
│   ├── epk.php              # 📁 Electronic Press Kit
│   ├── booking.php          # 📅 Réservations
│   ├── agenda.php           # 🗓️ Agenda des événements
│   ├── opportunites.php     # 💼 Opportunités
│   ├── galerie.php          # 🖼️ Galerie média
│   ├── notifications.php    # 🔔 Notifications
│   └── smartlink.php        # 🔗 Liens intelligents
└── doc/
    └── mololo_technique.txt  # 📖 Documentation technique
```

## 🔧 Améliorations techniques

### 1. **Sécurité renforcée**
- ✅ **Hachage des mots de passe** : Utilisation de `password_hash()` et `password_verify()`
- ✅ **Sessions sécurisées** : Gestion d'authentification avec sessions PHP
- ✅ **Validation stricte** : Validation des entrées dans chaque route
- ✅ **Protection des fichiers** : `.htaccess` empêche l'accès direct aux routes
- ✅ **Logs d'erreur** : Traçabilité avec `error_log()`

### 2. **Base de données**
- ✅ **Classe Database** : Connexion PDO centralisée et sécurisée
- ✅ **Prepared statements** : Protection contre les injections SQL
- ✅ **Nom correct** : Alignement avec `mololo_plus` (au lieu de `mololo`)
- ✅ **Gestion d'erreurs** : Try/catch pour toutes les opérations

### 3. **Architecture modulaire**
- ✅ **Séparation des responsabilités** : Chaque endpoint dans son propre fichier
- ✅ **Routeur léger** : Index.php focus sur le routage uniquement
- ✅ **Réutilisabilité** : Fonctions communes dans chaque route
- ✅ **Maintenabilité** : Code organisé et documenté

## 🌐 Endpoints de l'API

### **Authentification (JWT)**
```http
POST /api/inscription     # Créer un compte artiste
POST /api/connexion       # Se connecter (JWT access + refresh)
POST /api/refresh-token   # Rafraîchir access + refresh
POST /api/deconnexion     # Révoquer le refresh token
```

### **Gestion des artistes**
```http
GET  /api/artistes        # Liste tous les artistes
GET  /api/artiste/{id}    # Détails d'un artiste
PUT  /api/artiste/{id}    # Modifier un artiste (auth requis)
DELETE /api/artiste/{id}  # Supprimer un artiste (auth requis)
```

### **Profil utilisateur**
```http
GET  /api/profile         # Récupérer son profil (auth requis)
POST /api/profile         # Créer son profil (auth requis)
PUT  /api/profile         # Modifier son profil (auth requis)
```

### **EPK (Electronic Press Kit)**
```http
GET  /api/epk            # Liste des EPK (auth requis)
GET  /api/epk/{id}       # Détails d'un EPK (auth requis)
POST /api/epk            # Créer un EPK (auth requis)
```

### **Booking et événements**
```http
GET  /api/booking        # Liste des bookings (auth requis)
POST /api/booking        # Demande de booking
GET  /api/agenda         # Agenda des événements (auth requis)
POST /api/agenda         # Ajouter un événement (auth requis)
```

### **Contenu et opportunités**
```http
GET  /api/opportunites   # Liste des opportunités
POST /api/opportunites   # Créer une opportunité (auth requis)
GET  /api/galerie        # Galerie média
POST /api/galerie        # Ajouter du contenu (auth requis)
```

### **Communication**
```http
GET  /api/notifications  # Notifications (auth requis)
POST /api/notifications  # Créer une notification (auth requis)
GET  /api/smartlink      # SmartLinks (auth requis)
POST /api/smartlink      # Créer un SmartLink (auth requis)
```

### **Statut**
```http
GET  /api/status         # Statut de l'API et liste des endpoints
```

## 🔨 Installation et configuration

### 1. **Prérequis**
- PHP 7.4+ avec PDO
- MySQL/MariaDB
- Apache avec mod_rewrite
- XAMPP/WAMP/LAMP

### 2. **Configuration de la base de données**
```sql
-- Importer le fichier data.sql
mysql -u root -p mololo_plus < api/models/data.sql
```

### 3. **Configuration Apache**
Le fichier `.htaccess` est déjà configuré pour :
- Redirection vers `index.php`
- Protection des fichiers sensibles
- En-têtes de sécurité

### 4. **Variables d'environnement**
- `JWT_SECRET` : secret de signature JWT (obligatoire en prod)
- Modifier `api/models/database.php` si nécessaire :
```php
private $host = 'localhost';
private $dbname = 'mololo';
private $username = 'root';
private $password = '';
```

## 📝 Utilisation

### **Exemple d'inscription**
```javascript
fetch('/api/inscription', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        nom: 'John Doe',
        nom_artiste: 'DJ John',
        email: 'john@example.com',
        numero: '+33123456789',
        style_musique: 'Electronic',
        password: 'motdepasse123'
    })
})
.then(res => res.json())
.then(data => console.log(data));
```

### **Exemple de connexion (JWT)**
```javascript
fetch('/api/connexion', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        identifiant: 'john@example.com',
        password: 'motdepasse123'
    })
})
.then(res => res.json())
.then(({ access_token, refresh_token }) => {
    localStorage.setItem('access_token', access_token);
    localStorage.setItem('refresh_token', refresh_token);
});
```

### **Exemple d'appel authentifié (Bearer)**
```javascript
fetch('/api/profile', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('access_token')}`
    }
})
.then(res => res.json())
.then(data => console.log(data));
```

### **Refresh token**
```javascript
fetch('/api/refresh-token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ refresh_token: localStorage.getItem('refresh_token') })
})
.then(res => res.json())
.then(({ access_token, refresh_token }) => {
    localStorage.setItem('access_token', access_token);
    localStorage.setItem('refresh_token', refresh_token);
});
```

## 🔐 Sécurité

### **Authentification**
- Les mots de passe sont hachés avec `password_hash(PASSWORD_DEFAULT)`
- Authentification par JWT Bearer; pas de sessions PHP
- Access token: expiration 60 jours; refresh token stocké en base

### **Autorisation**
- `Authorization: Bearer <access_token>` requis sur les endpoints protégés
- Extraction du `user_id` via `require_jwt_auth()` côté serveur

### **Validation des données**
- Validation stricte des entrées dans chaque route
- Messages d'erreur informatifs avec codes HTTP appropriés
- Protection contre les injections SQL via prepared statements

## 📊 Base de données

### **Tables principales**
- `artiste` - Informations des artistes
- `profile` - Profils détaillés
- `epk` - Electronic Press Kits
- `booking` - Demandes de réservation
- `agenda` - Événements programmés
- `opportunite` - Opportunités de travail
- `galerie` - Contenu média
- `notification` - Notifications système
- `smartlink` - Liens intelligents

## 🧪 Tests

### **Avec Postman/Insomnia**
1. Tester l'inscription d'un artiste
2. Tester la connexion
3. Tester les endpoints protégés
4. Vérifier les codes de statut HTTP

### **Avec curl**
```bash
# Test de statut
curl http://localhost/api/status

# Test d'inscription
curl -X POST http://localhost/api/inscription \
  -d "nom=Test&nom_artiste=TestArt&email=test@test.com&numero=123456789&style_musique=Rock&password=test123"

# Connexion (JWT)
curl -X POST http://localhost/api/connexion \
  -d "identifiant=test@test.com&password=test123"

# Appel protégé (remplacer ACCESS_TOKEN)
curl -H "Authorization: Bearer ACCESS_TOKEN" http://localhost/api/profile

# Refresh (remplacer REFRESH_TOKEN)
curl -X POST http://localhost/api/refresh-token \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"REFRESH_TOKEN"}'
```

## 🐛 Débogage

### **Logs d'erreurs**
- Erreurs PHP dans les logs Apache
- Erreurs applicatives via `error_log()`
- Vérifier les logs : `/var/log/apache2/error.log` ou équivalent XAMPP

### **Problèmes courants**
1. **Erreur 404** : Vérifier mod_rewrite et .htaccess
2. **Erreur de connexion DB** : Vérifier les paramètres dans `database.php`
3. **Erreur d'authentification** : Vérifier les sessions PHP

## 🤝 Contribution

Pour ajouter de nouvelles fonctionnalités :

1. **Créer une nouvelle route** dans `/routes/`
2. **Ajouter le routage** dans `index.php`
3. **Respecter la structure** :
   ```php
   <?php
   header('Content-Type: application/json');
   require_once __DIR__ . '/../models/database.php';
   session_start();
   
   // Logique de la route...
   ```
4. **Tester** l'endpoint
5. **Documenter** dans ce README

## 📈 Roadmap

### **Prochaines améliorations**
- [ ] Authentification JWT
- [ ] Upload de fichiers sécurisé
- [ ] Cache Redis
- [ ] Rate limiting
- [ ] Documentation Swagger/OpenAPI
- [ ] Tests unitaires
- [ ] Docker containers

## 👨‍💻 Auteur

Développé pour MoloLo+ - Plateforme dédiée aux artistes musicaux.

## 📄 Licence

Tous droits réservés - MoloLo+

---

**🎵 MoloLo+ - Donnez vie à votre musique !**