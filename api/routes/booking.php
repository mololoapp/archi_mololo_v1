<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';


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
            $stmt = $pdo->prepare("SELECT * FROM booking WHERE user_id = ? ORDER BY date DESC");
            $stmt->execute([$user_id]);
            $bookings = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $bookings]);
            break;
            
        case 'POST':
            $user_id = require_jwt_auth();
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Validation des champs requis
            $errors = [];
            if (empty($data['nom_utilisateur'])) $errors[] = 'Nom utilisateur requis';
            if (empty($data['lieux'])) $errors[] = 'Lieu requis';
            if (empty($data['date'])) $errors[] = 'Date requise';
            if (empty($data['heure'])) $errors[] = 'Heure requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO booking (nom_utilisateur, lieux, adresse, montant, heure, date, message, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['nom_utilisateur'],
                $data['lieux'],
                $data['adresse'] ?? '',
                $data['montant'] ?? '',
                $data['heure'],
                $data['date'],
                $data['message'] ?? '',
                $user_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Demande de booking envoyée']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'envoi de la demande']);
            }
            break;
        
        case 'PUT':
            // PUT /api/booking/{id}
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
            $stmt = $pdo->prepare("UPDATE booking SET nom_utilisateur = COALESCE(?, nom_utilisateur), lieux = COALESCE(?, lieux), adresse = COALESCE(?, adresse), montant = COALESCE(?, montant), heure = COALESCE(?, heure), date = COALESCE(?, date), message = COALESCE(?, message) WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['nom_utilisateur'] ?? null,
                $data['lieux'] ?? null,
                $data['adresse'] ?? null,
                $data['montant'] ?? null,
                $data['heure'] ?? null,
                $data['date'] ?? null,
                $data['message'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'Booking mis à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'Booking introuvable']); }
            break;

        case 'DELETE':
            // DELETE /api/booking/{id}
            $user_id = require_jwt_auth();
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM booking WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'Booking supprimé']);
            else { http_response_code(404); echo json_encode(['error' => 'Booking introuvable']); }
            break;
            
        case 'PATCH':
            // PATCH /api/booking/{id}/read - Marquer comme lu
            $user_id = require_jwt_auth();
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            $action = $parts[3] ?? null;
            
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            
            if ($action === 'read') {
                $stmt = $pdo->prepare("UPDATE booking SET read_at = NOW() WHERE id = ? AND user_id = ?");
                $ok = $stmt->execute([$id, $user_id]);
                if ($ok && $stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Booking marqué comme lu']);
                } else {
                    http_response_code(404); 
                    echo json_encode(['error' => 'Booking introuvable']);
                }
            } else {
                http_response_code(400); 
                echo json_encode(['error' => 'Action non reconnue']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion booking : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
