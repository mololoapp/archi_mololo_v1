<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';

// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupération des données
    $identifiant = trim($_POST['identifiant'] ?? trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? trim($_POST['mot_de_passe'] ?? ''));
    
    // Validation
    $errors = [];
    if (empty($identifiant)) $errors[] = 'Email ou numéro requis';
    if (empty($password)) $errors[] = 'Mot de passe requis';
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        exit;
    }
    
    // Requête pour trouver l'utilisateur par email ou numéro
    $sql = "SELECT id, nom, nom_artiste, email, numero, style_musique, password, date_inscription 
            FROM artiste WHERE (email = ? OR numero = ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Démarrer la session utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nom_artiste'];
        
        // On ne retourne pas le mot de passe
        unset($user['password']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Identifiants invalides']);
    }
    
} catch (Exception $e) {
    error_log("Erreur connexion : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?>