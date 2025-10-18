<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';



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
            $stmt = $pdo->query("SELECT * FROM opportunite ORDER BY date DESC");
            $opportunities = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $opportunities]);
            break;
            
        case 'POST':
            requireAuth();
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Validation des champs requis
            $errors = [];
            if (empty($data['titre'])) $errors[] = 'Titre requis';
            if (empty($data['description'])) $errors[] = 'Description requise';
            if (empty($data['date'])) $errors[] = 'Date requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO opportunite (adresse, montant, date, titre, description) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['adresse'] ?? '',
                $data['montant'] ?? '',
                $data['date'],
                $data['titre'],
                $data['description']
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Opportunité créée']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la création de l\'opportunité']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion opportunités : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
