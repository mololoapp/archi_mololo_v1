<?php
require_once __DIR__ . '/../utils/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	sendResponse(['error' => 'Méthode non autorisée'], 405);
}

$post = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$rt = (string)($post['refresh_token'] ?? '');
if ($rt === '') sendResponse(['ok' => true]);

$hash = hash('sha256', $rt);
db()->prepare('UPDATE jwt_refresh_tokens_user SET revoked = 1 WHERE token_hash = ?')->execute([$hash]);

sendResponse(['ok' => true]);