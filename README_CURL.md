## 🧪 Tester l'API MoloLo+ avec curl (PowerShell)

Ce guide montre comment tester chaque endpoint avec `curl.exe` sous Windows PowerShell, sans Postman. Tous les exemples utilisent une variable `$nom_url`.

---

### ✅ Prérequis
- Avoir `curl` installé (utiliser `curl.exe` sur PowerShell)
- API démarrée en local
- Définir l’URL de base (choisir celle qui correspond à votre setup) :
  - `http://localhost/api`
  - `http://localhost/archi_mololo_v1/api/` (avec slash final si vous concaténez des chemins)

Note PowerShell: `curl` est un alias d'`Invoke-WebRequest`. Utilisez toujours `curl.exe` pour ces exemples.

Initialisation recommandée dans PowerShell :
```powershell
$nom_url = "http://localhost/archi_mololo_v1/api/"
```

---

### 🔧 En-têtes utiles
- `Content-Type: application/x-www-form-urlencoded` (formulaires)
- `Content-Type: application/json` (JSON)

---

### 🌐 Endpoints publics (curl.exe)

1) Statut API
```powershell
curl.exe -i "$nom_url/status"
```

2) Liste des artistes
```powershell
curl.exe -i "$nom_url/artistes"
```

3) Détails d’un artiste (ID = 1)
```powershell
curl.exe -i "$nom_url/artiste/1"
```

4) Liste des opportunités
```powershell
curl.exe -i "$nom_url/opportunites"
```

---

### 🔐 Authentification (avec gestion de session via cookies)

Notes importantes:
- On stocke les cookies de session après connexion avec `-c cookies.txt`.
- On réutilise la session avec `-b cookies.txt` pour les endpoints protégés.

1) Inscription (form-urlencoded)
```powershell
curl.exe -i -X POST "$nom_url/inscription" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data-urlencode "nom=John Doe" \
  --data-urlencode "nom_artiste=DJ John" \
  --data-urlencode "email=john.doe@example.com" \
  --data-urlencode "numero=+33123456789" \
  --data-urlencode "style_musique=Electronic" \
  --data-urlencode "password=motdepasse123"
```

2) Connexion (sauvegarde de session)
```powershell
curl.exe -i -X POST "$nom_url/connexion" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -c cookies.txt \
  --data-urlencode "identifiant=john.doe@example.com" \
  --data-urlencode "password=motdepasse123"
```

3) Déconnexion
```powershell
curl.exe -i -X POST "$nom_url/deconnexion" \
  -H "Content-Type: application/json" \
  -b cookies.txt
```

---

### 🔒 Endpoints protégés (nécéssitent d’être connecté)

1) Profil — Récupérer son profil
```powershell
curl.exe -i "$nom_url/profile" -b cookies.txt
```

2) Profil — Créer/Modifier (POST)
```powershell
curl.exe -i -X POST "$nom_url/profile" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "ville": "Paris",
    "bio_courte": "Artiste électronique passionné",
    "bio_detailles": "Plus de 10 ans d\u0027expérience...",
    "instagram": "@djjohn",
    "facebook": "facebook.com/djjohn",
    "style_musique": "Electronic, House"
  }'
```

3) Profil — Modifier (PUT)
```powershell
curl.exe -i -X PUT "$nom_url/profile" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "ville": "Lyon",
    "bio_courte": "Update bio courte"
  }'
```

4) EPK — Créer
```powershell
curl.exe -i -X POST "$nom_url/epk" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "nom_artiste": "DJ John",
    "genre_musical": "Electronic",
    "localisation": "Paris, France",
    "biographie": "Artiste électronique depuis 2010...",
    "discographie": "Album 1: Future Sounds (2020), Single: Night Vibes (2024)",
    "contact": "john.doe@example.com"
  }'
```

5) Booking — Consulter (GET)
```powershell
curl.exe -i "$nom_url/booking" -b cookies.txt
```

6) Booking — Demande (POST)
```powershell
curl.exe -i -X POST "$nom_url/booking" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "nom_utilisateur": "John Doe",
    "lieux": "Club XYZ",
    "adresse": "123 Rue de la Musique, Paris",
    "montant": "500€",
    "heure": "22:00:00",
    "date": "2024-12-15 22:00:00",
    "message": "Soirée électronique, 3h de set"
  }'
```

7) Agenda — Voir (GET)
```powershell
curl.exe -i "$nom_url/agenda" -b cookies.txt
```

8) Agenda — Ajouter (POST)
```powershell
curl.exe -i -X POST "$nom_url/agenda" \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "nom_concert": "Electronic Night",
    "date": "2024-12-20 21:00:00",
    "heure": "21:00:00",
    "adresse": "Salle Pleyel, Paris",
    "description": "Concert électronique avec invités spéciaux",
    "montant": "50€",
    "nombre_personne": "500"
  }'
```

---

### 🧪 Scénarios rapides

1) Nouvel utilisateur
- `curl.exe -i "$nom_url/status"`
- `curl.exe -i -X POST "$nom_url/inscription" ...`
- `curl.exe -i -X POST "$nom_url/connexion" -c cookies.txt ...`
- `curl.exe -i "$nom_url/profile" -b cookies.txt`
- `curl.exe -i -X POST "$nom_url/profile" -b cookies.txt ...`
- `curl.exe -i -X POST "$nom_url/epk" -b cookies.txt ...`
- `curl.exe -i -X POST "$nom_url/deconnexion" -b cookies.txt`

2) Utilisateur existant
- `curl.exe -i -X POST "$nom_url/connexion" -c cookies.txt ...`
- `curl.exe -i "$nom_url/artistes"`
- `curl.exe -i "$nom_url/profile" -b cookies.txt`
- `curl.exe -i -X PUT "$nom_url/profile" -b cookies.txt ...`
- `curl.exe -i "$nom_url/agenda" -b cookies.txt`
- `curl.exe -i -X POST "$nom_url/booking" -b cookies.txt ...`

---

### 🔍 Dépannage
- 401 Unauthorized: vérifier que vous utilisez `-b cookies.txt` après connexion
- 404 Not Found: vérifier `$nom_url` et la réécriture d’URL (mod_rewrite)
- 500 Server Error: vérifier la conf DB et les logs PHP/Apache

Astuce Windows PowerShell: si l’apostrophe pose problème dans un JSON inline, utilisez un here-string et un fichier temporaire:
```powershell
@"
{
  "ville": "Paris",
  "bio_courte": "Artiste électronique"
}
"@ | Out-File -Encoding utf8 body.json
curl.exe -i -X POST "$nom_url/profile" -H "Content-Type: application/json" -b cookies.txt --data-binary @body.json
```

---

Bon tests avec MoloLo+ API !


