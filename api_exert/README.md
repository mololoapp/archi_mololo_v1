# API RESTful PHP MySQL PDO pour Envoi de Messages WhatsApp et Emails

Cette API permet de récupérer les noms et numéros de téléphone depuis la table `artsite` de la base de données MySQL et d'envoyer des messages WhatsApp à tous les contacts via l'API wachap.app. Elle permet également de récupérer les noms et adresses email pour envoyer des emails de bienvenue via SMTP Brevo.

## Prérequis

- PHP 7.0 ou supérieur avec extension PDO activée.
- Serveur web (Apache, Nginx) configuré pour exécuter PHP.
- Base de données MySQL avec la table `artsite` contenant au moins les colonnes `name` (nom), `phone` (numéro de téléphone) et `email` (adresse email).
- Extension cURL activée pour les requêtes HTTP (pour WhatsApp).
- Fonction mail() activée pour l'envoi d'emails.

## Installation

1. Téléchargez ou clonez ce projet dans le répertoire racine de votre serveur web.
2. Configurez la base de données dans `db_config.php` :
   - Remplacez `$host`, `$dbname`, `$username`, `$password` par vos propres valeurs.
3. Configurez l'API WhatsApp dans `config.php` :
   - Remplacez `$whatsapp_instance_id` et `$whatsapp_access_token` par vos vrais identifiants de l'API wachap.app.
4. Configurez l'email SMTP dans `config_email.php` :
   - Les valeurs sont déjà pré-remplies pour Brevo.

## Utilisation

### Endpoints WhatsApp

#### 1. Récupérer les contacts (téléphone)
- **Méthode** : GET
- **URL** : `http://votre-domaine/api.php?action=contacts`
- **Description** : Récupère tous les noms et numéros de téléphone de la table `artsite`.
- **Réponse** : JSON array des contacts, e.g. `[{"name": "John Doe", "phone": "1234567890"}, ...]`

#### 2. Envoyer des messages WhatsApp
- **Méthode** : POST
- **URL** : `http://votre-domaine/api.php?action=send`
- **Description** : Envoie un message WhatsApp à chaque contact récupéré de la table `artsite`. Le message est : "Bonjour {name}, ceci est un message test."
- **Réponse** : JSON array des résultats, e.g. `[{"name": "John Doe", "phone": "1234567890", "response": {...}}, ...]`

### Endpoints Email

#### 3. Récupérer les contacts (email)
- **Méthode** : GET
- **URL** : `http://votre-domaine/api_email.php?action=contacts_email`
- **Description** : Récupère tous les noms et adresses email de la table `artsite`.
- **Réponse** : JSON array des contacts, e.g. `[{"name": "John Doe", "email": "john@example.com"}, ...]`

#### 4. Envoyer des emails de bienvenue
- **Méthode** : POST
- **URL** : `http://votre-domaine/api_email.php?action=send_email`
- **Description** : Envoie un email de bienvenue à chaque contact récupéré de la table `artsite`. Le sujet est "Bienvenue {name}" et le message inclut le nom de l'artiste.
- **Réponse** : JSON array des résultats, e.g. `[{"name": "John Doe", "email": "john@example.com", "response": {...}}, ...]`

### Exemples d'utilisation

#### Avec cURL

Récupérer les contacts téléphone :
```bash
curl -X GET "http://votre-domaine/api.php?action=contacts"
```

Envoyer les messages WhatsApp :
```bash
curl -X POST "http://votre-domaine/api.php?action=send"
```

Récupérer les contacts email :
```bash
curl -X GET "http://votre-domaine/api_email.php?action=contacts_email"
```

Envoyer les emails :
```bash
curl -X POST "http://votre-domaine/api_email.php?action=send_email"
```

#### Avec un formulaire HTML

Pour les endpoints POST, vous pouvez utiliser des formulaires simples :
```html
<form action="api.php?action=send" method="post">
    <input type="submit" value="Envoyer les messages WhatsApp">
</form>

<form action="api_email.php?action=send_email" method="post">
    <input type="submit" value="Envoyer les emails de bienvenue">
</form>
```

## Sécurité

- Assurez-vous que les fichiers `db_config.php`, `config.php` et `config_email.php` ne sont pas accessibles publiquement (placez-les hors du répertoire web si possible).
- Utilisez HTTPS pour les requêtes en production.
- Validez et nettoyez les données d'entrée pour éviter les injections SQL (bien que PDO soit utilisé, c'est une bonne pratique).

## Dépannage

- **Erreur de connexion DB** : Vérifiez les paramètres dans `db_config.php`.
- **Erreur API WhatsApp** : Vérifiez `instance_id` et `access_token` dans `config.php`.
- **Erreur email** : Vérifiez la configuration SMTP dans `config_email.php` et assurez-vous que la fonction mail() est configurée correctement.
- **cURL non disponible** : Assurez-vous que l'extension cURL est activée dans php.ini.

## Licence

Ce projet est fourni tel quel, sans garantie.
