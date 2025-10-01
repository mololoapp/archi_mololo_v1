<?php

require_once '../models/database.php'; 
require_once '../config/config.php'; 

// Gestion CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Réponse aux requêtes OPTIONS (préflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Routage simple pour l'API
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Normalisation de l'URL (enlève les paramètres GET)
$path = parse_url($request, PHP_URL_PATH);

switch (true) {
    case preg_match('#^/api/inscription/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/inscription.php';
        break;
    case preg_match('#^/api/login/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/login.php';
        break;
    case preg_match('#^/api/session/?$#', $path) && $method === 'GET':
        require __DIR__ . '/routes/session.php';
        break;
    case preg_match('#^/api/send_reset_email/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/send_reset_email.php';
        break;
    case preg_match('#^/api/verify_otp/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/verify_otp.php';
        break;
    case preg_match('#^/api/reset_password/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/reset_password.php';
        break;
    case preg_match('#^/api/logout/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/logout.php';
        break;
    case preg_match('#^/api/epk/?$#', $path) && $method === 'GET':
        require __DIR__ . '/routes/epk.php';
        break;
    case preg_match('#^/api/epk_update/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/epk_update.php';
        break;
    case preg_match('#^/api/epk_pdf/?$#', $path) && $method === 'GET':
        require __DIR__ . '/routes/epk_pdf.php';
        break;
    case preg_match('#^/api/profile_update/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/profile_update.php';
        break;
    case preg_match('#^/api/dashboard/?$#', $path) && $method === 'GET':
        require __DIR__ . '/routes/dashboard.php';
        break;
    case preg_match('#^/api/profile/?$#', $path) && $method === 'GET':
        require __DIR__ . '/routes/profile.php';
        break;
    case preg_match('#^/api/update_profile_photo/?$#', $path) && $method === 'POST':
        require __DIR__ . '/routes/update_profile_photo.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route non trouvée']);
        break;
} 