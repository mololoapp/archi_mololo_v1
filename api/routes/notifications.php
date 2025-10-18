<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';




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
            $stmt = $pdo->prepare("SELECT * FROM notification WHERE user_id = ? ORDER BY date DESC");
            $stmt->execute([$user_id]);
            $notifications = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $notifications]);
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
            if (empty($data['notification'])) $errors[] = 'Titre de notification requis';
            if (empty($data['description'])) $errors[] = 'Description requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO notification (notification, description, date, user_id) VALUES (?, ?, NOW(), ?)");
            $result = $stmt->execute([
                $data['notification'],
                $data['description'],
                $user_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Notification créée']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la création de la notification']);
            }
            break;
        
        case 'PUT':
            // PUT /api/notifications/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $data = getJsonInput();
            if (!$data) { http_response_code(400); echo json_encode(['error' => 'Données JSON invalides']); exit; }
            $stmt = $pdo->prepare("UPDATE notification SET notification = COALESCE(?, notification), description = COALESCE(?, description) WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['notification'] ?? null,
                $data['description'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'Notification mise à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'Notification introuvable']); }
            break;

        case 'DELETE':
            // DELETE /api/notifications/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM notification WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'Notification supprimée']);
            else { http_response_code(404); echo json_encode(['error' => 'Notification introuvable']); }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion notifications : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
