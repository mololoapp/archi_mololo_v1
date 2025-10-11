<?php
require_once __DIR__ . '/../models/database.php';
function normalize_identifiant(string $s): string {
	$s = trim($s);
	// email lower, tel sans espaces
	if (filter_var($s, FILTER_VALIDATE_EMAIL)) return strtolower($s);
	return preg_replace('/\s+/', '', $s);
}

function random_otp_6(): string {
	return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sha256_hex(string $s): string {
	return hash('sha256', $s);
}

function db() {
	$database = new Database();
    $pdo = $database->getConnection();
	return $pdo;
}

function find_user_by_identifiant(string $ident): ?array {
	$pdo = db();
	if (filter_var($ident, FILTER_VALIDATE_EMAIL)) {
		$st = $pdo->prepare('SELECT * FROM artiste WHERE email = ? LIMIT 1');
		$st->execute([$ident]);
	} else {
		$st = $pdo->prepare('SELECT * FROM artiste WHERE numero = ? LIMIT 1');
		$st->execute([$ident]);
	}
	$row = $st->fetch();
	return $row ?: null;
}

function insert_password_reset(int $user_id, string $channel, string $salt_bin, string $otp_hash_hex, DateTimeInterface $expires_at): void {
	$pdo = db();
	$st = $pdo->prepare('INSERT INTO password_resets (user_id, channel, salt, otp_hash, otp_expires_at) VALUES (?, ?, ?, ?, ?)');
	$st->execute([$user_id, $channel, $salt_bin, $otp_hash_hex, $expires_at->format('Y-m-d H:i:s')]);
}

function latest_active_reset(int $user_id): ?array {
	$pdo = db();
	$st = $pdo->prepare('SELECT * FROM password_resets WHERE user_id = ? AND consumed = 0 ORDER BY id DESC LIMIT 1');
	$st->execute([$user_id]);
	$row = $st->fetch();
	if (!$row) return null;
	if (new DateTimeImmutable($row['otp_expires_at']) < new DateTimeImmutable('now')) return null;
	return $row;
}

function increment_attempts(int $id): void {
	$pdo = db();
	$pdo->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?')->execute([$id]);
}

function mark_consumed(int $id): void {
	$pdo = db();
	$pdo->prepare('UPDATE password_resets SET consumed = 1 WHERE id = ?')->execute([$id]);
}

function is_strong_password(string $pwd): bool {
	return strlen($pwd) >= 8;
}