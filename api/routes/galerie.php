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
            $stmt = $pdo->query("SELECT * FROM galerie ORDER BY date DESC");
            $gallery = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $gallery]);
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
            if (empty($data['titre']) && empty($data['tire'])) $errors[] = 'Titre requis';
            if (empty($data['image']) && empty($data['video'])) $errors[] = 'Image ou vidéo requise';
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['errors' => $errors]);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO galerie (image, video, tire, favorie, description, details, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['image'] ?? '',
                $data['video'] ?? '',
                $data['titre'] ?? $data['tire'] ?? '',
                $data['favorie'] ?? '',
                $data['description'] ?? '',
                $data['details'] ?? '',
                $data['date'] ?? date('Y-m-d H:i:s')
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Élément ajouté à la galerie']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'ajout à la galerie']);
            }
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
