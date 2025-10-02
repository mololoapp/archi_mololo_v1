<?php
header('Content-Type: application/json');

// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

try {
    // Détruire la session
    session_destroy();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Déconnexion réussie'
    ]);
    
} catch (Exception $e) {
    error_log("Erreur déconnexion : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la déconnexion'
    ]);
}
?>
