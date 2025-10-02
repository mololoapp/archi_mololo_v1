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

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            requireAuth();
            $stmt = $pdo->query("SELECT * FROM booking ORDER BY date DESC");
            $bookings = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $bookings]);
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
            if (empty($data['nom_utilisateur'])) $errors[] = 'Nom utilisateur requis';
            if (empty($data['lieux'])) $errors[] = 'Lieu requis';
            if (empty($data['date'])) $errors[] = 'Date requise';
            if (empty($data['heure'])) $errors[] = 'Heure requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO booking (nom_utilisateur, lieux, adresse, montant, heure, date, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['nom_utilisateur'],
                $data['lieux'],
                $data['adresse'] ?? '',
                $data['montant'] ?? '',
                $data['heure'],
                $data['date'],
                $data['message'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Demande de booking envoyée']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'envoi de la demande']);
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
