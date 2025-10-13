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
            $stmt = $pdo->prepare("SELECT * FROM agenda WHERE user_id = ? ORDER BY date ASC");
            $stmt->execute([$user_id]);
            $events = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $events]);
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
            if (empty($data['nom_concert'])) $errors[] = 'Nom du concert requis';
            if (empty($data['date'])) $errors[] = 'Date requise';
            if (empty($data['heure'])) $errors[] = 'Heure requise';
            if (empty($data['adresse'])) $errors[] = 'Adresse requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO agenda (user_id, date, nom_concert, adresse, heure, description, montant, nombre_personne, mise_jour) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $user_id,
                $data['date'],
                $data['nom_concert'],
                $data['adresse'],
                $data['heure'],
                $data['description'] ?? '',
                $data['montant'] ?? '',
                $data['nombre_personne'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Événement ajouté à l\'agenda']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'ajout de l\'événement']);
            }
            break;
        
        case 'PUT':
            // PUT /api/agenda/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $data = getJsonInput();
            if (!$data) { http_response_code(400); echo json_encode(['error' => 'Données JSON invalides']); exit; }
            $stmt = $pdo->prepare("UPDATE agenda SET date = COALESCE(?, date), nom_concert = COALESCE(?, nom_concert), adresse = COALESCE(?, adresse), heure = COALESCE(?, heure), description = COALESCE(?, description), montant = COALESCE(?, montant), nombre_personne = COALESCE(?, nombre_personne), mise_jour = NOW() WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['date'] ?? null,
                $data['nom_concert'] ?? null,
                $data['adresse'] ?? null,
                $data['heure'] ?? null,
                $data['description'] ?? null,
                $data['montant'] ?? null,
                $data['nombre_personne'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'Événement mis à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'Événement introuvable']); }
            break;

        case 'DELETE':
            // DELETE /api/agenda/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'Événement supprimé']);
            else { http_response_code(404); echo json_encode(['error' => 'Événement introuvable']); }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion agenda : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
