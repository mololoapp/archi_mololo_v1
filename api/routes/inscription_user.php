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
    
    // Validation des champs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Vérifications
    $errors = [];
    if (empty($nom)) $errors[] = 'Nom requis';
    if (empty($prenom)) $errors[] = 'Prenom requis';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email valide requis';
    if (empty($numero) || !preg_match('/^\+?[0-9]{7,15}$/', $numero)) $errors[] = 'Numéro valide requis';
    if (empty($password) || strlen($password) < 6) $errors[] = 'Mot de passe requis (min 6 caractères)';
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email déjà utilisé';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        exit;
    }
    
    // Hasher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertion sécurisée
    $sql = "INSERT INTO users (nom, prenom, email, numero , password, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $nom,
        $prenom, 
        $email,
        $numero,
        $hashed_password
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Compte créé avec succès',
            'user_id' => $pdo->lastInsertId()
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la création du compte']);
    }
    
} catch (Exception $e) {
    error_log("Erreur inscription : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur' . $e->getMessage()]);
}
?>