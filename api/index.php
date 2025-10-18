<?php
/**
 * MoloLo+ API REST - Point d'entrée principal
 * Routeur centralisé pour tous les endpoints de l'API
 */

// Inclure la configuration de sécurité et CORS
require_once __DIR__ . '/config/config.php';

// Sessions non utilisées (JWT-only)

// Récupérer l'URI et la méthode HTTP
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Nettoyer l'URI (enlever les paramètres GET et le préfixe répertoire jusqu'à /api)
$uri = parse_url($request_uri, PHP_URL_PATH);
// Déterminer le répertoire courant (ex: /archi_mololo_v1/api)
$script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($script_dir !== '' && $script_dir !== '/' && strpos($uri, $script_dir) === 0) {
    $uri = substr($uri, strlen($script_dir));
}
$uri = ltrim($uri, '/');

// Découper l'URI en segments
$uri_segments = $uri ? explode('/', $uri) : [];
$endpoint = $uri_segments[0] ?? '';

// Fonction utilitaire pour envoyer une réponse JSON
function sendResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

// Routage principal
switch ($endpoint) {
    // ========== PARTIE ARTISTE ==========

    // ========== AUTHENTIFICATION ==========
    case 'inscription':
    case 'register':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/inscription.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'connexion':
    case 'login':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/jwt_connexion.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'deconnexion':
    case 'logout':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/jwt_deconnexion.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'password':
        include __DIR__ . '/routes/password.php';
        break;
        
    case 'refresh-token':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/jwt_refresh.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;
        
    case 'deconnexion-jwt':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/jwt_deconnexion.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    // ========== GESTION DES ARTISTES ==========
    case 'artistes':
        if ($request_method === 'GET') {
            include __DIR__ . '/routes/artistes.php';
        } elseif ($request_method === 'POST') {
            // Créer un nouvel artiste (redirection vers inscription)
            include __DIR__ . '/routes/inscription.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'artiste':
        include __DIR__ . '/routes/artiste.php';
        break;

    // ========== GESTION DES PROFILS ==========
    case 'profile':
    case 'profil':
        include __DIR__ . '/routes/profile.php';
        break;

    // ========== GESTION DES EPK ==========
    case 'epk':
        include __DIR__ . '/routes/epk.php';
        break;

    // ========== GÉNÉRATION PDF ==========
    case 'pdf':
        include __DIR__ . '/routes/pdf.php';
        break;

    // ========== WHATSAPP ==========
    case 'contacts':
        // GET /contacts?action=contacts
        if ($request_method === 'GET') {
            include __DIR__ . '/routes/whatsapp.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'send':
        // POST /send?action=send
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/whatsapp.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    // ========== GESTION DU BOOKING ==========
        
    case 'booking-artiste':
        include __DIR__ . '/routes/booking_artiste.php';
        break;

    // ========== GESTION DE L'AGENDA ==========
    case 'agenda':
        include __DIR__ . '/routes/agenda.php';
        break;

    // ========== GESTION DES OPPORTUNITÉS ==========
    case 'opportunites':
    case 'opportunities':
        include __DIR__ . '/routes/opportunites.php';
        break;

    // ========== GESTION DE LA GALERIE ==========
    case 'galerie':
    case 'gallery':
        include __DIR__ . '/routes/galerie.php';
        break;

    // ========== GESTION DES NOTIFICATIONS ==========
    case 'notifications':
        include __DIR__ . '/routes/notifications.php';
        break;

    // ========== ENVOI D'EMAILS POUR ARTISTES ==========
    case 'contacts_email':
        if ($request_method === 'GET') {
            include __DIR__ . '/routes/email.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'send_email':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/email.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    // ========== GESTION DES SMARTLINKS ==========
    case 'smartlink':
        include __DIR__ . '/routes/smartlink.php';
        break;

    // ========== DASHBOARD ARTISTE ==========
    case 'dashboard':
        include __DIR__ . '/routes/dashboard.php';
        break;
    

    // ========== PARTIE CLIENT ==========

    // ========== AUTHENTIFICATION CLIENT ==========

    case 'userloggin':
    case 'connexion_user':
        if($request_method === 'POST'){
            include __DIR__ . '/routes/jwt_connexion_user.php';
        }else{
            sendResponse(['error ' => 'methde non autorisée'], 405);
        }
        break;

    case 'inscription_user':
    case 'register_user':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/inscription_user.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'deconnexion_user':
    case 'logout_user':
        if ($request_method === 'POST') {
            include __DIR__ . '/routes/jwt_deconnexion.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

     // ========== GESTION DES CIENT ==========
    case 'users':
        if ($request_method === 'GET') {
            include __DIR__ . '/routes/users.php';
        } elseif ($request_method === 'POST') {
            include __DIR__ . '/routes/inscription_user.php';
        } else {
            sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
        break;

    case 'user':
        include __DIR__ . '/routes/user.php';
        break;

    // ========== GESTION BOOKING ==========

    case 'booking-client':
        include __DIR__ . '/routes/booking_client.php';
        break;

    // ========== ENDPOINT DE STATUT ==========
    case 'status':
    case '':
        sendResponse([
            'success' => true,
            'message' => 'API MoloLo+ opérationnelle',
            'version' => '1.0',
            'endpoints' => [
                'POST /inscription' => 'Création de compte artiste',
                'POST /connexion' => 'Connexion utilisateur',
                'POST /deconnexion' => 'Déconnexion utilisateur',
                'GET /artistes' => 'Liste des artistes',
                'GET|PUT|DELETE /artiste/{id}' => 'Gestion d\'un artiste',
                'GET|POST|PUT /profile' => 'Gestion du profil',
                'GET|POST /epk' => 'Gestion des EPK',
                'GET|POST /booking' => 'Gestion des bookings (legacy)',
                'GET|PATCH /booking-artiste' => 'Gestion des bookings reçus (artistes)',
                'GET|POST|PUT|DELETE /booking-client' => 'Gestion des bookings envoyés (clients)',
                'GET|POST /agenda' => 'Gestion de l\'agenda',
                'GET|POST /opportunites' => 'Gestion des opportunités',
                'GET|POST /galerie' => 'Gestion de la galerie',
                'GET|POST /notifications' => 'Gestion des notifications',
                'GET|POST /smartlink' => 'Gestion des SmartLinks',
                'GET /dashboard' => 'Dashboard artiste (statistiques et résumé)'
            ]
        ]);
        break;

    // ========== ENDPOINT NON TROUVÉ ==========
    default:
        sendResponse([
            'error' => 'Endpoint non trouvé',
            'requested' => $endpoint,
            'available_endpoints' => [
                'inscription', 'connexion', 'deconnexion', 'artistes', 'artiste/{id}',
                'profile', 'epk', 'booking', 'booking-artiste', 'booking-client', 
                'agenda', 'opportunites', 'galerie', 'notifications', 'smartlink', 
                'dashboard', 'status'
            ]
        ], 404);
        break;
}
?>