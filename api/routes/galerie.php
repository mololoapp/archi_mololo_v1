<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../utils/upload.php';


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

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $user_id = require_jwt_auth();
            $stmt = $pdo->prepare("SELECT * FROM galerie WHERE user_id = ? ORDER BY date DESC");
            $stmt->execute([$user_id]);
            $gallery = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $gallery]);
            break;
            
        case 'POST':
            $user_id = require_jwt_auth();
            $data = $_POST ?: getJsonInput() ?: [];
            
            if (!$data && empty($_FILES)) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Validation des champs requis
            $errors = [];
            if (empty($data['titre']) && empty($data['tire'])) $errors[] = 'Titre requis';
            $uploadedPath = '';
            if (!empty($_FILES['image']) && is_array($_FILES['image'])) {
                $res = move_uploaded_image($_FILES['image'], __DIR__ . '/../../uploads');
                if (!$res['ok']) $errors[] = 'Upload image invalide: ' . $res['error'];
                else $uploadedPath = $res['path'];
            }
            if (empty($uploadedPath) && empty($data['image']) && empty($data['video'])) $errors[] = 'Image ou vidéo requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO galerie (image, video, tire, favorie, description, details, date, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $uploadedPath ?: ($data['image'] ?? ''),
                $data['video'] ?? '',
                $data['titre'] ?? $data['tire'] ?? '',
                $data['favorie'] ?? '',
                $data['description'] ?? '',
                $data['details'] ?? '',
                $data['date'] ?? date('Y-m-d H:i:s'),
                $user_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Élément ajouté à la galerie']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'ajout à la galerie']);
            }
            break;
        
        case 'PUT':
            $user_id = require_jwt_auth();
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $data = getJsonInput();
            if (!$data) { http_response_code(400); echo json_encode(['error' => 'Données JSON invalides']); exit; }
            $stmt = $pdo->prepare("UPDATE galerie SET image = COALESCE(?, image), video = COALESCE(?, video), tire = COALESCE(?, tire), favorie = COALESCE(?, favorie), description = COALESCE(?, description), details = COALESCE(?, details) WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['image'] ?? null,
                $data['video'] ?? null,
                ($data['titre'] ?? $data['tire'] ?? null),
                $data['favorie'] ?? null,
                $data['description'] ?? null,
                $data['details'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'Élément mis à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'Élément introuvable']); }
            break;

        case 'DELETE':
            $user_id = require_jwt_auth();
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM galerie WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'Élément supprimé']);
            else { http_response_code(404); echo json_encode(['error' => 'Élément introuvable']); }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion galerie : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
