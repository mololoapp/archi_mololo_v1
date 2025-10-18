<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../utils/pdf.php';
require_once __DIR__ . '/../utils/jwt.php';

// helper to read JSON body
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

try {
    // Require authentication (artists) — if you want public generation, remove this
    $user_id = require_jwt_auth();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $url = $_GET['url'] ?? null;
            if (!$url) {
                http_response_code(400);
                echo json_encode(['error' => 'Paramètre url requis']);
                exit;
            }
            $path = pdfshift($url);
            if ($path) {
                echo json_encode(['success' => true, 'file' => $path]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Erreur génération PDF']);
            }
            break;

        case 'POST':
            $data = getJsonInput();
            if (!$data || empty($data['url'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Corps JSON invalide ou url manquante']);
                exit;
            }
            $path = pdfshift($data['url']);
            if ($path) {
                echo json_encode(['success' => true, 'file' => $path]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Erreur génération PDF']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }

} catch (Exception $e) {
    error_log('Erreur route pdf: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
