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
    $user_id = $_SESSION['user_id'];
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Récupérer l'ID EPK si fourni
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $uri_segments = explode('/', $uri);
            $epk_id = $uri_segments[2] ?? null;
            
            
            if ($epk_id) {
                // Récupérer un EPK spécifique
                $stmt = $pdo->prepare("SELECT * FROM epk WHERE id = ?");
                $stmt->execute([$epk_id]);
                $epk = $stmt->fetch();
                
                if ($epk) {
                    echo json_encode(['success' => true, 'data' => $epk]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'EPK non trouvé']);
                }
            } else {
                // Récupérer tous les EPK
                $stmt = $pdo->query("SELECT * FROM epk ORDER BY date DESC");
                $epks = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $epks]);
            }
            break;
            
        case 'POST':
            $data = getJsonInput();
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO epk (user_id, `Nom_d'artiste`, Genre_musical, localisation, Annees_dactivite, artiste_model, biographie, discographie, photo, videos, presse, fiche, conctact, date) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $user_id,
                $data['nom_artiste'] ?? '',
                $data['genre_musical'] ?? '',
                $data['localisation'] ?? '',
                $data['annees_activite'] ?? '',
                $data['artiste_model'] ?? '',
                $data['biographie'] ?? '',
                $data['discographie'] ?? '',
                $data['photo'] ?? '',
                $data['videos'] ?? '',
                $data['presse'] ?? '',
                $data['fiche'] ?? '',
                $data['contact'] ?? ''
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'EPK créé', 
                    'id' => $pdo->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la création de l\'EPK']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    error_log("Erreur gestion EPK : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur' . $e->getMessage()
    ]);
}
?>
