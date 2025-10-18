<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../utils/whatsapp.php';

// Helper to read JSON body
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Require JWT auth (artist) to use these endpoints
    $user_id = require_jwt_auth();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'contacts') {
                $stmt = $pdo->prepare("SELECT name, phone FROM artiste");
                $stmt->execute();
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $contacts, 'total' => count($contacts)]);
                exit;
            }
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint GET non trouvé']);
            break;

        case 'POST':
            if (isset($_POST['action']) && $_POST['action'] === 'send') {
                $stmt = $pdo->prepare("SELECT name, phone FROM artiste");
                $stmt->execute();
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $results = [];
                foreach ($contacts as $contact) {
                    $message = "Bonjour " . $contact['name'] . ", ceci est un message test.";
                    $response = sendWhatsAppMessage($contact['phone'], $message);
                    $results[] = [
                        'name' => $contact['name'],
                        'phone' => $contact['phone'],
                        'response' => $response
                    ];
                }
                echo json_encode(['success' => true, 'results' => $results]);
                exit;
            }
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint POST non trouvé']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }

} catch (Exception $e) {
    error_log('Erreur route whatsapp: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
