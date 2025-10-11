<?php
require_once __DIR__ . '/../utils/otp.php';
require_once __DIR__ . '/../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	sendResponse(['error' => 'Méthode non autorisée'], 405);
}

$post = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$ident = normalize_identifiant($post['identifiant'] ?? '');
$pwd = (string)($post['password'] ?? '');

$user = $ident ? find_user_by_identifiant($ident) : null;
if (!$user) sendResponse(['error' => 'Unauthorized'], 401);

// supporter transition: si l’ancien mot de passe était en clair, autoriser une seule fois et re-hasher
$ok = password_verify($pwd, $user['password']);
if (!$ok && hash_equals($user['password'], $pwd)) {
	$ok = true;
	$hash = password_hash($pwd, PASSWORD_BCRYPT);
	db()->prepare('UPDATE artiste SET password = ? WHERE id = ?')->execute([$hash, (int)$user['id']]);
	$user['password'] = $hash;
}
if (!$ok) sendResponse(['error' => 'Unauthorized'], 401);

$secret = getenv('JWT_SECRET') ?: 'change-me-please';
$access = make_access_token($user, $secret);
$refresh = make_refresh_token();

db()->prepare('INSERT INTO jwt_refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')->execute([
	(int)$user['id'],
	hash('sha256', $refresh),
	(new DateTimeImmutable('now'))->add(new DateInterval('P30D'))->format('Y-m-d H:i:s')
]);

sendResponse(['access_token' => $access, 'refresh_token' => $refresh]);