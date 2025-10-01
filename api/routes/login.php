<?php

require_once '../models/database.php';
require_once '../config/config.php';

// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;

// ...existing code...
$identifiant = trim($_POST['identifiant'] ?? '');
$mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

// Validation

$errors = [];
if (empty($identifiant)) $errors[] = 'Email ou numéro WhatsApp requis';
if (empty($mot_de_passe)) $errors[] = 'Mot de passe requis';


// Récupération des données
$identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($identifier === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Veuillez remplir tous les champs.'
    ]);
    exit;
}

try {
    $sql = "SELECT * FROM artistes WHERE email = :identifier OR whatsapp = :identifier LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['identifier' => $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Si le mot de passe est hashé, utiliser password_verify
        if (password_verify($password, $user['motdepasse'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie !'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Mot de passe incorrect.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Utilisateur non trouvé.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur : ' . $e->getMessage()
    ]);
}

}