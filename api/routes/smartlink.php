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
            $stmt = $pdo->query("SELECT * FROM smartlink");
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
            
            $stmt = $pdo->prepare("INSERT INTO smartlink (smartlink, smartlink_whatsapp, smartlink_email) VALUES (?, ?, ?)");
            $result = $stmt->execute([
                $data['smartlink'],
                $data['smartlink_whatsapp'] ?? '',
                $data['smartlink_email'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'SmartLink créé']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la création du SmartLink']);
            }
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
