<?php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'config.php';

// Fonction pour envoyer un message WhatsApp via GET
function sendWhatsAppMessage($number, $message, $instance_id, $access_token) {
    $url = "https://wachap.app/api/send?number=" . urlencode($number) . "&type=text&message=" . urlencode($message) . "&instance_id=" . urlencode($instance_id) . "&access_token=" . urlencode($access_token);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Endpoint GET /contacts : Récupérer les noms et numéros de téléphone
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'contacts') {
    try {
        $stmt = $pdo->query("SELECT name, phone FROM artsite");
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($contacts);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération des contacts : ' . $e->getMessage()]);
    }
}

// Endpoint POST /send : Envoyer des messages WhatsApp à tous les contacts
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    global $whatsapp_instance_id, $whatsapp_access_token;

    try {
        $stmt = $pdo->query("SELECT name, phone FROM artsite");
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results = [];
        foreach ($contacts as $contact) {
            $message = "Bonjour " . $contact['name'] . ", ceci est un message test.";
            $response = sendWhatsAppMessage($contact['phone'], $message, $instance_id, $access_token);
            $results[] = [
                'name' => $contact['name'],
                'phone' => $contact['phone'],
                'response' => json_decode($response, true)
            ];
        }
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de l\'envoi des messages : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Endpoint non trouvé']);
}
?>
