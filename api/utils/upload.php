<?php
// Utilitaire d'upload d'images sécurisé (JPEG/PNG/WebP) avec taille max

function ensure_upload_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function is_allowed_image(string $mime): bool {
    return in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
}

function move_uploaded_image(array $file, string $targetDir = __DIR__ . '/../../uploads', int $maxBytes = 5 * 1024 * 1024): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['ok' => false, 'error' => 'invalid_upload'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'upload_error_' . $file['error']];
    }

    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'file_too_large'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!is_allowed_image($mime)) {
        return ['ok' => false, 'error' => 'unsupported_type'];
    }

    ensure_upload_dir($targetDir);
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'bin',
    };
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['ok' => false, 'error' => 'move_failed'];
    }

    // Retourner un chemin relatif depuis /api pour servir statiquement si configuré
    return ['ok' => true, 'path' => 'uploads/' . $name];
}
?>

