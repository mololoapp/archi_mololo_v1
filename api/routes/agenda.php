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
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM agenda ORDER BY date ASC");
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
            
            $stmt = $pdo->prepare("INSERT INTO agenda (date, nom_concert, adresse, heure, description, montant, nombre_personne, mise_jour) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
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
