<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../utils/mailer.php';

// Fonction utilitaire pour récupérer les données JSON
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Vérifier l'authentification JWT (artiste)
    $user_id = require_jwt_auth();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // GET /api/contacts_email?action=contacts_email
            if (isset($_GET['action']) && $_GET['action'] === 'contacts_email') {
                $stmt = $pdo->prepare("SELECT name, email FROM artiste");
                // Si votre table s'appelle `artsite`, remplacez `artiste` par `artsite`.
                $stmt->execute();
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $contacts, 'total' => count($contacts)]);
                exit;
            }
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint GET non trouvé']);
            break;

        case 'POST':
            // POST /api/send_email?action=send_email
            if (isset($_POST['action']) && $_POST['action'] === 'send_email') {
                $stmt = $pdo->prepare("SELECT name, email FROM artiste");
                $stmt->execute();
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $results = [];
                foreach ($contacts as $contact) {
                    $subject = "Bienvenue " . $contact['name'];
                    $message = "Bonjour " . $contact['name'] . ",\n\nBienvenue sur notre plateforme !\n\nCordialement,\nL'équipe ArtSite";
                    $response = sendEmailSMTP($contact['email'], $subject, $message, $smtp_from, $smtp_host, $smtp_port, $smtp_username, $smtp_password);
                    $results[] = [
                        'name' => $contact['name'],
                        'email' => $contact['email'],
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
    error_log("Erreur gestion email: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}
