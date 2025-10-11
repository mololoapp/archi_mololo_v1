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
    
    // Récupérer l'ID de l'artiste depuis l'URI
    $request_uri = $_SERVER['REQUEST_URI'];
    $uri = parse_url($request_uri, PHP_URL_PATH);
    $uri = str_replace('/api', '', $uri);
    $uri = trim($uri, '/');
    $uri_segments = explode('/', $uri);
    $artiste_id = $uri_segments[2] ?? null;
    
    if (!$artiste_id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID artiste requis']);
        exit;
    }
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Récupérer un artiste spécifique
            $stmt = $pdo->prepare("SELECT * FROM artiste WHERE id = ?");
            $stmt->execute([$artiste_id]);
            $artiste = $stmt->fetch();
            
            if ($artiste) {
                unset($artiste['password']); // Ne pas retourner le mot de passe
                echo json_encode(['success' => true, 'data' => $artiste]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Artiste non trouvé']);
            }
            break;
            
        case 'PUT':
            requireAuth();
            // Mettre à jour un artiste
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE artiste SET nom = ?, nom_artiste = ?, email = ?, numero = ?, style_musique = ? WHERE id = ?");
            $result = $stmt->execute([
                $data['nom'] ?? '',
                $data['nom_artiste'] ?? '',
                $data['email'] ?? '',
                $data['numero'] ?? '',
                $data['style_musique'] ?? '',
                $artiste_id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artiste mis à jour']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la mise à jour']);
            }
            break;
            
        case 'DELETE':
            requireAuth();
            // Supprimer un artiste
            $stmt = $pdo->prepare("DELETE FROM artiste WHERE id = ?");
            $result = $stmt->execute([$artiste_id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artiste supprimé']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la suppression']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion artiste : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
?>
