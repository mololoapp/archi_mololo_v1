# 📅 Système de Booking MoloLo+ - Artistes vs Clients

## 📋 Vue d'ensemble

Le système de booking a été réorganisé pour séparer clairement les fonctionnalités entre **artistes** et **clients** :

- 🎵 **Artistes** : Reçoivent les demandes, peuvent les consulter et changer leur statut
- 👥 **Clients** : Envoient les demandes, peuvent les gérer (CRUD complet)

---

## 🎵 **Côté ARTISTE** - `booking-artiste`

### **Fonctionnalités disponibles :**
- ✅ **GET** : Consulter tous les bookings reçus
- ✅ **PATCH** : Marquer un booking comme lu
- ✅ **PATCH** : Changer le statut d'un booking

### **Endpoints :**

#### **1. Consulter les bookings reçus**
```http
GET /api/booking-artiste
Authorization: Bearer <access_token>
```

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "client_id": 1,
      "user_id": 4,
      "nom_utilisateur": "John Doe",
      "lieux": "Club XYZ",
      "adresse": "123 Rue de la Musique, Paris",
      "montant": "500€",
      "heure": "22:00:00",
      "date": "2024-12-15 22:00:00",
      "message": "Soirée électronique, 3h de set",
      "status": "en_attente",
      "read_at": null,
      "created_at": "2024-10-16 10:30:00",
      "updated_at": null,
      "client_nom": "John",
      "client_prenom": "Doe",
      "client_email": "john.doe@example.com",
      "client_numero": "+33123456789"
    }
  ],
  "total": 1
}
```

#### **2. Marquer un booking comme lu**
```http
PATCH /api/booking-artiste/{id}/read
Authorization: Bearer <access_token>
```

**Réponse :**
```json
{
  "success": true,
  "message": "Booking marqué comme lu"
}
```

#### **3. Changer le statut d'un booking**
```http
PATCH /api/booking-artiste/{id}/status
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "status": "accepte"
}
```

**Statuts possibles :**
- `en_attente` : En attente de réponse
- `accepte` : Booking accepté
- `refuse` : Booking refusé

**Réponse :**
```json
{
  "success": true,
  "message": "Statut du booking mis à jour"
}
```

---

## 👥 **Côté CLIENT** - `booking-client`

### **Fonctionnalités disponibles :**
- ✅ **GET** : Consulter tous les bookings envoyés
- ✅ **POST** : Créer un nouveau booking
- ✅ **PUT** : Modifier un booking existant
- ✅ **DELETE** : Supprimer un booking

### **Endpoints :**

#### **1. Consulter les bookings envoyés**
```http
GET /api/booking-client
Authorization: Bearer <access_token>
```

**Réponse :**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "client_id": 1,
      "user_id": 4,
      "nom_utilisateur": "John Doe",
      "lieux": "Club XYZ",
      "adresse": "123 Rue de la Musique, Paris",
      "montant": "500€",
      "heure": "22:00:00",
      "date": "2024-12-15 22:00:00",
      "message": "Soirée électronique, 3h de set",
      "status": "en_attente",
      "read_at": null,
      "created_at": "2024-10-16 10:30:00",
      "updated_at": null,
      "artiste_nom": "John",
      "nom_artiste": "DJ John",
      "artiste_email": "john.doe@example.com",
      "artiste_numero": "+33123456789",
      "style_musique": "Electronic"
    }
  ],
  "total": 1
}
```

#### **2. Créer un nouveau booking**
```http
POST /api/booking-client
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "artiste_id": 4,
  "lieux": "Club XYZ",
  "adresse": "123 Rue de la Musique, Paris",
  "montant": "500€",
  "heure": "22:00:00",
  "date": "2024-12-15 22:00:00",
  "message": "Soirée électronique, 3h de set"
}
```

**Champs requis :**
- `artiste_id` : ID de l'artiste à qui envoyer la demande
- `lieux` : Nom du lieu
- `date` : Date du booking
- `heure` : Heure du booking

**Champs optionnels :**
- `adresse` : Adresse complète
- `montant` : Montant proposé
- `message` : Message personnalisé

**Réponse :**
```json
{
  "success": true,
  "message": "Demande de booking envoyée à DJ John",
  "booking_id": 1
}
```

#### **3. Modifier un booking**
```http
PUT /api/booking-client/{id}
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "lieux": "Nouveau Club",
  "montant": "600€",
  "message": "Mise à jour du booking"
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Booking mis à jour"
}
```

#### **4. Supprimer un booking**
```http
DELETE /api/booking-client/{id}
Authorization: Bearer <access_token>
```

**Réponse :**
```json
{
  "success": true,
  "message": "Booking supprimé"
}
```

---

## 🗄️ **Structure de la base de données**

### **Table `booking` mise à jour :**
```sql
CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,           -- ID du client (users.id)
  `user_id` int(11) NOT NULL,             -- ID de l'artiste (artiste.id)
  `nom_utilisateur` varchar(200) NOT NULL, -- Nom du client
  `lieux` varchar(200) NOT NULL,          -- Nom du lieu
  `adresse` varchar(200) NOT NULL,        -- Adresse complète
  `montant` varchar(200) NOT NULL,        -- Montant proposé
  `heure` time NOT NULL,                  -- Heure du booking
  `date` datetime NOT NULL,               -- Date du booking
  `message` varchar(200) NOT NULL,        -- Message du client
  `status` enum('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente',
  `read_at` datetime DEFAULT NULL,        -- Date de lecture par l'artiste
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
);
```

### **Relations :**
- `client_id` → `users.id` (clé étrangère)
- `user_id` → `artiste.id` (clé étrangère)

---

## 🔄 **Flux de travail**

### **1. Création d'un booking (Client)**
```
Client → POST /booking-client → Booking créé (status: en_attente)
```

### **2. Consultation par l'artiste**
```
Artiste → GET /booking-artiste → Voir tous les bookings reçus
```

### **3. Marquer comme lu (Artiste)**
```
Artiste → PATCH /booking-artiste/{id}/read → Booking marqué comme lu
```

### **4. Changer le statut (Artiste)**
```
Artiste → PATCH /booking-artiste/{id}/status → Status changé (accepte/refuse)
```

### **5. Gestion par le client**
```
Client → GET/PUT/DELETE /booking-client → Gérer ses bookings envoyés
```

---

## 🧪 **Tests avec le tester.php**

### **Tests pour les artistes :**
1. **Connexion artiste** : `POST /connexion`
2. **Voir les bookings** : `GET /booking-artiste`
3. **Marquer comme lu** : `PATCH /booking-artiste/1/read`
4. **Accepter booking** : `PATCH /booking-artiste/1/status`

### **Tests pour les clients :**
1. **Connexion client** : `POST /connexion_user`
2. **Créer booking** : `POST /booking-client`
3. **Voir ses bookings** : `GET /booking-client`
4. **Modifier booking** : `PUT /booking-client/1`
5. **Supprimer booking** : `DELETE /booking-client/1`

---

## 📊 **Statistiques dans le dashboard**

Le dashboard artiste affiche maintenant :
- **Bookings non lus** : Nombre de bookings avec `read_at = NULL`
- **Bookings en attente** : Nombre de bookings avec `status = 'en_attente'`
- **Bookings acceptés** : Nombre de bookings avec `status = 'accepte'`

---

## 🚨 **Sécurité**

### **Isolation des données :**
- ✅ **Artistes** ne voient que leurs bookings reçus (`user_id = artiste.id`)
- ✅ **Clients** ne voient que leurs bookings envoyés (`client_id = client.id`)
- ✅ **Authentification JWT** requise pour tous les endpoints
- ✅ **Validation** des données d'entrée

### **Contraintes métier :**
- ✅ Un client ne peut modifier que ses propres bookings
- ✅ Un artiste ne peut changer le statut que de ses bookings reçus
- ✅ Vérification de l'existence de l'artiste avant création

---

## 🔧 **Migration nécessaire**

Exécutez le script de migration pour ajouter les nouvelles colonnes :

```sql
-- Voir le fichier : api/migrations/update_booking_table.sql
```

---

**🎵 MoloLo+ - Système de booking séparé pour artistes et clients !**
