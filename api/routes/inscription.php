<?php

require_once '../models/database.php';
require_once '../config/config.php'; 

// Vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Fonction pour valider les champs
function validate($field, $minLength = 1) {
    return isset($_POST[$field]) && strlen(trim($_POST[$field])) >= $minLength;
}

// Récupération des données du formulaire
$nomComplet = $_POST['nomComplet'] ?? '';
$nomArtiste = $_POST['nomArtiste'] ?? '';
$email = $_POST['email'] ?? null;
$whatsapp = $_POST['whatsapp'] ?? '';
$motdepasse = $_POST['motdepasse'] ?? '';
$confirmMotdepasse = $_POST['confirmMotdepasse'] ?? '';
$genres = $_POST['genres'] ?? [];

// Validation basique
if (!validate('nomComplet') || !validate('nomArtiste') || !validate('whatsapp') || !validate('motdepasse', 6)) {
    echo json_encode(['success' => false, 'error' => 'Champs obligatoires manquants ou mot de passe trop court']);
    exit;
}
if ($motdepasse !== $confirmMotdepasse) {
    echo json_encode(['success' => false, 'error' => 'Les mots de passe ne correspondent pas']);
    exit;
}

// Gestion de l'upload de la photo de profil
$photoPath = null;
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileTmp = $_FILES['profilePic']['tmp_name'];
    $fileName = uniqid('profile_') . '_' . basename($_FILES['profilePic']['name']);
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($fileTmp, $targetFile)) {
        $photoPath = 'uploads/' . $fileName;
    }
}

// Hash du mot de passe
$motdepasseHash = password_hash($motdepasse, PASSWORD_DEFAULT);

// Préparation des genres
$genresStr = is_array($genres) ? implode(',', $genres) : $genres;

// Insertion en base de données
try {
    $stmt = $pdo->prepare("INSERT INTO artistes (nom_complet, nom_artiste, email, whatsapp, motdepasse, genres, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $nomComplet,
        $nomArtiste,
        $email,
        $whatsapp,
        $motdepasseHash,
        $genresStr,
        $photoPath
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}

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