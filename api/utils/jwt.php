<?php
require_once __DIR__ . '/../models/database.php';
function b64url($s) {
	return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function b64url_json($arr) {
	return b64url(json_encode($arr, JSON_UNESCAPED_SLASHES));
}

function hmac_sha256_raw($data, $key) {
	return hash_hmac('sha256', $data, $key, true);
}

function jwt_sign_hs256(array $payload, string $secret): string {
	$header = ['alg' => 'HS256', 'typ' => 'JWT'];
	$h = b64url_json($header);
	$p = b64url_json($payload);
	$sig = b64url(hmac_sha256_raw("$h.$p", $secret));
	return "$h.$p.$sig";
}

function jwt_verify_hs256(string $jwt, string $secret): ?array {
	$parts = explode('.', $jwt);
	if (count($parts) !== 3) return null;
	[$h, $p, $s] = $parts;
	$calc = b64url(hmac_sha256_raw("$h.$p", $secret));
	if (!hash_equals($calc, $s)) return null;
	$payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);
	if (!$payload) return null;
	if (isset($payload['exp']) && time() > (int)$payload['exp']) return null;
	return $payload;
}

function make_access_token(array $user, string $secret, int $ttlSeconds = 60 * 24 * 60 * 60): string {
    // ttlSeconds par défaut: 60 jours
    $ver = substr(sha1($user['password'] ?? ''), 0, 10);
    $now = time();
    $payload = [
        'sub' => (int)$user['id'],
        'iat' => $now,
        'exp' => $now + $ttlSeconds,
        'ver' => $ver
    ];
    return jwt_sign_hs256($payload, $secret);
}

function make_refresh_token(): string {
	return bin2hex(random_bytes(32)); // opaque
}

function get_bearer_token(): ?string {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? '';
    if (!$hdr && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $hdr = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
    }
    if (!preg_match('/^Bearer\s+(.*)$/i', $hdr, $m)) return null;
    return trim($m[1]);
}

function require_jwt_auth(): int {
    $token = get_bearer_token();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Missing bearer token']);
        exit();
    }
    $secret = getenv('JWT_SECRET') ?: 'change-me-please';
    $payload = jwt_verify_hs256($token, $secret);
    if (!$payload || !isset($payload['sub'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit();
    }
    return (int)$payload['sub'];
}