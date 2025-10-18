<?php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'config_email.php';

// Fonction pour envoyer un email via SMTP Brevo
function sendEmail($to, $subject, $message, $smtp_host, $smtp_port, $smtp_username, $smtp_password) {
    $headers = "From: $smtp_username\r\n";
    $headers .= "Reply-To: $smtp_username\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        return ['success' => true, 'message' => 'Email envoyé avec succès'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email'];
    }
}

// Endpoint GET /contacts_email : Récupérer les noms et adresses email
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'contacts_email') {
    try {
        $stmt = $pdo->query("SELECT name, email FROM artsite");
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($contacts);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des contacts : ' . $e->getMessage()]);
    }
}

// Endpoint POST /send_email : Envoyer des emails de bienvenue à tous les contacts
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_email') {
    try {
        $stmt = $pdo->query("SELECT name, email FROM artsite");
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results = [];
        foreach ($contacts as $contact) {
            $subject = "Bienvenue " . $contact['name'];
            $message = "Bonjour " . $contact['name'] . ",\n\nBienvenue sur notre plateforme !\n\nCordialement,\nL'équipe ArtSite";
            $response = sendEmail($contact['email'], $subject, $message, $smtp_host, $smtp_port, $smtp_username, $smtp_password);
            $results[] = [
                'name' => $contact['name'],
                'email' => $contact['email'],
                'response' => $response
            ];
        }
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de l\'envoi des emails : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Endpoint non trouvé']);
}
?>
