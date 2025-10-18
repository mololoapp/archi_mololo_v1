<?php
require_once __DIR__ . '/../utils/otp.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../utils/mailer.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($script_dir !== '' && $script_dir !== '/' && strpos($uri, $script_dir) === 0) {
	$uri = substr($uri, strlen($script_dir));
}
$uri = ltrim($uri, '/');
$parts = $uri ? explode('/', $uri) : [];
$action = $parts[1] ?? null; // après "password"
$step = $parts[2] ?? null;   // "request" | "verify" | null

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	sendResponse(['error' => 'Méthode non autorisée'], 405);
}

$post = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];

switch ($action) {
	case 'forgot':
		if ($step === 'request') {
			$ident = normalize_identifiant($post['identifiant'] ?? '');
			if ($ident === '') sendResponse(['ok' => true]); // générique
			$user = find_user_by_identifiant($ident);
			// réponse générique pour ne pas divulguer
			if ($user) {
				$otp = random_otp_6();
				$salt = random_bytes(16);
				$otp_hash = sha256_hex($otp . bin2hex($salt));
				$channel = filter_var($ident, FILTER_VALIDATE_EMAIL) ? 'email' : 'sms';
				$exp = (new DateTimeImmutable('now'))->add(new DateInterval('PT10M'));
				insert_password_reset((int)$user['id'], $channel, $salt, $otp_hash, $exp);
				// Enregistrement OK — on envoie le code par email si le canal est email
				error_log("[OTP] user_id={$user['id']} via {$channel} code=$otp"); // log pour debug
				if ($channel === 'email' && !empty($user['email'])) {
					$to = $user['email'];
					$subject = 'Code de réinitialisation MoloLo+';
					$message = "Bonjour " . ($user['nom'] ?? '') . ",\n\n" .
					    "Voici votre code de réinitialisation : $otp\n" .
					    "Il expirera dans 10 minutes. Si vous n'avez pas demandé ce code, ignorez cet email.\n\n" .
					    "Cordialement,\nL'équipe MoloLo+";

					$mailResp = sendEmailSMTP($to, $subject, $message, $smtp_from, $smtp_host, $smtp_port, $smtp_username, $smtp_password);
					if (!empty($mailResp) && empty($mailResp['success'])) {
						error_log('Erreur envoi OTP email pour user_id=' . $user['id'] . ' : ' . ($mailResp['message'] ?? 'unknown'));
					}
				}
			}
			sendResponse(['ok' => true]);
		}
		if ($step === 'verify') {
			$ident = normalize_identifiant($post['identifiant'] ?? '');
			$otp_code = (string)($post['otp_code'] ?? '');
			$user = $ident ? find_user_by_identifiant($ident) : null;
			if (!$user) sendResponse(['valid' => false]);
			$pr = latest_active_reset((int)$user['id']);
			if (!$pr || (int)$pr['attempts'] >= 5) sendResponse(['valid' => false]);
			$calc = sha256_hex($otp_code . bin2hex($pr['salt']));
			if (!hash_equals($pr['otp_hash'], $calc)) {
				increment_attempts((int)$pr['id']);
				sendResponse(['valid' => false]);
			}
			sendResponse(['valid' => true]);
		}
		break;

	case 'reset':
		$ident = normalize_identifiant($post['identifiant'] ?? '');
		$otp_code = (string)($post['otp_code'] ?? '');
		$new_password = (string)($post['new_password'] ?? '');
		if (!is_strong_password($new_password)) sendResponse(['ok' => false, 'error' => 'password_weak'], 400);

		$user = $ident ? find_user_by_identifiant($ident) : null;
		if (!$user) sendResponse(['ok' => true]); // générique

		$pr = latest_active_reset((int)$user['id']);
		if (!$pr || (int)$pr['attempts'] >= 5) sendResponse(['ok' => false], 400);

		$calc = sha256_hex($otp_code . bin2hex($pr['salt']));
		if (!hash_equals($pr['otp_hash'], $calc)) {
			increment_attempts((int)$pr['id']);
			sendResponse(['ok' => false], 400);
		}

		$hash = password_hash($new_password, PASSWORD_BCRYPT);
		$pdo->prepare('UPDATE artiste SET password = ? WHERE id = ?')->execute([$hash, (int)$user['id']]);
		mark_consumed((int)$pr['id']);

		// Révoquer tous les refresh tokens
		$pdo->prepare('UPDATE jwt_refresh_tokens SET revoked = 1 WHERE user_id = ?')->execute([(int)$user['id']]);

		sendResponse(['ok' => true]);
		break;

	default:
		sendResponse(['error' => 'Endpoint non trouvé'], 404);
}