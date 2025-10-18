# API MoloLo+ - Routes et documentation

Ce fichier liste toutes les routes exposées par l'API `api/` avec leur méthode, paramètres, exemples d'appel (PowerShell) et notes sur l'authentification et la configuration.

---

Format des exemples PowerShell utilisés :
Invoke-RestMethod -Method <Method> -Uri "http://localhost/archi_mololo_v1/api/<endpoint>" -Headers @{ Authorization = "Bearer <token>" } -Body <body>

Remarque : remplacez `localhost/archi_mololo_v1` par votre hôte et base path si nécessaire.

---

## Authentification / Utilisateurs

### POST /inscription
- Description : Créer un compte artiste.
- Méthode : POST
- Corps : formulaire ou JSON selon implémentation (`nom`, `email`, `password`, ...)
- Auth : public

Exemple (PowerShell):
```powershell
Invoke-RestMethod -Method Post -Uri "http://localhost/archi_mololo_v1/api/inscription" -Body @{ nom = 'Dupont'; email='a@b.c'; password='secret' }
```

### POST /connexion
- Description : Connexion artiste (renvoie JWT)
- Méthode : POST
- Corps : `email`, `password`
- Auth : public

### POST /deconnexion
- Description : Déconnexion / révocation token
- Méthode : POST
- Auth : JWT

### POST /refresh-token
- Description : Rafraîchir un token d'accès (refresh)
- Méthode : POST
- Auth : dépend de l'implémentation

### POST /deconnexion-jwt
- Description : Variante de déconnexion JWT
- Méthode : POST
- Auth : JWT

---

## Artistes & Profils

### GET /artistes
- Description : Lister tous les artistes
- Méthode : GET
- Auth : public

### POST /artistes
- Description : Créer un artiste (redirige vers inscription)
- Méthode : POST
- Auth : public

### /artiste
- Description : Gestion d'un artiste individuel (GET | PUT | DELETE selon la route interne, ex: /artiste/{id})
- Auth : souvent JWT

### GET|POST|PUT /profile (alias /profil)
- Description : Gestion du profil de l'artiste
- Auth : JWT

---

## EPK & PDF

### GET /epk
- Description : Gestion EPK (lecture/génération)
- Méthode : GET (parfois POST pour création)
- Auth : dépend de l'implémentation (souvent JWT)

### GET /pdf?url=<url>
- Description : Génère un PDF à partir d'une URL via PDFShift et sauvegarde le fichier sur le serveur.
- Méthode : GET
- Paramètres : `url` (query)
- Auth : JWT (dans l'implémentation actuelle)
- Réponse : JSON { success: true, file: "<chemin_fichier_sur_serveur>" }

Exemple :
```powershell
Invoke-RestMethod -Method Get -Uri "http://localhost/archi_mololo_v1/api/pdf?url=https://example.com/page" -Headers @{ Authorization = "Bearer <token>" }
```

### POST /pdf
- Description : Génère un PDF avec corps JSON { "url": "..." }
- Méthode : POST
- Corps : JSON `{ "url": "https://..." }`
- Auth : JWT

Notes :
- La clé API PDF est lue depuis `api/config/pdf.php` (idéalement via variable d'environnement `PDF_API_KEY`).
- Le fichier retourné est le chemin sur le serveur. Pour servir un lien public, configurez le webserver pour exposer `api/pdf/tmp/`.

---

## WhatsApp (contacts & envoi)

### GET /contacts?action=contacts
- Description : Retourne la liste des contacts (name, phone) depuis la table `artiste`.
- Méthode : GET
- Auth : JWT (implémentation actuelle)

Exemple :
```powershell
Invoke-RestMethod -Method Get -Uri "http://localhost/archi_mololo_v1/api/contacts?action=contacts" -Headers @{ Authorization = "Bearer <token>" }
```

### POST /send?action=send
- Description : Envoie un message WhatsApp à tous les contacts via un proxy `wachap.app`.
- Méthode : POST
- Auth : JWT

Exemple :
```powershell
Invoke-RestMethod -Method Post -Uri "http://localhost/archi_mololo_v1/api/send" -Headers @{ Authorization = "Bearer <token>" } -Body @{ action='send' }
```

Notes : configurez `WHATSAPP_INSTANCE_ID` et `WHATSAPP_ACCESS_TOKEN` comme variables d'environnement ou dans `api/config/whatsapp.php`.

---

## Bookings

### /booking-artiste
- Description : Gestion des bookings reçus (artiste) — route `booking_artiste.php`
- Méthode : GET | PATCH (selon implémentation)
- Auth : JWT artiste

### /booking-client
- Description : Gestion des bookings envoyés (client) — route `booking_client.php`
- Méthode : GET (liste), POST (créer), PUT (modifier), DELETE (supprimer)
- Auth : JWT client

Exemple création booking (client) :
```powershell
$body = @{ artiste_id = 12; lieux='Salle X'; date='2025-11-01'; heure='20:00' } | ConvertTo-Json
Invoke-RestMethod -Method Post -Uri "http://localhost/archi_mololo_v1/api/booking-client" -Body $body -ContentType 'application/json' -Headers @{ Authorization = "Bearer <token>" }
```

---

## Agenda, Opportunités, Galerie, Notifications

### /agenda
- GET|POST : gestion agenda
- Auth : selon action (généralement JWT)

### /opportunites (alias /opportunities)
- GET|POST : gestion opportunités
- Auth : selon action

### /galerie (alias /gallery)
- GET|POST : upload / liste images
- Auth : selon action

### /notifications
- GET : liste notifications (pour user_id via JWT)
- POST : créer notification
- PUT /notifications/{id} : modifier
- DELETE /notifications/{id} : supprimer
- Auth : JWT

---

## Emails

### GET /contacts_email?action=contacts_email
- Description : Récupère `name,email` de la table `artiste` (ou `artsite` selon DB)
- Méthode : GET
- Auth : JWT

### POST /send_email?action=send_email
- Description : Envoie des emails de bienvenue à tous les contacts
- Méthode : POST
- Auth : JWT

Notes : l'envoi utilise `api/utils/mailer.php`. Configurez `api/config/email.php` (ou variables d'environnement) pour les identifiants SMTP.

---

## Smartlink & Dashboard

### /smartlink
- GET|POST : gestion des SmartLinks
- Auth : selon action

### /dashboard
- GET : résumé/statistiques pour artiste
- Auth : JWT

---

## Users (clients)

### POST /userloggin or /connexion_user
- Connexion client (renvoie JWT)

### POST /inscription_user or /register_user
- Créer compte client

### GET /users
- Lister clients

### /user
- Gestion user individuel (GET|PUT|DELETE)

---

## JWT endpoints
- `routes/jwt_connexion.php` : connexion artiste
- `routes/jwt_connexion_user.php` : connexion client
- `routes/jwt_refresh.php` : refresh token
- `routes/jwt_deconnexion.php` : deconnexion

---

## Endpoint de statut
### GET /status (ou GET /)
- Retourne un résumé de l'API et une liste sommaire d'endpoints pris en charge.

---

## Remarques finales
- Beaucoup d'endpoints utilisent la table `artiste`. Si votre base utilise `artsite` (comme dans `api_exert`), remplacez les requêtes SQL appropriées.
- Protégez vos clés (SMTP, PDF_API_KEY, WhatsApp) via variables d'environnement plutôt que de les laisser en clair.
- Si vous voulez, je peux générer un Postman collection ou un fichier OpenAPI (swagger) automatiquement à partir de cette liste.

---

Faites-moi savoir si vous voulez que je :
- transforme ceci en `README.md` plus détaillé, 
- produise une collection Postman, 
- génère un OpenAPI spec (YAML/JSON), 
- ou ajoute les exemples curl (Unix) en plus des exemples PowerShell.