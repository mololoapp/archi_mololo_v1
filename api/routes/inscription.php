<?php


// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Validation des champs
$nom_complet = trim($_POST['nom_complet'] ?? '');
$nom_artiste = trim($_POST['nom_artiste'] ?? '');
$email = trim($_POST['email'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$genres = $_POST['genres'] ?? [];
$photo = $_FILES['photo'] ?? null;

// Vérifications
$errors = [];
if (empty($nom_complet)) $errors[] = 'Nom complet requis';
if (empty($nom_artiste)) $errors[] = 'Nom d’artiste requis';
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
if (!preg_match('/^\+?[0-9]{7,15}$/', $whatsapp)) $errors[] = 'Numéro WhatsApp invalide';
if (!is_array($genres)) $errors[] = 'Genres musicaux invalides';

if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    if (!in_array($photo['type'], $allowed_types)) {
        $errors[] = 'Format de photo non autorisé';
    }
    if ($photo['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Photo trop volumineuse (max 5MB)';
    }
} else {
    $errors[] = 'Photo de profil requise';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Sauvegarde de la photo
$filename = uniqid() . '_' . basename($photo['name']);
$target_path = 'uploads/' . $filename;
move_uploaded_file($photo['tmp_name'], $target_path);

// Insertion sécurisée
$sql = "INSERT INTO artistes (nom_complet, nom_artiste, email, whatsapp, genres, photo_path)
        VALUES (:nom_complet, :nom_artiste, :email, :whatsapp, :genres, :photo_path)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nom_complet' => $nom_complet,
    ':nom_artiste' => $nom_artiste,
    ':email' => $email ?: null,
    ':whatsapp' => $whatsapp,
    ':genres' => implode(',', $genres),
    ':photo_path' => $target_path
]);

echo json_encode(['success' => true, 'message' => 'Compte créé avec succès']);
?>