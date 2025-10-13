<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../utils/upload.php';

// Fonction utilitaire pour récupérer les données JSON
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

$user_id = require_jwt_auth();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // ID du profil = utilisateur authentifié via JWT
    $profile_id = $user_id;
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = ?");
            $stmt->execute([$profile_id]);
            $profile = $stmt->fetch();
            
            if ($profile) {
                echo json_encode(['success' => true, 'data' => $profile]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Profil non trouvé ']);
            }
            break;
            
        case 'POST':
        case 'PUT':
            $data = $_POST ?: getJsonInput() ?: [];
            
            if (!$data && empty($_FILES)) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Vérifier si le profil existe
            $stmt = $pdo->prepare("SELECT id FROM profile WHERE user_id = ?");
            $stmt->execute([$profile_id]);
            $exists = $stmt->fetch();
            
            // Uploads optionnels
            $photoCouverture = $data['photo_couverture'] ?? null;
            if (!empty($_FILES['photo_couverture']) && is_array($_FILES['photo_couverture'])) {
                $res = move_uploaded_image($_FILES['photo_couverture'], __DIR__ . '/../../uploads');
                if ($res['ok']) $photoCouverture = $res['path'];
            }
            $photoProfile = $data['photo_profile'] ?? null;
            if (!empty($_FILES['photo_profile']) && is_array($_FILES['photo_profile'])) {
                $res2 = move_uploaded_image($_FILES['photo_profile'], __DIR__ . '/../../uploads');
                if ($res2['ok']) $photoProfile = $res2['path'];
            }

            if ($exists) {
                // Mise à jour
                $stmt = $pdo->prepare("UPDATE profile SET photo_couverture = COALESCE(?, photo_couverture), photo_profile = COALESCE(?, photo_profile), SmartLink = ?, ville = ?, bio_courte = ?, bio_detailles = ?, instagram = ?, tiktok = ?, twitter = ?, linkeding = ?, facebook = ?, Spotify = ?, apple_music = ?, youtube = ?, Deezer = ?, Audiomack = ?, style_musique = ?, bio = ? WHERE user_id = ?");
                $result = $stmt->execute([
                    $photoCouverture,
                    $photoProfile,
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
                $stmt = $pdo->prepare("INSERT INTO profile (user_id,  photo_couverture, photo_profile, SmartLink, ville, bio_courte, bio_detailles, instagram, tiktok, twitter, linkeding, facebook, Spotify, apple_music, youtube, Deezer, Audiomack, style_musique, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([
                    $profile_id,
                    $photoCouverture ?? '',
                    $photoProfile ?? '',
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
