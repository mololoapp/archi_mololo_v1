<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';


// Fonction utilitaire pour vérifier l'authentification
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Authentification requise'
        ]);
        exit();
    }
}

// Fonction utilitaire pour récupérer les données JSON
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

requireAuth();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer l'ID du profil (par défaut celui de l'utilisateur connecté)
    $request_uri = $_SERVER['REQUEST_URI'];
    $uri = parse_url($request_uri, PHP_URL_PATH);
    $uri = str_replace('/api', '', $uri);
    $uri = trim($uri, '/');
    $uri_segments = explode('/', $uri);
    $profile_id = $uri_segments[1] ?? $_SESSION['user_id'];
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $stmt = $pdo->prepare("SELECT * FROM profile WHERE id = ?");
            $stmt->execute([$profile_id]);
            $profile = $stmt->fetch();
            
            if ($profile) {
                echo json_encode(['success' => true, 'data' => $profile]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Profil non trouvé']);
            }
            break;
            
        case 'POST':
        case 'PUT':
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Vérifier si le profil existe
            $stmt = $pdo->prepare("SELECT id FROM profile WHERE id = ?");
            $stmt->execute([$profile_id]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Mise à jour
                $stmt = $pdo->prepare("UPDATE profile SET photo_couverture = ?, photo_profile = ?, SmartLink = ?, ville = ?, bio_courte = ?, bio_detailles = ?, instagram = ?, tiktok = ?, twitter = ?, linkeding = ?, facebook = ?, Spotify = ?, apple_music = ?, youtube = ?, Deezer = ?, Audiomack = ?, style_musique = ?, bio = ? WHERE id = ?");
                $result = $stmt->execute([
                    $data['photo_couverture'] ?? '',
                    $data['photo_profile'] ?? '',
                    $data['SmartLink'] ?? '',
                    $data['ville'] ?? '',
                    $data['bio_courte'] ?? '',
                    $data['bio_detailles'] ?? '',
                    $data['instagram'] ?? '',
                    $data['tiktok'] ?? '',
                    $data['twitter'] ?? '',
                    $data['linkeding'] ?? '',
                    $data['facebook'] ?? '',
                    $data['Spotify'] ?? '',
                    $data['apple_music'] ?? '',
                    $data['youtube'] ?? '',
                    $data['Deezer'] ?? '',
                    $data['Audiomack'] ?? '',
                    $data['style_musique'] ?? '',
                    $data['bio'] ?? '',
                    $profile_id
                ]);
                $message = 'Profil mis à jour';
            } else {
                // Création
                $stmt = $pdo->prepare("INSERT INTO profile (id, photo_couverture, photo_profile, SmartLink, ville, bio_courte, bio_detailles, instagram, tiktok, twitter, linkeding, facebook, Spotify, apple_music, youtube, Deezer, Audiomack, style_musique, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([
                    $profile_id,
                    $data['photo_couverture'] ?? '',
                    $data['photo_profile'] ?? '',
                    $data['SmartLink'] ?? '',
                    $data['ville'] ?? '',
                    $data['bio_courte'] ?? '',
                    $data['bio_detailles'] ?? '',
                    $data['instagram'] ?? '',
                    $data['tiktok'] ?? '',
                    $data['twitter'] ?? '',
                    $data['linkeding'] ?? '',
                    $data['facebook'] ?? '',
                    $data['Spotify'] ?? '',
                    $data['apple_music'] ?? '',
                    $data['youtube'] ?? '',
                    $data['Deezer'] ?? '',
                    $data['Audiomack'] ?? '',
                    $data['style_musique'] ?? '',
                    $data['bio'] ?? ''
                ]);
                $message = 'Profil créé';
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la mise à jour du profil']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion profil : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
