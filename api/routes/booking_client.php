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
    
    // Vérifier l'authentification JWT (client)
    $user_id = require_jwt_auth();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Récupérer tous les bookings envoyés par le client
            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    a.nom as artiste_nom,
                    a.nom_artiste,
                    a.email as artiste_email,
                    a.numero as artiste_numero,
                    a.style_musique
                FROM booking b
                LEFT JOIN artiste a ON b.user_id = a.id
                WHERE b.client_id = ? 
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
            
        case 'POST':
            // Créer un nouveau booking
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Validation des champs requis
            $errors = [];
            if (empty($data['artiste_id'])) $errors[] = 'ID artiste requis';
            if (empty($data['lieux'])) $errors[] = 'Lieu requis';
            if (empty($data['date'])) $errors[] = 'Date requise';
            if (empty($data['heure'])) $errors[] = 'Heure requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            // Vérifier que l'artiste existe
            $stmt = $pdo->prepare("SELECT id, nom, nom_artiste FROM artiste WHERE id = ?");
            $stmt->execute([$data['artiste_id']]);
            $artiste = $stmt->fetch();
            
            if (!$artiste) {
                http_response_code(404);
                echo json_encode(['error' => 'Artiste non trouvé']);
                exit;
            }
            
            // Récupérer les infos du client
            $stmt = $pdo->prepare("SELECT nom, prenom FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $client = $stmt->fetch();
            
            $stmt = $pdo->prepare("
                INSERT INTO booking (
                    client_id, user_id, nom_utilisateur, lieux, adresse, 
                    montant, heure, date, message, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NOW())
            ");
            $result = $stmt->execute([
                $user_id,
                $data['artiste_id'],
                $client['nom'] . ' ' . $client['prenom'],
                $data['lieux'],
                $data['adresse'] ?? '',
                $data['montant'] ?? '',
                $data['heure'],
                $data['date'],
                $data['message'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Demande de booking envoyée à ' . $artiste['nom_artiste'],
                    'booking_id' => $pdo->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'envoi de la demande']);
            }
            break;
            
        case 'PUT':
            // PUT /api/booking-client/{id} - Modifier un booking
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            
            if (!$id) { 
                http_response_code(400); 
                echo json_encode(['error' => 'ID requis']); 
                exit; 
            }
            
            $data = getJsonInput();
            if (!$data) { 
                http_response_code(400); 
                echo json_encode(['error' => 'Données JSON invalides']); 
                exit; 
            }
            
            // Vérifier que le booking appartient au client
            $stmt = $pdo->prepare("SELECT id FROM booking WHERE id = ? AND client_id = ?");
            $stmt->execute([$id, $user_id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Booking introuvable']);
                exit;
            }
            
            $stmt = $pdo->prepare("
                UPDATE booking SET 
                    lieux = COALESCE(?, lieux), 
                    adresse = COALESCE(?, adresse), 
                    montant = COALESCE(?, montant), 
                    heure = COALESCE(?, heure), 
                    date = COALESCE(?, date), 
                    message = COALESCE(?, message),
                    updated_at = NOW()
                WHERE id = ? AND client_id = ?
            ");
            $ok = $stmt->execute([
                $data['lieux'] ?? null,
                $data['adresse'] ?? null,
                $data['montant'] ?? null,
                $data['heure'] ?? null,
                $data['date'] ?? null,
                $data['message'] ?? null,
                $id,
                $user_id
            ]);
            
            if ($ok) {
                echo json_encode(['success' => true, 'message' => 'Booking mis à jour']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la mise à jour']);
            }
            break;
            
        case 'DELETE':
            // DELETE /api/booking-client/{id} - Supprimer un booking
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            
            if (!$id) { 
                http_response_code(400); 
                echo json_encode(['error' => 'ID requis']); 
                exit; 
            }
            
            $stmt = $pdo->prepare("DELETE FROM booking WHERE id = ? AND client_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            
            if ($ok && $stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Booking supprimé']);
            } else {
                http_response_code(404); 
                echo json_encode(['error' => 'Booking introuvable']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion booking client : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
