<?php
header('Content-Type: application/json');
require 'config.php';

// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données

// On récupère l'identifiant (email ou whatsapp) et le mot de passe
$identifiant = trim($_POST['identifiant'] ?? '');
$mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

// Validation

$errors = [];
if (empty($identifiant)) $errors[] = 'Email ou numéro WhatsApp requis';
if (empty($mot_de_passe)) $errors[] = 'Mot de passe requis';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}


// Requête pour trouver l'utilisateur par email ou whatsapp
$sql = "SELECT id, nom_complet, nom_artiste, email, whatsapp, genres, photo_path, mot_de_passe FROM artistes
        WHERE (email = :identifiant OR whatsapp = :identifiant)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':identifiant' => $identifiant]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['mot_de_passe'] === $mot_de_passe) {
    // On ne retourne pas le mot de passe
    unset($user['mot_de_passe']);
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => $user
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Identifiants invalides']);
}
?>