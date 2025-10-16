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
    
    // Vérifier l'authentification JWT (artiste)
    $user_id = require_jwt_auth();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Récupérer tous les bookings reçus par l'artiste
            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    u.nom as client_nom,
                    u.prenom as client_prenom,
                    u.email as client_email,
                    u.numero as client_numero
                FROM booking b
                LEFT JOIN users u ON b.client_id = u.id
                WHERE b.user_id = ? 
                ORDER BY b.date DESC
            ");
            $stmt->execute([$user_id]);
            $bookings = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true, 
                'data' => $bookings,
                'total' => count($bookings)
            ]);
            break;
            
        case 'PATCH':
            // PATCH /api/booking-artiste/{id}/read - Marquer comme lu
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            $action = $parts[3] ?? null;
            
            if (!$id) { 
                http_response_code(400); 
                echo json_encode(['error' => 'ID requis']); 
                exit; 
            }
            
            if ($action === 'read') {
                $stmt = $pdo->prepare("UPDATE booking SET read_at = NOW() WHERE id = ? AND user_id = ?");
                $ok = $stmt->execute([$id, $user_id]);
                if ($ok && $stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Booking marqué comme lu']);
                } else {
                    http_response_code(404); 
                    echo json_encode(['error' => 'Booking introuvable']);
                }
            } elseif ($action === 'status') {
                // PATCH /api/booking-artiste/{id}/status - Changer le statut
                $data = getJsonInput();
                if (!$data || !isset($data['status'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Statut requis']);
                    exit;
                }
                
                $allowed_statuses = ['en_attente', 'accepte', 'refuse'];
                if (!in_array($data['status'], $allowed_statuses)) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Statut invalide. Valeurs autorisées: ' . implode(', ', $allowed_statuses)]);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE booking SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $ok = $stmt->execute([$data['status'], $id, $user_id]);
                if ($ok && $stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Statut du booking mis à jour']);
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
    error_log("Erreur gestion booking artiste : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
