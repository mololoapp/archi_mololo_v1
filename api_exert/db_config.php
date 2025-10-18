<?php
// Configuration de la base de données
$host = 'localhost'; // Remplacez par votre hôte
$dbname = 'your_db'; // Remplacez par le nom de votre base de données
$username = 'your_username'; // Remplacez par votre nom d'utilisateur
$password = 'your_password'; // Remplacez par votre mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
