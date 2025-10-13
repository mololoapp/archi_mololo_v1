<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';


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

$user_id = require_jwt_auth();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $stmt = $pdo->prepare("SELECT * FROM smartlink WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $smartlinks = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $smartlinks]);
            break;
            
        case 'POST':
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Validation des champs requis
            $errors = [];
            if (empty($data['smartlink'])) $errors[] = 'SmartLink requis';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO smartlink (smartlink, smartlink_whatsapp, smartlink_email, user_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['smartlink'],
                $data['smartlink_whatsapp'] ?? '',
                $data['smartlink_email'] ?? '',
                $user_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'SmartLink créé']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la création du SmartLink']);
            }
            break;
        
        case 'PUT':
            // PUT /api/smartlink/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $data = getJsonInput();
            if (!$data) { http_response_code(400); echo json_encode(['error' => 'Données JSON invalides']); exit; }
            $stmt = $pdo->prepare("UPDATE smartlink SET smartlink = COALESCE(?, smartlink), smartlink_whatsapp = COALESCE(?, smartlink_whatsapp), smartlink_email = COALESCE(?, smartlink_email) WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['smartlink'] ?? null,
                $data['smartlink_whatsapp'] ?? null,
                $data['smartlink_email'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'SmartLink mis à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'SmartLink introuvable']); }
            break;

        case 'DELETE':
            // DELETE /api/smartlink/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM smartlink WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'SmartLink supprimé']);
            else { http_response_code(404); echo json_encode(['error' => 'SmartLink introuvable']); }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion SmartLink : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
