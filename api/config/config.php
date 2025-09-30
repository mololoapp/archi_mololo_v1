<?php

//configuration de api rest full securisé 
// Autoriser l'accès depuis n'importe quelle origine (à adapter selon ton besoin)
header("Access-Control-Allow-Origin: *");

// Autoriser les méthodes HTTP spécifiques
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Autoriser certains en-têtes personnalisés
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Définir le type de contenu en JSON
header("Content-Type: application/json; charset=UTF-8");


// 🔐 En-têtes de sécurité
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
// header("Referrer-Policy: no-referrer");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");


// 📦 Type de contenu
header("Content-Type: application/json; charset=UTF-8");

// ⚙️ Gestion des requêtes OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🧪 Exemple de réponse JSON
$response = [
    "status" => "success",
    "message" => "API sécurisée prête à recevoir des requêtes."
];

echo json_encode($response);

// fin de la ligne de code de réponse JSON et mon code api configuraion 
?>
