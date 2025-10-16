<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';

// Vérifier méthode GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Récupérer la liste des artistes
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $artistes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $artistes
    ]);
    
} catch (Exception $e) {
    error_log("Erreur récupération artistes : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des artistes'
    ]);
}
?>
