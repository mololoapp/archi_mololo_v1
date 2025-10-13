<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../utils/upload.php';


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

$user_id = require_jwt_auth();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    // $user_id déjà extrait via JWT
    
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
                $stmt = $pdo->prepare("SELECT * FROM epk WHERE id = ? AND user_id = ?");
                $stmt->execute([$epk_id, $user_id]);
                $epk = $stmt->fetch();
                
                if ($epk) {
                    echo json_encode(['success' => true, 'data' => $epk]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'EPK non trouvé']);
                }
            } else {
                // Récupérer tous les EPK
                $stmt = $pdo->prepare("SELECT * FROM epk WHERE user_id = ? ORDER BY date DESC");
                $stmt->execute([$user_id]);
                $epks = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $epks]);
            }
            break;
            
        case 'POST':
            $data = $_POST ?: getJsonInput() ?: [];
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Données JSON invalides']);
                exit;
            }
            
            // Upload optionnel de la photo
            $photoPath = $data['photo'] ?? '';
            if (!empty($_FILES['photo']) && is_array($_FILES['photo'])) {
                $res = move_uploaded_image($_FILES['photo'], __DIR__ . '/../../uploads');
                if ($res['ok']) $photoPath = $res['path'];
            }

            $stmt = $pdo->prepare("INSERT INTO epk (user_id, `Nom_d'artiste`, Genre_musical, localisation, Annees_dactivite, artiste_model, biographie, discographie, photo, videos, presse, fiche, conctact, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $user_id,
                $data['nom_artiste'] ?? '',
                $data['genre_musical'] ?? '',
                $data['localisation'] ?? '',
                $data['annees_activite'] ?? '',
                $data['artiste_model'] ?? '',
                $data['biographie'] ?? '',
                $data['discographie'] ?? '',
                $photoPath,
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

        case 'PUT':
            // PUT /api/epk/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $data = $_POST ?: getJsonInput() ?: [];
            $photoPath = $data['photo'] ?? null;
            if (!empty($_FILES['photo']) && is_array($_FILES['photo'])) {
                $res = move_uploaded_image($_FILES['photo'], __DIR__ . '/../../uploads');
                if ($res['ok']) $photoPath = $res['path'];
            }
            $stmt = $pdo->prepare("UPDATE epk SET `Nom_d'artiste` = COALESCE(?, `Nom_d'artiste`), Genre_musical = COALESCE(?, Genre_musical), localisation = COALESCE(?, localisation), Annees_dactivite = COALESCE(?, Annees_dactivite), artiste_model = COALESCE(?, artiste_model), biographie = COALESCE(?, biographie), discographie = COALESCE(?, discographie), photo = COALESCE(?, photo), videos = COALESCE(?, videos), presse = COALESCE(?, presse), fiche = COALESCE(?, fiche), conctact = COALESCE(?, conctact) WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([
                $data['nom_artiste'] ?? null,
                $data['genre_musical'] ?? null,
                $data['localisation'] ?? null,
                $data['annees_activite'] ?? null,
                $data['artiste_model'] ?? null,
                $data['biographie'] ?? null,
                $data['discographie'] ?? null,
                $photoPath,
                $data['videos'] ?? null,
                $data['presse'] ?? null,
                $data['fiche'] ?? null,
                $data['contact'] ?? null,
                $id,
                $user_id
            ]);
            if ($ok) echo json_encode(['success' => true, 'message' => 'EPK mis à jour']);
            else { http_response_code(404); echo json_encode(['error' => 'EPK introuvable']); }
            break;

        case 'DELETE':
            // DELETE /api/epk/{id}
            $request_uri = $_SERVER['REQUEST_URI'];
            $uri = parse_url($request_uri, PHP_URL_PATH);
            $uri = str_replace('/api', '', $uri);
            $uri = trim($uri, '/');
            $parts = explode('/', $uri);
            $id = $parts[2] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requis']); exit; }
            $stmt = $pdo->prepare("DELETE FROM epk WHERE id = ? AND user_id = ?");
            $ok = $stmt->execute([$id, $user_id]);
            if ($ok && $stmt->rowCount() > 0) echo json_encode(['success' => true, 'message' => 'EPK supprimé']);
            else { http_response_code(404); echo json_encode(['error' => 'EPK introuvable']); }
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
