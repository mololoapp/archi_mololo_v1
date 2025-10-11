<?php
require_once __DIR__ . '/../utils/otp.php';
require_once __DIR__ . '/../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	sendResponse(['error' => 'Méthode non autorisée'], 405);
}

$post = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];
$rt = (string)($post['refresh_token'] ?? '');
if ($rt === '') sendResponse(['error' => 'Unauthorized'], 401);

$hash = hash('sha256', $rt);
$pdo = db();
$st = $pdo->prepare('SELECT * FROM jwt_refresh_tokens WHERE token_hash = ? LIMIT 1');
$st->execute([$hash]);
$row = $st->fetch();

if (!$row || (int)$row['revoked'] === 1 || new DateTimeImmutable($row['expires_at']) < new DateTimeImmutable('now')) {
	sendResponse(['error' => 'Unauthorized'], 401);
}

$user_st = $pdo->prepare('SELECT * FROM artiste WHERE id = ? LIMIT 1');
$user_st->execute([(int)$row['user_id']]);
$user = $user_st->fetch();
if (!$user) sendResponse(['error' => 'Unauthorized'], 401);

// rotation
$pdo->prepare('UPDATE jwt_refresh_tokens SET revoked = 1 WHERE id = ?')->execute([(int)$row['id']]);

$new_refresh = make_refresh_token();
$pdo->prepare('INSERT INTO jwt_refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')->execute([
	(int)$user['id'],
	hash('sha256', $new_refresh),
	(new DateTimeImmutable('now'))->add(new DateInterval('P30D'))->format('Y-m-d H:i:s')
]);

$secret = getenv('JWT_SECRET') ?: 'change-me-please';
$access = make_access_token($user, $secret);

sendResponse(['access_token' => $access, 'refresh_token' => $new_refresh]);